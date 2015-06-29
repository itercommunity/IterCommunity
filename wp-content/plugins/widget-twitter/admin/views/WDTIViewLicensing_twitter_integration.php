<?php

class WDTIViewLicensing_twitter_integration {
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
    ?>   
    <div style="float: right; text-align: right;">
        <a style="color: red; text-decoration: none;" target="_blank" href="http://web-dorado.com/files/fromTwitterIntegrationWP.php">
          <img width="215" border="0" alt="web-dorado.com" src="<?php echo WD_WDTI_URL . '/images/header.png'; ?>" />
          <p style="font-size: 16px; margin: 0; padding: 0 20px 0 0;">Get the full version</p>
        </a>
    </div>
    <div style="width:95%"> <p>
	  This plugin is the non-commercial version of the Twitter Integration. Use of the Twitter Integration is free.<br /> The only
	  limitation is the use of the timelines. If you want to use one of the 4 standard Twitter timelines,
	  you are required to purchase a license.<br/> Purchasing a license will allow adding Twitter timelines to 
      your posts and pages (also as a widget). </p>
      <br />
	  <br />
      <a href="http://web-dorado.com/files/fromTwitterIntegrationWP.php" class="button-primary" target="_blank">Purchase a License</a>
      <br/>
	  <br/>
	  <br/>
	  <p>After the purchasing the commercial version follow this steps:</p>
	  <ol>
		<li>Deactivate Twitter Integration Plugin</li>
		<li>Delete Twitter Integration Plugin</li>
	    <li>Install the downloaded commercial version of the plugin</li>
	  </ol>
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