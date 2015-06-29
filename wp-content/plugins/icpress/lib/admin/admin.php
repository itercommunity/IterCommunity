<?php

// ADMIN -----------------------------------------------------------------------------------------

    function ICPress_options()
    {
    
    	// NEW CODE FOR ICPRESS 
    	// let users add keywords and other data associated with their records
    
    
    	// TODO: what is the ideal permission?
    	// TODO: what about the admin permission / role? 
    	// see http://codex.wordpress.org/Roles_and_Capabilities for more
    	    	
    	if  ( (current_user_can('edit_posts')) && (isset($_GET['ICPress_editItem']) ) )
        	{
            	include( dirname(__FILE__) . '/admin.recordedit.php' );
        	}
    
    
    
        // Prevent access to users who are not editors
        if ( !current_user_can('edit_others_posts') && !is_admin() )
			wp_die( __('Only editors can access this page through the admin panel.'), __('ICPress: Access Denied') );
        
        
        
        // SETUP AND IMPORT PAGES
        
        if (isset($_GET['setup']))
        {
            include( dirname(__FILE__) . '/admin.setup.php' );
        }
        
        else if (isset($_GET['import']))
        {
            include( dirname(__FILE__) . '/../import/import.php' );
        }
        
        else if (isset($_GET['selective']))
        {
            include( dirname(__FILE__) . '/../import/import.selective.php' );
        }
                
        
        
        // ACCOUNTS PAGE
        
        else if (isset($_GET['accounts']))
        {
            include( dirname(__FILE__) . '/admin.accounts.php' );
        }
        
        
        
        // OPTIONS PAGE
        
        else if (isset($_GET['options']))
        {
            include( dirname(__FILE__) . '/admin.options.php' );
        }
        
        
        // CITATION EDIT PAGE
        
        else if (isset($_REQUEST['citation']))
        {
            include( dirname(__FILE__) . '/admin.citations.php' );
        }
        
                
        
        // HELP PAGE
        
        else if (isset($_GET['help']))
        {
            include( dirname(__FILE__) . '/admin.help.php' );
        }
        
        
        
        // BROWSE PAGE
        
        else
        {
            include( dirname(__FILE__) . '/admin.browse.php' );
        }
    }

// END ADMIN ------------------------------------------------------------------------------------------

?>