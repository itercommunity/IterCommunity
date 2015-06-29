<?php
/**
 * This file is used when deleting Absolute Privacy from within the plugin page.
 * The values for delete_option() and remove_role() have been hardcoded here, so
 * they may need some adjusting if you've changed their values in absolute_privacy.php
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
} //prevent direct access


delete_option( 'absolute-privacy-options' ); //remove the absolute privacy options
remove_role( 'unapproved' ); //removed the 'unapproved' role