<?php
global $userdata;

/**
 * First we'll run the shortcode that displays the login form
 * if the user isn't logged in. Otherwise it displays a logout link.
 * This shortcode is found in Absolute Privacy's functions.php file
 */
echo do_shortcode( '[loginform]' );

/**
 * We'll only show any of this profile updating form if the user is logged in.
 */
if ( is_user_logged_in() ) :
	require_once( ABSPATH . WPINC . '/registration.php' ); //this file is needed for functions used below

	$user_id = $userdata->ID; //reassign the username variable to ensure everything is up to date
	$errors = false;

	if ( isset( $_POST['wp-submit'] ) ) { // The form has been submitted

		/* Here we check for errors. We'll throw an error if the user has
		 * left the first name, last name, or email address empty.
		 */
		if ( empty( $_POST['first_name'] ) || empty( $_POST['last_name'] ) || empty( $_POST['user_email'] ) ) {
			$errors = __( 'You must enter a value for first name, last name, and email address.', 'absprivacy' );
		}


		/* Here we check if the user is trying to submit a new password.
		 * We only update the password if there are no previous errors,
		 * and if both inputted passwords match.
		 */
		if ( isset( $_POST['password'] ) && false === $errors && ! empty( $_POST['password'] ) ) {
			if ( strtolower( $_POST['password'] ) !== strtolower( $_POST['password2'] ) || empty( $_POST['password2'] ) ) {
				$errors = __( 'Your passwords do not match.', 'absprivacy' );
			} else {
				wp_set_password( $_POST['password'], $user_id );
			}
		}

		if ( false === $errors ) { //no errors, so lets update the user
			wp_update_user(
				array(
				     'ID'         => $user_id,
				     'first_name' => htmlentities( trim( $_POST['first_name'] ) ),
				     'last_name'  => htmlentities( trim( $_POST['last_name'] ) ),
				     'user_email' => htmlentities( trim( $_POST['user_email'] ) )
				)
			);

			echo '<p class="profile_updated"><em>' . __( 'Your profile has been updated', 'absprivacy' ) . '</em></p>';
		} else {
			echo '<p class="profile_errors"><strong>ERROR:</strong> ' . $errors . '</p>';
		}
	}

	$user = new WP_User( $user_id ); // use this instead of $userdata so that the changes are reflected after a user updates the form
	?>


	<p><?php _e( 'You may edit your profile using the form below.', 'absprivacy' ); ?></p>

	<form name="profileform" action="" method="post">

		<p>
			<label for="first_name"><?php _e( 'First Name', 'absprivacy' ); ?></label>
			<input type="text" name="first_name" id="first_name" class="input" value="<?php echo $user->first_name; ?>"
			       size="30" tabindex="10"/>
		</p>

		<p>
			<label for="last_name"><?php _e( 'Last Name', 'absprivacy' ); ?></label>
			<input type="text" name="last_name" id="last_name" class="input" value="<?php echo $user->last_name; ?>"
			       size="30" tabindex="20"/>
		</p>


		<p>
			<label for="user_email"><?php _e( 'Email Address', 'absprivacy' ); ?></label>
			<input type="text" name="user_email" id="user_email" class="input" value="<?php echo $user->user_email; ?>"
			       size="40" tabindex="30"/>
		</p>

		<p><?php _e( 'You may also change your password (optional).', 'absprivacy' ); ?></p>

		<p>
			<label for="password"><?php _e( 'Password', 'absprivacy' ); ?></label>
			<input type="password" name="password" id="password" class="input" value="" size="20" tabindex="40"/> <br/>
			<label for="password2"><?php _e( 'And Again', 'absprivacy' ); ?></label>
			<input type="password" name="password2" id="password2" class="input" value="" size="20" tabindex="50"/>
		</p>

		<input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php _e( 'Submit Changes', 'absprivacy' ); ?>" tabindex="100"/>
	</form>
<?php endif; ?>