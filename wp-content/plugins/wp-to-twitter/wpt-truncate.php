<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

function jd_truncate_tweet( $tweet, $post, $post_ID, $retweet = false, $ref = false ) {
	// media file occupies 22 characters, need to account for in shortening.
	$maxlength    = apply_filters( 'wpt_max_tweet_length', array( 'with_media' => 116, 'without_media' => 139 ) );
	$tweet_length = ( wpt_post_with_media( $post_ID ) ) ? $maxlength['with_media'] : $maxlength['without_media'];
	$tweet        = apply_filters( 'wpt_tweet_sentence', $tweet, $post_ID );
	$tweet        = trim( wpt_custom_shortcodes( $tweet, $post_ID ) );
	$shrink       = ( $post['shortUrl'] != '' ) ? $post['shortUrl'] : apply_filters( 'wptt_shorten_link', $post['postLink'], $post['postTitle'], $post_ID, false );
	// generate all template variable values
	$auth         = $post['authId'];
	$title        = trim( apply_filters( 'wpt_status', $post['postTitle'], $post_ID, 'title' ) );
	$blogname     = trim( $post['blogTitle'] );
	$excerpt      = trim( apply_filters( 'wpt_status', $post['postExcerpt'], $post_ID, 'post' ) );
	$thisposturl  = trim( $shrink );
	$category     = trim( $post['category'] );
	$cat_desc     = trim( $post['cat_desc'] );
	$user_account = get_user_meta( $auth, 'wtt_twitter_username', true );
	$tags         = wpt_generate_hash_tags( $post_ID );
	$account      = get_option( 'wtt_twitter_username' );
	$date         = trim( $post['postDate'] );
	$modified     = trim( $post['postModified'] );
	if ( get_option( 'jd_individual_twitter_users' ) == 1 ) {
		if ( $user_account == '' ) {
			if ( get_user_meta( $auth, 'wp-to-twitter-enable-user', true ) == 'mainAtTwitter' ) {
				$account = $user_account = stripcslashes( get_user_meta( $auth, 'wp-to-twitter-user-username', true ) );
			} else if ( get_user_meta( $auth, 'wp-to-twitter-enable-user', true ) == 'mainAtTwitterPlus' ) {
				$account = $user_account = stripcslashes( get_user_meta( $auth, 'wp-to-twitter-user-username', true ) . ' @' . get_option( 'wtt_twitter_username' ) );
			}
		} else {
			$account = "$user_account";
		}
	}
	$display_name = get_the_author_meta( 'display_name', $auth );
	// value of #author#
	$author = ( $user_account != '' ) ? "@$user_account" : $display_name;
	// value of #account# 
	$account = ( $account != '' ) ? "@$account" : '';
	// value of #@# 
	$uaccount = ( $user_account != '' ) ? "@$user_account" : "$account";
	// clean up data if extra @ included //
	$account  = str_ireplace( '@@', '@', $account );
	$uaccount = str_ireplace( '@@', '@', $uaccount );
	$author   = str_ireplace( '@@', '@', $author );

	if ( get_user_meta( $auth, 'wpt-remove', true ) == 'on' ) {
		$account = '';
	}
	if ( get_option( 'jd_twit_prepend' ) != "" && $tweet != '' ) {
		$tweet = stripslashes( get_option( 'jd_twit_prepend' ) ) . " " . $tweet;
	}
	if ( get_option( 'jd_twit_append' ) != "" && $tweet != '' ) {
		$tweet = $tweet . " " . stripslashes( get_option( 'jd_twit_append' ) );
	}
	$encoding = get_option( 'blog_charset' );
	if ( $encoding == '' ) {
		$encoding = 'UTF-8';
	}

	$has_excerpt_tag = ( strpos( $tweet, '#post#' ) === false ) ? false : true;
	$has_title_tag   = ( strpos( $tweet, '#title#' ) === false ) ? false : true;

	if ( strpos( $tweet, '#url#' ) === false
	     && strpos( $tweet, '#title#' ) === false
	     && strpos( $tweet, '#blog#' ) === false
	     && strpos( $tweet, '#post#' ) === false
	     && strpos( $tweet, '#category#' ) === false
	     && strpos( $tweet, '#date#' ) === false
	     && strpos( $tweet, '#author#' ) === false
	     && strpos( $tweet, '#displayname#' ) === false
	     && strpos( $tweet, '#tags#' ) === false
	     && strpos( $tweet, '#modified#' ) === false
	     && strpos( $tweet, '#reference#' ) === false
	     && strpos( $tweet, '#account#' ) === false
	     && strpos( $tweet, '#@#' ) === false
	     && strpos( $tweet, '#cat_desc#' ) === false
		 && strpos( $tweet, '#longurl#' ) === false
	) {
		// there are no tags in this Tweet. Truncate and return.
		$post_tweet = mb_substr( $tweet, 0, $tweet_length, $encoding );

		return $post_tweet;
	}
	if ( function_exists( 'wpt_pro_exists' ) && wpt_pro_exists() == true ) {
		$reference = ( $ref ) ? $account : '@' . get_option( 'wtt_twitter_username' );
	} else {
		$reference = '';
	}
	// create full unconditional post tweet - prior to truncation
	$replace    = ( function_exists( 'wpt_pro_exists' ) && wpt_pro_exists() == true ) ? $reference : '';
	$search     = array(
		'#account#',
		'#@#',
		'#reference#',
		'#url#',
		'#title#',
		'#blog#',
		'#post#',
		'#category#',
		'#cat_desc#',
		'#date#',
		'#author#',
		'#displayname#',
		'#tags#',
		'#modified#',
		'#longurl#'
	);
	$replace    = array(
		$account,
		$uaccount,
		$replace,
		$thisposturl,
		$title,
		$blogname,
		$excerpt,
		$category,
		$cat_desc,
		$date,
		$author,
		$display_name,
		$tags,
		$modified,
		$post['postLink']
	);
	$post_tweet = str_ireplace( $search, $replace, $tweet );

	$url_strlen = mb_strlen( urldecode( fake_normalize( $thisposturl ) ), $encoding );
	// check total length 
	$str_length = mb_strlen( urldecode( fake_normalize( $post_tweet ) ), $encoding );
	if ( $str_length < $tweet_length + 1 ) {
		if ( mb_strlen( fake_normalize( $post_tweet ) ) > $tweet_length + 1 ) {
			$post_tweet = mb_substr( $post_tweet, 0, $tweet_length, $encoding );
		}

		return $post_tweet; // return early if all is well without replacements.
	} else {
		// build an array of variable names and the number of characters in that variable.
		$length_array             = array();
		$length_array['excerpt']  = mb_strlen( fake_normalize( $excerpt ), $encoding );
		$length_array['title']    = mb_strlen( fake_normalize( $title ), $encoding );
		$length_array['date']     = mb_strlen( fake_normalize( $date ), $encoding );
		$length_array['category'] = mb_strlen( fake_normalize( $category ), $encoding );
		$length_array['cat_desc'] = mb_strlen( fake_normalize( $cat_desc ), $encoding );
		$length_array['@']        = mb_strlen( fake_normalize( $uaccount ), $encoding );
		$length_array['blogname'] = mb_strlen( fake_normalize( $blogname ), $encoding );
		$length_array['author']   = mb_strlen( fake_normalize( $author ), $encoding );
		$length_array['account']  = mb_strlen( fake_normalize( $account ), $encoding );
		if ( function_exists( 'wpt_pro_exists' ) && wpt_pro_exists() == true ) {
			$length_array['reference'] = mb_strlen( fake_normalize( $reference ), $encoding );
		}
		$length_array['tags']     = mb_strlen( fake_normalize( $tags ), $encoding );
		$length_array['modified'] = mb_strlen( fake_normalize( $modified ), $encoding );
		// if the total length is too long, truncate items until the length is appropriate. 
		// Twitter's t.co shortener is mandatory. All URLS are max-character value set by Twitter.			
		$tco   = ( wpt_is_ssl( $thisposturl ) ) ? 23 : 22;
		$order = get_option( 'wpt_truncation_order' );
		if ( is_array( $order ) ) {
			asort( $order );
			$preferred = array();
			foreach ( $order as $k => $v ) {
				$preferred[ $k ] = $length_array[ $k ];
			}
		} else {
			$preferred = $length_array;
		}
		$diff = ( ( $url_strlen - $tco ) > 0 ) ? $url_strlen - $tco : 0;
		if ( $str_length > ( $tweet_length + 1 + $diff ) ) {
			foreach ( $preferred AS $key => $value ) {
				// don't truncate content of post excerpt if excerpt tag not in use
				if ( ! ( $key == 'excerpt' && ! $has_excerpt_tag ) && ! ( $key == 'title' && ! $has_title_tag ) ) {
					$str_length = mb_strlen( urldecode( fake_normalize( trim( $post_tweet ) ) ), $encoding );
					if ( $str_length > ( $tweet_length + 1 + $diff ) ) {
						$trim      = $str_length - ( $tweet_length + 1 + $diff );
						$old_value = ${$key};
						// prevent URL from being modified
						$post_tweet = str_ireplace( array( $thisposturl, $post['postLink'] ), array( '#url#', '#longurl#' ), $post_tweet );
						// modify the value and replace old with new
						if ( $key == 'account' || $key == 'author' || $key == 'category' || $key == 'date' || $key == 'modified' || $key == 'reference' || $key == '@' ) {
							// these elements make no sense if truncated, so remove them entirely.
							$new_value = '';
						} else if ( $key == 'tags' ) {
							// remove any stray hash characters due to string truncation
							if ( mb_strlen( $old_value ) - $trim <= 2 ) {
								$new_value = '';
							} else {
								$new_value = $old_value;
								while ( ( mb_strlen( $old_value ) - $trim ) < mb_strlen( $new_value ) ) {
									$new_value = trim( mb_substr( $new_value, 0, mb_strrpos( $new_value, '#', $encoding ) - 1 ) );
								}
							}
						} else {
							// trim letters
							$new_value = mb_substr( $old_value, 0, - ( $trim ), $encoding );
							// trim rest of last word
							$last_space = strrpos( $new_value, ' ' );
							$new_value  = apply_filters( 'wpt_filter_truncated_value', mb_substr( $new_value, 0, $last_space, $encoding ), $key );
						}
						$post_tweet = str_ireplace( $old_value, $new_value, $post_tweet );
						// put URL back before checking length
						$post_tweet = str_ireplace( array( '#url#', '#longurl#' ), array( $thisposturl, $post['postLink'] ), $post_tweet );
					} else {
						if ( mb_strlen( fake_normalize( $post_tweet ), $encoding ) > ( $tweet_length + 1 + $diff ) ) {
							$post_tweet = mb_substr( $post_tweet, 0, ( $tweet_length + $diff ), $encoding );
						}
					}
				}
			}
		}
		// this is needed in case a tweet needs to be truncated outright and the truncation values aren't in the above.
		// 1) removes URL 2) checks length of remainder 3) Replaces URL
		if ( mb_strlen( fake_normalize( $post_tweet ) ) > $tweet_length + 1 ) {
			$temp = str_ireplace( array( $thisposturl, $post['postLink'] ), array( '#url#', '#longurl#' ), $post_tweet );
			if ( mb_strlen( fake_normalize( $temp ) ) > ( ( $tweet_length + 1 ) - ( $tco - strlen( '#url#' ) ) ) && $temp != $post_tweet ) {
				$post_tweet = trim( mb_substr( $temp, 0, ( ( $tweet_length + 1 ) - ( $tco - strlen( '#url#' ) ) ), $encoding ) );
				// it's possible to trim off the #url# part in this process. If that happens, put it back.
				$sub_sentence = ( strpos( $tweet, '#url#' ) === false ) ? $post_tweet : $post_tweet . ' ' . $thisposturl;
				$post_tweet   = ( strpos( $post_tweet, '#url#' ) === false ) ? $sub_sentence : str_ireplace( array( '#url#', '#longurl#' ), array( $thisposturl, $post['postLink'] ), $post_tweet );
			}
		}
	}

	return apply_filters( 'wpt_custom_truncate', $post_tweet, $tweet, $post_ID, $retweet ); // catch all, should never happen. But no reason not to include it.
}