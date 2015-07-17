<?php

class register_wid extends WP_Widget {
	
	public function __construct() {
		parent::__construct(
	 		'register_wid',
			'Register Widget AFO',
			array( 'description' => __( 'This is a simple register form in the widget.', 'rwa' ), )
		);
		add_action( 'init', array($this, 'register_validate' ) );
	 }

	public function widget( $args, $instance ) {
		extract( $args );
		
		$wid_title = apply_filters( 'widget_title', $instance['wid_title'] );
		
		echo $args['before_widget'];
		if ( ! empty( $wid_title ) )
			echo $args['before_title'] . $wid_title . $args['after_title'];
			$this->registerForm();
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['wid_title'] = strip_tags( $new_instance['wid_title'] );
		return $instance;
	}


	public function form( $instance ) {
		$wid_title = $instance[ 'wid_title' ];
		?>
		<p><label for="<?php echo $this->get_field_id('wid_title'); ?>"><?php _e('Title:'); ?> </label>
		<input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo $wid_title; ?>" />
		</p>
		<?php 
	}
	
	public function is_field_enabled($value){
		$data = get_option( $value );
		if($data == 'Yes'){
			return true;
		} else {
			return false;
		}
	}
	
	public function is_field_required($value){
		$data = get_option( $value );
		if($data == 'Yes'){
			return 'required="required"';
		} else {
			return '';
		}
	}
	
	public function registerForm(){
		global $post;

		$this->error_message();
		if(!is_user_logged_in()){
			if(get_option('users_can_register')) {  
		?>
		<form name="register" id="register" method="post" action="" enctype="multipart/form-data">
		<input type="hidden" name="option" value="afo_user_register" />
		<div id="reg_forms">
			
			<div class="form-group">
				<label for="username"><?php _e('Username','rwa');?> </label>
				<input type="text" name="user_login" required="required" placeholder="<?php _e('Username','rwa');?>"/>
			</div>
			
			<div class="form-group">
				<label for="useremail"><?php _e('User Email','rwa');?> </label>
				<input type="email" name="user_email" required="required" placeholder="<?php _e('User Email','rwa');?>"/>
			</div>
			
			<?php if($this->is_field_enabled('password_in_registration')){ ?>
			<div class="form-group">
			<label for="password"><?php _e('Password','rwa');?> </label>
			<input type="password" name="new_user_password" required="required" placeholder="<?php _e('Password','rwa');?>" />
			</div>
			
			<div class="form-group">
			<label for="retypepassword"><?php _e('Retype Password','rwa');?> </label>
			<input type="password" name="re_user_password" required="required" placeholder="<?php _e('Retype Password','rwa');?>"/>
			</div>
			<?php } ?>
			
			<?php if($this->is_field_enabled('firstname_in_registration')){ ?>
			<div class="form-group">
			<label for="firstname"><?php _e('First Name','rwa');?> </label>
			<input type="text" name="first_name" <?php echo $this->is_field_required('is_firstname_required');?> placeholder="<?php _e('First Name','rwa');?>"/>
			</div>
			<?php } ?>
			
			<?php if($this->is_field_enabled('lastname_in_registration')){ ?>
			<div class="form-group">
			<label for="lastname"><?php _e('Last Name','rwa');?> </label>
			<input type="text" name="last_name" <?php echo $this->is_field_required('is_lastname_required');?> placeholder="<?php _e('Last Name','rwa');?>"/>
			</div>
			<?php } ?>
			
			<?php if($this->is_field_enabled('displayname_in_registration')){ ?>
			<div class="form-group">
			<label for="displayname"><?php _e('Display Name','rwa');?> </label>
			<input type="text" name="display_name" <?php echo $this->is_field_required('is_displayname_required');?> placeholder="<?php _e('Display Name','rwa');?>"/>
			</div>
			<?php } ?>
			
			<?php if($this->is_field_enabled('userdescription_in_registration')){ ?>
			<div class="form-group">
			<label for="aboutuser"><?php _e('About User','rwa');?> </label>
			<textarea name="description" <?php echo $this->is_field_required('is_userdescription_required');?>></textarea>
			</div>
			<?php } ?>
			
			<?php if($this->is_field_enabled('userurl_in_registration')){ ?>
			<div class="form-group">
			<label for="website"><?php _e('Website','rwa');?> </label>
			<input type="url" name="user_url" <?php echo $this->is_field_required('is_userurl_required');?> placeholder="<?php _e('Website','rwa');?>"/>
			</div>
			<?php } ?>
			
			<?php do_action('wp_register_profile_subscription'); ?>
			
			<div class="form-group"><input name="register" type="submit" value="<?php _e('Register','rwa');?>" /></div>

		</div>
		</form>

		<?php 
		} else {
			echo '<div id="reg_forms"><p>'.__('Sorry. Registration is not allowed in this site.','rwa').'</p></div>';
		}
		}
	}
	
	
	public function error_message(){
		if(isset($_SESSION['reg_error_msg']) and $_SESSION['reg_error_msg']){
			echo '<div class="'.$_SESSION['reg_msg_class'].'">'.$_SESSION['reg_error_msg'].'</div>';
			unset($_SESSION['reg_error_msg']);
			unset($_SESSION['reg_msg_class']);
		}
	}
	
