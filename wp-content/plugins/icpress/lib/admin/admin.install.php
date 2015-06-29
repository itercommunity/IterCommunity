<?php

// INSTALL -----------------------------------------------------------------------------------------
    
    function ICPress_install()
    {
        global $wpdb;
        $ICPress_main_db_version = "5.2";
        $ICPress_oauth_db_version = "5.0.5";

        $ICPress_marcItems_db_version = "5.2.2";        
        $ICPress_zoteroItems_db_version = "5.2.2";
        $ICPress_zoteroCollections_db_version = "5.2.2";
        $ICPress_zoteroTags_db_version = "5.2.2";
        $ICPress_zoteroRelItemColl_db_version = "5.2.1";
        $ICPress_zoteroRelItemTags_db_version = "5.2.1";
        $ICPress_zoteroItemImages_db_version = "5.2.6";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        
        
        // ZOTERO ACCOUNTS TABLE
		
		/**
		 * For each table, the basic check is:
		 *
		 * If the table version option doesn't exist, OR
		 * If the table version is not the same as the update version (variables defined above)
		 *
		 * Then add/update the table and add/update the option
		 */
        
        if
			(
				!get_option("ICPress_main_db_version")
                || get_option("ICPress_main_db_version") != $ICPress_main_db_version
            )
        {
			$table_name = $wpdb->prefix . "icpress";
            
            $structure = "CREATE TABLE $table_name (
                id INT(9) NOT NULL AUTO_INCREMENT,
                account_type VARCHAR(10) NOT NULL,
                api_user_id VARCHAR(10) NOT NULL,
                public_key VARCHAR(28) default NULL,
                nickname VARCHAR(200) default NULL,
                version VARCHAR(10) default '5.1',
                UNIQUE KEY id (id)
            );";
            
            dbDelta($structure);
            
            update_option("ICPress_main_db_version", $ICPress_main_db_version);
        }
        
        
        // OAUTH CACHE TABLE
        
        if (!get_option("ICPress_oauth_db_version")
                || get_option("ICPress_oauth_db_version") != $ICPress_oauth_db_version
                )
        {
			$table_name = $wpdb->prefix . "icpress_oauth";
            
            $structure = "CREATE TABLE $table_name (
                id INT(9) NOT NULL AUTO_INCREMENT,
                cache LONGTEXT NOT NULL,
                UNIQUE KEY id (id)
            );";
            
            dbDelta($structure);
            
            update_option("ICPress_oauth_db_version", $ICPress_oauth_db_version);
            
            // Initial populate
            if ($wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."icpress_oauth;") == 0)
                $wpdb->query("INSERT INTO ".$wpdb->prefix."icpress_oauth (cache) VALUES ('empty')");
        }

        // MARC RECORDS TABLE
        if ( !get_option("ICPress_marcItems_db_version")
                || get_option("ICPress_marcItems_db_version") != $ICPress_marcItems_db_version
           )
        {
			$table_name = $wpdb->prefix . "icpress_marcItems";
            
			// Check if table exists first, then, alter it
			$table_exists = $wpdb->query( "SELECT COUNT(table_name) AS count 
					FROM INFORMATION_SCHEMA.TABLES 
				    WHERE table_schema = '".$wpdb->dbname."' 
					AND table_name = '$table_name'");
			
            $structure = "CREATE TABLE $table_name (

				MARC RECORDS FIELDS
				
                UNIQUE KEY id (id),
                PRIMARY KEY (api_user_id,item_key)
            );";
            
            dbDelta( $structure );
            
            update_option( "ICPress_marcItems_db_version", $ICPress_marcItems_db_version );
        }
        
        
        // ZOTERO ITEMS TABLE
        if ( !get_option("ICPress_zoteroItems_db_version")
                || get_option("ICPress_zoteroItems_db_version") != $ICPress_zoteroItems_db_version    
           )
        {
			$table_name = $wpdb->prefix . "icpress_zoteroItems";
            
			// Check if table exists first, then, alter it
			$table_exists = $wpdb->query( "SELECT COUNT(table_name) AS count 
					FROM INFORMATION_SCHEMA.TABLES 
				    WHERE table_schema = '".$wpdb->dbname."' 
					AND table_name = '$table_name'");
			
			if ( $table_exists == 1 )
			{
				$wpdb->query(
					"
					ALTER TABLE $table_name DROP PRIMARY KEY;
					"
				);
				
				// Remove any duplicates before updating structure
				// Thanks to http://www.semicolon.co.za/mysql_tutorials/finding-and-removing-duplicates-in-mysql-database-ii.html
				$wpdb->query(
					"
					DELETE u1 FROM $table_name u1, $table_name u2 
					WHERE u1.id < u2.id 
					AND (u1.item_key = u2.item_key AND u1.api_user_id = u2.api_user_id);
					"
				);
			}
			
            $structure = "CREATE TABLE $table_name (
                id INT(9) AUTO_INCREMENT,
                item_key VARCHAR(50),
                retrieved TEXT,
                json LONGTEXT NOT NULL,
                citation LONGTEXT,
                style VARCHAR(100) DEFAULT 'apa',
                author TEXT,
                zpdate TEXT,
                title TEXT,
                itemType VARCHAR(100),
                linkMode VARCHAR(100),
                parent VARCHAR(100),
                image TEXT,
                numchildren INT(10),
                year VARCHAR(10) DEFAULT '1977',
                updated INT(1) DEFAULT 1,
                UNIQUE KEY id (id),
                PRIMARY KEY (item_key)
            );";
            
            dbDelta( $structure );
            
            update_option( "ICPress_zoteroItems_db_version", $ICPress_zoteroItems_db_version );
        }
        
        // ZOTERO USER-TO-ITEMS TABLE
        if ( !get_option("ICPress_zoteroUserItems_db_version")
                || get_option("ICPress_zoteroUserItems_db_version") != $ICPress_zoteroUserItems_db_version
           )
        {
			$table_name = $wpdb->prefix . "icpress_zoteroUserItems";
            
			// Check if table exists first, then, alter it
			$table_exists = $wpdb->query( "SELECT COUNT(table_name) AS count 
					FROM INFORMATION_SCHEMA.TABLES 
				    WHERE table_schema = '".$wpdb->dbname."' 
					AND table_name = '$table_name'");
			
			if ( $table_exists == 1 )
			{
				$wpdb->query(
					"
					ALTER TABLE $table_name DROP PRIMARY KEY;
					"
				);
				
				// Remove any duplicates before updating structure
				// Thanks to http://www.semicolon.co.za/mysql_tutorials/finding-and-removing-duplicates-in-mysql-database-ii.html
				$wpdb->query(
					"
					DELETE u1 FROM $table_name u1, $table_name u2 
					WHERE u1.id < u2.id 
					AND (u1.item_key = u2.item_key AND u1.api_user_id = u2.api_user_id);
					"
				);
			}
			
            $structure = "CREATE TABLE $table_name (
                id INT(9),
                api_user_id VARCHAR(50),
                keywords TEXT,
                PRIMARY KEY (api_user_id,id)
            );";
            
            dbDelta( $structure );
            
            update_option( "ICPress_zoteroUserItems_db_version", $ICPress_zoteroUserItems_db_version );
        }

        
        // ZOTERO ITEM IMAGES TABLE
        
        if ( !get_option("ICPress_zoteroItemImages_db_version")
                || get_option("ICPress_zoteroItemImages_db_version") != $ICPress_zoteroItemImages_db_version
           )
        {
			$table_name = $wpdb->prefix . "icpress_zoteroItemImages";
			
            $structure = "CREATE TABLE $table_name (
                id INT(9) AUTO_INCREMENT,
                api_user_id VARCHAR(50),
                item_key VARCHAR(50),
                image TEXT,
                UNIQUE KEY id (id),
                PRIMARY KEY (api_user_id, item_key)
            );";
            
            dbDelta( $structure );
            
            update_option( "ICPress_zoteroItemImages_db_version", $ICPress_zoteroItemImages_db_version );
        }
        
        
        // ZOTERO COLLECTIONS TABLE
        
        if (!get_option("ICPress_zoteroCollections_db_version")
                || get_option("ICPress_zoteroCollections_db_version") != $ICPress_zoteroCollections_db_version
                )
        {
            $table_name = $wpdb->prefix . "icpress_zoteroCollections";
            
            $structure = "CREATE TABLE $table_name (
                id INT(9) NOT NULL AUTO_INCREMENT,
                api_user_id VARCHAR(50),
                title TEXT,
                retrieved VARCHAR(100),
                parent TEXT,
                item_key TEXT,
                numCollections INT(9),
                numItems INT(9),
                updated INT(1) DEFAULT 1,
                UNIQUE KEY id (id)
            );";
            
            dbDelta($structure);
            
            update_option("ICPress_zoteroCollections_db_version", $ICPress_zoteroCollections_db_version);
        }
        
        
        // ZOTERO TAGS TABLE
        
        if (!get_option("ICPress_zoteroTags_db_version")
                || get_option("ICPress_zoteroTags_db_version") != $ICPress_zoteroTags_db_version
                )
        {
            $table_name = $wpdb->prefix . "icpress_zoteroTags";
            
            $structure = "CREATE TABLE $table_name (
                id INT(9) NOT NULL UNIQUE AUTO_INCREMENT,
                api_user_id VARCHAR(50),
                title VARCHAR(128) BINARY NOT NULL UNIQUE,
                retrieved VARCHAR(100),
                numItems INT(9),
                updated INT(1) DEFAULT 1,
                PRIMARY KEY (api_user_id, title)
            );";
            
            dbDelta($structure);
            
            update_option("ICPress_zoteroTags_db_version", $ICPress_zoteroTags_db_version);
        }
        
        
        // ZOTERO RELATIONSHIP TABLE FOR ITEMS AND COLLECTIONS
        
        if (!get_option("ICPress_zoteroRelItemColl_db_version")
                || get_option("ICPress_zoteroRelItemColl_db_version") != $ICPress_zoteroRelItemColl_db_version
                )
        {
            $table_name = $wpdb->prefix . "icpress_zoteroRelItemColl";
            
			// Check if table exists first, then, alter it
			$table_exists = $wpdb->query( "SELECT COUNT(table_name) AS count 
					FROM INFORMATION_SCHEMA.TABLES 
				    WHERE table_schema = '".$wpdb->dbname."' 
					AND table_name = '$table_name'");
			
			if ( $table_exists == 1 )
			{
				$wpdb->query(
					"
					ALTER TABLE $table_name DROP PRIMARY KEY;
					"
				);
				
				$wpdb->query(
					"
					DELETE u1 FROM $table_name u1, $table_name u2 
					WHERE u1.id < u2.id 
					AND (u1.item_key = u2.item_key AND u1.collection_key = u2.collection_key AND u1.api_user_id = u2.api_user_id);
					"
				);
			}
            
            $structure = "CREATE TABLE $table_name (
                id INT(9) AUTO_INCREMENT,
                api_user_id VARCHAR(50),
                item_key VARCHAR(50),
                collection_key VARCHAR(50),
                UNIQUE KEY id (id),
                PRIMARY KEY (api_user_id,item_key,collection_key)
            );";
            
            dbDelta($structure);
            
            update_option("ICPress_zoteroRelItemColl_db_version", $ICPress_zoteroRelItemColl_db_version);
        }
        
        
        
        // ZOTERO RELATIONSHIP TABLE FOR ITEMS AND TAGS
        
        if (!get_option("ICPress_zoteroRelItemTags_db_version")
                || get_option("ICPress_zoteroRelItemTags_db_version") != $ICPress_zoteroRelItemTags_db_version
                )
        {
            $table_name = $wpdb->prefix . "icpress_zoteroRelItemTags";
            
			// Check if table exists first, then, alter it
			$table_exists = $wpdb->query( "SELECT COUNT(table_name) AS count 
					FROM INFORMATION_SCHEMA.TABLES 
				    WHERE table_schema = '".$wpdb->dbname."' 
					AND table_name = '$table_name'");
			
			if ( $table_exists == 1 )
			{
				$wpdb->query(
					"
					ALTER TABLE $table_name DROP PRIMARY KEY;
					"
				);
				
				$wpdb->query(
					"
					DELETE u1 FROM $table_name u1, $table_name u2 
					WHERE u1.id < u2.id 
					AND (u1.item_key = u2.item_key AND u1.tag_title = u2.tag_title AND u1.api_user_id = u2.api_user_id);
					"
				);
			}
            
            $structure = "CREATE TABLE $table_name (
                id INT(9) AUTO_INCREMENT,
                api_user_id VARCHAR(50),
                item_key VARCHAR(50),
                tag_title VARCHAR(128),
                UNIQUE KEY id (id),
                PRIMARY KEY (api_user_id,item_key,tag_title)
            );";
            
            dbDelta($structure);
            
            update_option("ICPress_zoteroRelItemTags_db_version", $ICPress_zoteroRelItemTags_db_version);
        }
        
    }
    
    /*
    add_action( 'after_setup_theme', 'icp_enable_thumbnails');
    function icp_enable_thumbnails() {
        add_theme_support( 'post-thumbnails', array( 'icp_entry' ) );
    }
    
    if ( !post_type_exists( 'icp_entry' ) ) add_action( 'init', 'icp_create_post_type' );
    function icp_create_post_type()
    {
        register_post_type( 'icp_entry',
            array(
                'label' => __( 'ICPress Entries' ),
                'labels' => array(
                    'name' => __( 'ICPress Entries' ),
                    'singular_name' => __( 'ICPress Entry' )
                ),
                'description' => 'A generic content type for all Zotero items.',
                'menu_position' => 21,
                'menu_icon' => ICPRESS_PLUGIN_URL.'images/icon-type.png',
                'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
                'public' => true,
                'has_archive' => true
            )
        );
        
        register_taxonomy( 'icp_collections', 'icp_entry',
            array(
                'label' => 'ICPress Collections',
                'labels' => array(
                    'name' => __( 'ICPress Collections' ),
                    'singular_name' => __( 'ICPress Collection' )
                ),
                'hierarchical' => true,
                'public' => true
            )
        );
        register_taxonomy_for_object_type( 'icp_collections', 'icp_entry' );
        
        register_taxonomy( 'icp_tags', 'icp_entry',
            array(
                'label' => 'ICPress Tags',
                'labels' => array(
                    'name' => __( 'ICPress Tags' ),
                    'singular_name' => __( 'ICPress Tag' )
                ),
                'public' => true
            )
        );
        register_taxonomy_for_object_type( 'icp_tags', 'icp_entry' );
    }
    */

    register_activation_hook( ICPRESS_PLUGIN_FILE, 'ICPress_install' );

