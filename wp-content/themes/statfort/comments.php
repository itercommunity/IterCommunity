<?php
global $cs_theme_option;
if ( comments_open() ) {
	if ( post_password_required() ) return;
?>
		<?php if ( have_comments() ) : ?>
			<div id="comments">
				 <header class="cs-heading-title">
					<h2 class="cs-section-title"><?php echo comments_number(__('No Comments', 'Statfort'), __('1 Comment', 'Statfort'), __('% Comments', 'Statfort') );?></h2>
				</header>
                 <ul>
                    <?php wp_list_comments( array( 'callback' => 'cs_comment' ) );	?>
                </ul>
				<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
					<div class="navigation">
						<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'Statfort') ); ?></div>
						<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'Statfort') ); ?></div>
					</div> <!-- .navigation -->
				<?php endif; // check for comment navigation ?>
                
				<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
                    <div class="navigation">
                        <div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'Statfort') ); ?></div>
                        <div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'Statfort') ); ?></div>
                    </div><!-- .navigation -->
                <?php endif; ?>
			</div>
		<?php endif; // end have_comments() ?>
	
 			<?php 
			global $post_id;
			$you_may_use = __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s', 'Statfort');
			$must_login = __( 'You must be <a href="%s">logged in</a> to post a comment.', 'Statfort');
			$logged_in_as = __('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'Statfort');
			$required_fields_mark = ' ' . __('Required fields are marked %s', 'Statfort');
			$required_text = sprintf($required_fields_mark , '<span class="required">*</span>' );
	
			$defaults = array( 'fields' => apply_filters( 'comment_form_default_fields', 
				array(
					'notes' => '<p class="comment-notes">
                            </p>',
					'author' => '<p class="comment-form-author">'.
					'<label for="author">' . __( 'Name', 'Statfort').
					'<span>'.( $req ? __( '(required)', 'Statfort') : '' ) .'</span></label><input id="author" name="author" class="nameinput" type="text" value="' .
					esc_attr( $commenter['comment_author'] ) . '" size="30" tabindex="1"  />' .
					'</p><!-- #form-section-author .form-section -->',
					
					'email'  => '<p class="comment-form-email">' .
					'<label for="email">'. __( 'Email', 'Statfort').
					'<span>'.( $req ? __( '(required)', 'Statfort') : '' ) .'</span></label>'.
					'<input id="email" name="email" class="emailinput" type="text"  value="' . 
					esc_attr(  $commenter['comment_author_email'] ) . '" size="30" tabindex="2"/>' .
					'</p><!-- #form-section-email .form-section -->',
					
					'url'    => '<p class="comment-form-website">' .
					'<label for="url">' . __( 'Website', 'Statfort') . '<span></span></label>' .
					'<input id="url" name="url" type="text" class="websiteinput"  value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" tabindex="3" />' .
					'</p><!-- #<span class="hiddenSpellError" pre="">form-section-url</span> .form-section -->' ) ),
					
					'comment_field' => '<p class="comment-form-comment fullwidth">'.
					'<label for="comment">'.__( 'Comment: ', 'Statfort'). '<span>'.( $req ? __( '(required)', 'Statfort') : '' ) .'</span></label>' .
					'<textarea id="comment" name="comment"  class="commenttextarea" rows="4" cols="39"></textarea>' .
					'</p><!-- #form-section-comment .form-section -->',
					
					'must_log_in' => '<p class="must-log-in">' .  sprintf( $must_login,	wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
					'logged_in_as' => '<p class="logged-in-as">' . sprintf( $logged_in_as, admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ),
					'comment_notes_before' => '',
					'comment_notes_after' =>  '',
					'id_form' => 'commentform',
					'id_submit' => 'submit-comment',
					'title_reply' => __( 'Leave a Comment', 'Statfort' ),
					'title_reply_to' => __( 'Leave a Reply to %s', 'Statfort' ),
					'cancel_reply_link' => __( 'Cancel reply', 'Statfort' ),
					'label_submit' => __( 'Submit', 'Statfort' ),); 
					comment_form($defaults, $post_id); 
				?>
				
 <?php }?>