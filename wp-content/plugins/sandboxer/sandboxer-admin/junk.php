if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('admin_menu', 'sboxr_admin_add_page');

function sboxr_general_options_admin_head(){ 
	sboxr_options_admin_head();
}

/*
function sboxr_about_options_admin_head(){ 
	sboxr_options_admin_head();
}
*/

function sboxr_options_admin_head(){ 
	?>
<style type="text/css">
.container {width: 100%; margin: 10px 0px; font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;}
ul.tabs {margin: 0;padding: 0;float: left;list-style: none;height: 25px;border-bottom: 1px solid #e3e3e3;border-left: 1px solid #e3e3e3;width: 100%;}
ul.tabs li {float: left;margin: 0;padding: 0;	height: 24px;line-height: 24px;border: 1px solid #e3e3e3;border-left: none;margin-bottom: -1px;background:#EBEBEB;overflow: hidden;position: relative; background-repeat:repeat-x;}
ul.tabs li a {text-decoration: none;color: #21759b;display: block;font-size: 12px;padding: 0 20px;border: 1px solid #fff;outline: none;}
ul.tabs li a:hover {color: #d54e21;}
html ul.tabs li.active, html ul.tabs li.active a:hover  {background: #fff;border-bottom: 1px solid #fff;}
.tab_container {border: 1px solid #e3e3e3;border-top: none;clear: both;float: left; width: 100%;background: #fff;font-size:11px;}
.tab_content {padding: 20px;font-size: 1.2em;}
.tab_content h3 {margin-top:0px;margin-bottom:10px;}
.tab_content .head-description{font-style:italic;}
.tab_content .description{padding-left:15px}
.tab_content ul li{list-style:square outside; margin-left:20px}
a.delete_source { background-color: red; color: white; padding: 6px; -webkit-border-radius: 4px; border-radius: 4px; -webkit-box-shadow:  1px 2px 3px 3px rgba(90, 80, 80, 0.6); text-decoration: none; box-shadow:  1px 2px 3px 3px rgba(90, 80, 80, 0.6); border-bottom: #aa0000 1px solid; border-right: #bb0000 1px solid; }
a.add_source { background-color: #44aa44; color: white; padding: 6px; -webkit-border-radius: 4px; border-radius: 4px; -webkit-box-shadow:  1px 2px 3px 3px rgba(70, 90, 80, 0.6); text-decoration: none; box-shadow:  1px 2px 3px 3px rgba(70, 90, 80, 0.6); border-bottom: #00aa00 1px solid; border-right: #00bb00 1px solid; }

.zebra-1 td { background-color: #eeeeee; }
.zebra-0 td, .zebra-2 td { background-color: #ffffff; }

</style>
<script type="text/javascript">
jQuery(document).ready(function() {
    //Default Action
    // jQuery(".tab_content").hide(); //Hide all content
    // jQuery("ul.tabs li:first").addClass("active").show(); //Activate first tab
    // jQuery(".tab_content:first").show(); //Show first tab content
    //On Click Event
    jQuery("ul.tabs li").click(function() {
            // jQuery(".tab_content").hide(); //Hide all tab content
            var activeTab = jQuery(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
            jQuery(activeTab).show();
            return false;
    });
});
</script>
<script type="text/javascript" src="<?php print SBOXR_URL; ?>sboxr-admin/add-remove.js"></script>
<?php
} /* EO function sboxr_options_admin_head() */

function sboxr_general_page() {
	$registered_options = array(
		'general' => 'general',
		'remotesources' => 'remotesources',
		'test' => 'test',
		'faqs' => 'faqs',
		'manual' => 'manual',
		'about' => 'about'
	);

    $sboxr_debug = sboxr_debug();
	$tab = isset($_GET['tab']) ? $_GET['tab'] : ""; 
	$active_tab = (($tab) ? $registered_options[$tab] : array_shift($registered_options));	
if ($sboxr_debug !== FALSE) {
	?>		
<!-- div id="message" class="error"><p><strong><?php echo $sboxr_debug ?></strong></p></div -->
<?php } ?>
<div class="wrap">

<?php 
if(function_exists('screen_icon')) { screen_icon(); } ?>
<h2><?php _e('Sandboxer General Settings'); ?></h2>
<small>Powered by <a href="http://www.itercom.org/" target="_blank">The Iter Community</a>. 
UPGRADE<br/>
</small>

<div class="container">
    <ul class="tabs">
			<li class="nav-tab <?php echo $active_tab == 'general' ? 'active' : ''; ?>"><a href="?page=sboxr-admin/options.php&tab=general"><?php  _e("General Options", 'wp-remote-to-post')?></a></li>
			<li class="nav-tab <?php echo $active_tab == 'remotesources' ? 'active' : ''; ?>"><a href="?page=sboxr-admin/options.php&tab=remotesources"><?php  _e("Remote Sources", 'wp-remote-to-post')?></a></li>
			<li class="nav-tab <?php echo $active_tab == 'about' ? 'active' : ''; ?>"><a href="?page=sboxr-admin/options.php&tab=test"><?php  _e("Test", 'wp-remote-to-post')?></a></li>
			<li class="nav-tab <?php echo $active_tab == 'faqs' ? 'active' : ''; ?>"><a href="?page=sboxr-admin/options.php&tab=faqs"><?php  _e("FAQs", 'wp-remote-to-post')?></a></li>
			<li class="nav-tab <?php echo $active_tab == 'manual' ? 'active' : ''; ?>"><a href="?page=sboxr-admin/options.php&tab=manual"><?php  _e("Manual", 'wp-remote-to-post')?></a></li>
			<li class="nav-tab <?php echo $active_tab == 'about' ? 'active' : ''; ?>"><a href="?page=sboxr-admin/options.php&tab=about"><?php  _e("About", 'wp-remote-to-post')?></a></li>
    </ul>
    <div class="tab_container">
        <div id="tab1" class="tab_content">
			<?php 
			$include = $active_tab."_tab.php";			

			// print $include;
			// print "<pre>".print_r($_POST, TRUE)."</pre>";
			// print "<hr/>";
			// print "<pre>".print_r($_GET, TRUE)."</pre>";

			if (isset($_POST['option_page'])) {
				// print preg_replace('/(sboxr_)([a-z]+)(_options)/', '$2', $_POST['option_page']);
				if (($_POST['option_page'] == 'sboxr_options') || (in_array(preg_replace('/(sboxr_)([a-z]+)(_options)/', '$2', $_POST['option_page']), $registered_options))) {
					// print print_r($_POST[$_POST['option_page']], TRUE);
					// print "<br/>".$_POST['option_page'];

					update_option($_POST['option_page'], $_POST[$_POST['option_page']]);
					}
			}

			include_once($include);
			?>
        </div>
    </div>
</div>
<?php }