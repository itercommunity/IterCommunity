<?php
/*
Plugin Module: Drupal
Description: This plugin creates subsites for Drupal
Author: Shawn DeWolfe - mikedewolfe@gmail.com
Version: 0.1
Author URI: http://etcl.uvic.ca/TBA
*/

$settings['baseline'] = sboxr_either_option('sboxr_drupal_baseline', array('sboxr_drupal_baseline' => "./sites/baseline/settings.php"));
$settings['drupal_install'] = sboxr_either_option('sboxr_drupal_install', array('sboxr_drupal_install' => ABSPATH."/drupal"));


/* share for the most part */

/* do the form management */

if ($good != $replaced) {
	make_community($_GET['prefx'], $_GET['suffx'], $_GET['longphrase'], $_GET['tid']);
}
else {
	// error
}

/* the actual functions */

// $args is the $_POST passed through
function sboxr_drupal_make_subsite($args = array()) {
	global $settings;

	if ($source = @file_get_contents($settings['baseline'])) {
		// good to go!

		$prefx = $sboxr_name = $args['sboxr_name']; 
		$suffx = $sboxr_user = $args['sboxr_user']; 
		$sboxr_path = $args['sboxr_path']; 
		$sboxr_status = $args['sboxr_status']; 
		$sboxr_module = $args['sboxr_module']; 

		$machine = str_replace(" ","-",sboxr_defang(strtolower($prefx." ".$suffx)));
		$full = sboxr_defang($prefx.", ".strtoupper($suffx));
		$underscore = str_replace(" ","_",sboxr_defang(strtolower($prefx." ".$suffx)));
		
		$sbox_drupal_new_database = $sbox_drupal_new_user = sboxr_drupal_dbprefix($prefx,$suffx, 8);
		$sbox_drupal_new_password = wp_generate_password(8);
		
		$sboxr_drupal_dbprefix = sboxr_drupal_dbprefix($prefx,$suffx, 4)."_";
		$tid = intval($tid);

		$from = array('{machine}', '{full}', '{underscore}', '{sboxr_drupal_dbprefix}', '{filedir}', '{longphrase}', '{prefx}', '{dbname}', '{dbuser}', '{dbpass}');
		$to = array($machine, $full, $underscore, $sboxr_drupal_dbprefix, $sboxr_path, $longphrase, $prefx, $sbox_drupal_new_database, $sbox_drupal_new_user, $sbox_drupal_new_password);
		$output = str_replace($from, $to, $source);

		// $ln_call = "ln -s ".$_SERVER["DOCUMENT_ROOT"]." ".$_SERVER["DOCUMENT_ROOT"]."/".$sboxr_path;

		$dr = $settings['drupal_install'];
		if (!symlink($dr, $dr."/".$sboxr_path)) {
			print "<br/>Tried to symlink ".$dr." to ".$dr."/".$sboxr_path;
			print "<br/>What is there: ".readlink($dr."/".$sboxr_path);
		}
		else {
			print "<br/>Success: ".readlink($dr."/".$sboxr_path);
		}

		// makedir
		$structure = './sites/www.itercom.org.'.$sboxr_path; // future version of this will have a) a variable in lieu of "www.itercom.org" and b) a way to name other sites, other servers.
		if (!mkdir($structure, 0777, true)) {
			print 'Failed to create folders...<br/>';
		}
		else {
			$filename = $structure.'/settings.php';
			$fpc = file_put_contents($filename, $output);		
		}
		if (!mkdir($structure."/files", 0777, true)) {
			print 'Failed to create files folders...<br/>';
		}

		// drupal boostrap - call for tables and then make them

		$databases = array('default' => array( 'default' => array(
			'driver' => 'mysql',
			'database' => 'd',
			'username' => 'u',
			'password' => 'p',
			'host' => 'h',
			'collation' => 'utf8_general_ci',
		)));
		
		// populate db_prefix with a discovery of the database tables in the baseline
		$db_prefix = array();
		
		$dbconnect = mysql_connect($databases['default']['default']['host'], $databases['default']['default']['username'], $databases['default']['default']['password']);
		if ($dbconnect) {
			mysql_select_db($databases['default']['default']['database']);
			
			// make a database
			$create_database = "CREATE DATABASE '".$sbox_drupal_new_database."'";
			if ($result = mysql_query($create_database)) {			
				// create a new user
				$create_user = "CREATE USER '".$sbox_drupal_new_user."'@'".$databases['default']['default']['host']."' IDENTIFIED BY PASSWORD '".$sbox_drupal_new_password."'";
				if ($result = mysql_query($create_user)) {
					// if that succeeds, grant them privileges 
					$grant_permission = "GRANT ALL PRIVILEGES ON '".$sbox_drupal_new_database."'.* TO '".$sbox_drupal_new_user."'@'".$databases['default']['default']['host']."' IDENTIFIED BY PASSWORD '".$sbox_drupal_new_password."'";
					if ($result = mysql_query($grant_permission)) {
						// get the available database tables to populate the new database
						$result = mysql_query("show tables"); 
						while ($table = mysql_fetch_array($result)) { 
							$db_prefix[] = $table[0];
						}
			
						foreach ($db_prefix as $table) {
							if(sboxr_copy_table('drup_'.$table, $sboxr_drupal_dbprefix.$table, $sbox_drupal_new_database, $dbconnect)) {
								echo "success - $sboxr_drupal_dbprefix$table created\n";
							}
							else {
								echo "failure $sboxr_drupal_dbprefix$table already exists\n";
							}
						}
					}			
				} 
			}	
		}
	}
}