	public function set_html_content_type() {
		return 'text/html';
	}
	
	public function create_user($data){
		$userdata = $data['userdata'];
		
		// insert new user in db //
			$user_id = wp_insert_user( $userdata ) ;
		// insert new user in db //
		
		// send mail to user //
		$subject = __('Registration Successfull','rwa');
		$body = __('Thankyou for registration','rwa').'<br><br>
		
				'.__('Username','rwa').' : '.$userdata['user_login'].'<br>
				'.__('Password','rwa').' : '.$userdata['user_pass'].'<br>
				'.__('Site Link','rwa').' : '.site_url().'<br>
		';
		
		$to_array = array($userdata['user_email']);
		add_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
		wp_mail( $to_array, $subject, $body );
		remove_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
		// send mail to user //
		
		$_SESSION['reg_error_msg'] = __('You are successfully registered to the site. Please check your email for login details.','rwa');
		$_SESSION['reg_msg_class'] = 'reg_success';
		
		return $user_id;
	}
				
	public function register_validate(){
		if(isset($_POST['option']) and $_POST['option'] == "afo_user_register"){
			global $post;
			$error = false;
			
			if ( username_exists( $_POST['user_login'] ) ){
				$msg .= __('Username already exists. Please use a different one!','rwa');
				$msg .= '</br>';
				$error = true;
			}
			
			if( email_exists( $_POST['user_email'] )) {
				$msg .= __('Email already exists. Please use a different one!','rwa');
				$msg .= '</br>';
				$error = true;
			}
			
			if($this->is_field_enabled('password_in_registration')){ 
				if($_POST['new_user_password'] != $_POST['re_user_password']){
					$msg .= __('Password and Retype password donot match!','rwa');
					$msg .= '</br>';
					$error = true;
				}
			}
			
			
			if(!$error){
				$userdata = array(
					'user_login' => $_POST['user_login'],
					'user_email' => $_POST['user_email']
					);
				
				if($this->is_field_enabled('password_in_registration') and $_POST['new_user_password']){
					$new_pass = $_POST['new_user_password'];
					$userdata['user_pass'] = $new_pass;
				} else {
					$new_pass = wp_generate_password();
					$userdata['user_pass'] = $new_pass;
				}
				
				if($this->is_field_enabled('firstname_in_registration') and $_POST['first_name']){
					$userdata['first_name'] = $_POST['first_name'];
				}
				
				if($this->is_field_enabled('lastname_in_registration') and $_POST['last_name']){
					$userdata['last_name'] = $_POST['last_name'];
				}
				
				if($this->is_field_enabled('displayname_in_registration') and $_POST['display_name']){
					$userdata['display_name'] = $_POST['display_name'];
				}
				
				if($this->is_field_enabled('userdescription_in_registration') and $_POST['description']){
					$userdata['description'] = $_POST['description'];
				} 
				
				if($this->is_field_enabled('userurl_in_registration') and $_POST['user_url']){
					$userdata['user_url'] = $_POST['user_url'];
				} 
				
				if(get_option('enable_subscription') == 'Yes'){
					$_SESSION['wp_register_subscription']['userdata'] = $userdata;
					$_SESSION['wp_register_subscription']['sub_type'] = $_REQUEST['sub_type'];
					$redirect_page = get_permalink(get_option('subscription_page'));
					wp_redirect($redirect_page);
					exit;
				} else {
					$create_user_data['userdata'] = $userdata;
					$user_id = $this->create_user($create_user_data);
					$_SESSION['reg_error_msg'] = __('You are successfully registered to the site. Please check your email for login details.','rwa');
					$_SESSION['reg_msg_class'] = 'reg_success';
					
					$redirect_page = get_option('thank_you_page_after_registration_url');
					if($redirect_page){
						$redirect =  get_permalink($redirect_page);
					} else {
						$redirect =  get_permalink($post->ID);
					}
					wp_redirect($redirect);
					exit;
				}
			} else {
				$_SESSION['reg_error_msg'] = $msg;
				$_SESSION['reg_msg_class'] = 'reg_error';
			}
		}
	}
	
} 

add_action( 'widgets_init', create_function( '', 'register_widget( "register_wid" );' ) );
?>