// INSTALL -----------------------------------------------------------------------------------------



// UNINSTALL --------------------------------------------------------------------------------------

    function ICPress_deactivate()
    {
        global $wpdb;
        global $current_user;
        
        // Drop all tables -- originally not including accounts/main, but not sure why
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_oauth;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_zoteroItems;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_zoteroUserItems;");        
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_zoteroItemImages;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_zoteroCollections;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_zoteroTags;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_zoteroRelItemColl;");
        $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_zoteroRelItemTags;");
        
        // Delete options
        delete_option( 'ICPress_DefaultCPT' );
        delete_option( 'ICPress_DefaultAccount' );
        delete_option( 'ICPress_LastAutoUpdate' );
        delete_option( 'ICPress_DefaultStyle' );
        delete_option( 'ICPress_StyleList' );
        delete_option( 'ICPress_DefaultAutoUpdate' );
        delete_option( 'ICPress_update_version' );
        delete_option( 'ICPress_main_db_version' );
        delete_option( 'ICPress_oauth_db_version' );
        delete_option( 'ICPress_zoteroItems_db_version' );
        delete_option( 'ICPress_zoteroUserItems_db_version' );        
        delete_option( 'ICPress_zoteroCollections_db_version' );
        delete_option( 'ICPress_zoteroTags_db_version' );
        delete_option( 'ICPress_zoteroRelItemColl_db_version' );
        delete_option( 'ICPress_zoteroRelItemTags_db_version' );
		delete_option( 'ICPress_zoteroItemImages_db_version' );
        
        // Delete user meta
        delete_user_meta( $current_user->ID, 'icpress_5_2_ignore_notice' );
        delete_user_meta( $current_user->ID, 'icpress_survey_notice_ignore' );
    }
    
    register_uninstall_hook( ICPRESS_PLUGIN_FILE, 'ICPress_deactivate' );

// UNINSTALL ---------------------------------------------------------------------------------------


// UPDATE ------------------------------------------------------------------------------------------


	/**
	 *
	 * If update check option doesn't exist, OR
	 * If it exists but it's not the same version as the database update version
	 *
	 * Then, run the install, which installs or updates the databases
	 *
	**/
    if
		(
			!get_option( "ICPress_update_version" )
			|| get_option("ICPress_update_version") != $GLOBALS['ICPress_update_db_by_version']
		)
    {
        ICPress_install();
        
        // Add or update version number
        if ( !get_option( "ICPress_update_version" ) )
            add_option( "ICPress_update_version", $GLOBALS['ICPress_update_db_by_version'], "", "no" );
        else
            update_option( "ICPress_update_version", $GLOBALS['ICPress_update_db_by_version'] );
    }
    
// UPDATE ------------------------------------------------------------------------------------------


?>