/*
register settings held inside of wp_options
register_setting($group, $name, $typing);
register_setting('general', 'sboxr_drupal_[thing]', 'sboxr_drupal_sanitize_general');
*/

function sboxr_drupal_sanitize_general($arg) {
	if (is_string($arg)) {
		return $arg;
	}
	else {
		return "n/a";
	}
}

// saving all of the variables happens elsewhere and pipes into sboxr_drupal_put_variables
function sboxr_drupal_put_variables($key, $value) {
	// save all of the ones with the sboxr_drupal prefix
	update_option($key, $value);
}

// produce for elements to show off all of the variables
function sboxr_drupal_general_tab_form($args) {
	// form naming convention: [module]_[key phrase] -- drupal_baseline is the "baseline" read in for the "drupal" sandboxer module
	$output = '<tr>';
	$output .= '<td>Baseline</td>';	
	$output .= '<td><input name="sboxr_drupal_baseline" size="60" type="text" value="'.esc_attr(sboxr_either_arg('sboxr_drupal_baseline', @$args)).'"/></td>';
	$output .= "</tr>";
	$output = '<tr>';
	$output .= '<td>Install path</td>';	
	$output .= '<td><input name="sboxr_drupal_install" size="60" type="text" value="'.esc_attr(sboxr_either_arg('sboxr_drupal_install', @$args)).'"/></td>';
	$output .= "</tr>";

	return $output;
}


function sboxr_drupal_dbprefix($prefx, $suffx, $length = 3) {
	$pre = round($length / 2);
	$suf = $length - $pre;

	$output = "";
	$words = explode(" ", strtolower(sboxr_defang($prefx)));
	$suffx = strtolower($suffx);
	if (sizeof($words) == 1) {
		$output = substr($prefx, 0, $pre).substr($suffx, 0, $suf);
		return strtolower($output);
	}
	foreach ($words as $word) {
		$output .= substr($word, 0, 1);
		$incr++;
	}
	$output .= substr($suffx, 0, ($length - $incr));
	return strtolower($output);
}

function sboxr_copy_table($from_table, $from_db, $to_table, $to_db, $dbconnect) {
    if(sboxr_table_exists($to_table, $to_db)) {
        $success = false;
    }
    else {
        mysql_query("CREATE TABLE $to_db.$to_table LIKE $from_db.$from_table");
		mysql_query("INSERT INTO $to_db.$to_table SELECT * FROM $from_db.$from_table");
        $success = true;
    }
    return $success;
}
 
function sboxr_table_exists($tablename, $database = false) {
    if(!$database) {
        $res = mysql_query("SELECT DATABASE()");
        $database = mysql_result($res, 0);
    }
    
    $res = mysql_query("
        SELECT COUNT(*) AS count
        FROM information_schema.tables
        WHERE table_schema = '$database'
        AND table_name = '$tablename'
    ");
    
    return mysql_result($res, 0) == 1;
}


?>