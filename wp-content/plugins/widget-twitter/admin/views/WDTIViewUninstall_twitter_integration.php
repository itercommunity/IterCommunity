<?php

class WDTIViewUninstall_twitter_integration {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  private $model;


  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct($model) {
    $this->model = $model;
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function display() {
    global $wpdb;
    $prefix = $wpdb->prefix;
    ?>
	<div style="float: right; text-align: right;">
        <a style="color: red; text-decoration: none;" target="_blank" href="http://web-dorado.com/files/fromTwitterIntegrationWP.php">
          <img width="215" border="0" alt="web-dorado.com" src="<?php echo WD_WDTI_URL . '/images/header.png'; ?>" />
          <p style="font-size: 16px; margin: 0; padding: 0 20px 0 0;">Get the full version</p>
        </a>
    </div>
    <form method="post" action="admin.php?page=uninstall_twitter_integration" style="width:95%;">
      <?php wp_nonce_field('best_wordpress_gallery uninstall');?>
      <div class="wrap">
        <span class="uninstall_icon"></span>
        <h2>Uninstall Widget Twitter</h2>
        <p>
          Deactivating Widget Twitter plugin does not remove any data that may have been created. To completely remove this plugin, you can uninstall it here.
        </p>
        <p style="color: red;">
          <strong>WARNING:</strong>
          Once uninstalled, this can't be undone. You should use a Database Backup plugin of WordPress to back up all the data first.
        </p>
        <p style="color: red">
          <strong>The following Database Table will be deleted:</strong>
        </p>
        <table class="widefat">
          <thead>
            <tr>
              <th>Database Table</th>
            </tr>
          </thead>
          <tr>
            <td valign="top">
              <ol>
                  <li><?php echo $prefix; ?>twitter_integration</li>
              </ol>
            </td>
          </tr>
        </table>
        <p style="text-align: center;">
          Do you really want to uninstall Widget Twitter?
        </p>
        <p style="text-align: center;">
          <input type="checkbox" name="Widget Twitter" id="check_yes" value="yes" />&nbsp;<label for="check_yes">Yes</label>
        </p>
        <p style="text-align: center;">
          <input type="submit" value="UNINSTALL" class="button-primary" onclick="if (check_yes.checked) { 
                                                                                                      if (confirm('You are About to Uninstall Widget Twitter from WordPress.\nThis Action Is Not Reversible.')) {
                                                                                                          spider_set_input_value('task', 'uninstall');
                                                                                                      } else {
                                                                                                          return false;
                                                                                                      }
                                                                                                    }
                                                                                                    else {
                                                                                                      return false;
                                                                                                    }" />
        </p>
      </div>
      <input id="task" name="task" type="hidden" value="" />
    </form>
  <?php
  }

  public function uninstall() {
    $this->model->delete_db_tables();
    global $wpdb;
    $prefix = $wpdb->prefix;
    $deactivate_url = wp_nonce_url('plugins.php?action=deactivate&amp;plugin=widget-twitter/twitter.php', 'deactivate-plugin_widget-twitter/twitter.php');
	?>
	<div style="float: right; text-align: right;">
        <a style="color: red; text-decoration: none;" target="_blank" href="http://web-dorado.com/files/fromTwitterIntegrationWP.php">
          <img width="215" border="0" alt="web-dorado.com" src="<?php echo WD_WDTI_URL . '/images/header.png'; ?>" />
          <p style="font-size: 16px; margin: 0; padding: 0 20px 0 0;">Get the full version</p>
        </a>
    </div>
    <div id="message" class="updated fade">
      <p>The following Database Tables succesfully deleted:</p>
      <p><?php echo $prefix; ?>twitter_integration,</p>
    </div>
    <div class="wrap">
      <h2>Uninstall Widget Twitter</h2>
      <p><strong><a href="<?php echo $deactivate_url; ?>">Click Here</a> To Finish the Uninstallation and Widget Twitter will be Deactivated Automatically.</strong></p>
      <input id="task" name="task" type="hidden" value="" />
    </div>
  <?php
  }
  
  ////////////////////////////////////////////////////////////////////////////////////////
  // Getters & Setters                                                                  //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Private Methods                                                                    //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Listeners                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
}