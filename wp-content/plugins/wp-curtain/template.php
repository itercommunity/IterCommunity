<!DOCTYPE html>
<head>
	<?php $wpc_settings = get_option("wpc_settings"); ?>
	<title><?php echo $wpc_settings['page_title']?$wpc_settings['page_title']:"We'll be right back"; ?></title>
	<?php wp_head(); ?>
	<script type="text/javascript">var futureDateString = '<?php echo $wpc_settings['future_date']['date'].' '.$wpc_settings['future_date']['hh'].':'.$wpc_settings['future_date']['mm'].':'.$wpc_settings['future_date']['ss']; ?>';$ = jQuery;</script>
</head>
<body class="wp-curtain">
	<div id="container">
		<div id="content-section">
			<h1><?php echo $wpc_settings['page_heading']?$wpc_settings['page_heading']:"We'll be right back"; ?></h1>
			<p><?php echo $wpc_settings['page_description']?$wpc_settings['page_description']:"Please try again later"; ?></p>
		</div>
		<?php if(!$wpc_settings['disable_timer']) include('counter.php'); ?>
		<?php if(!$wpc_settings['disable_login_box'] && !is_user_logged_in()) include('login-box.php'); ?>
	</div>
</body>