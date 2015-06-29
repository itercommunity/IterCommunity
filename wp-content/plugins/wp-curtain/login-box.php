<div class="seperator"></div>
<div id="login-section">
	<button id="login-slide-down" class="button">Login</button>
	<div id="login-box">
		<?php
			$form_args = array();
			$url = $wpc_settings['redirect_url'];
			if($url && filter_var($url, FILTER_VALIDATE_URL)==true) 
				$form_args['redirect'] = $url;
			wp_login_form($form_args);
		?>
		<button class="button" id="login-slide-up"><img style="vertical-align:middle;" src="<?php echo plugins_url( 'static/img/arrow-up.png' , __FILE__ ) ?>"/></button>
	</div>
</div>
<script>
	$(document).ready(function(){
		$("#login-slide-down").click(function(){
			$("#login-box").slideDown();
			$(this).slideUp();
		});
		$("#login-slide-up").click(function(){
			$("#login-box").slideUp();
			$("#login-slide-down").slideDown();
		});
	});
</script>