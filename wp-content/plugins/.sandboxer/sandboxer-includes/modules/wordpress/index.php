<?php
/*
Plugin Module: WordPress
Description: This plugin creates subsites for WordPress	
Author: Shawn DeWolfe - mikedewolfe@gmail.com
Version: 0.1
Author URI: http://etcl.uvic.ca/TBA
*/

$settings['baseline'] = "./sites/baseline/settings.php";

/* share for the most part */

/* do the form management */

if ($good != $replaced) {
	make_community($_GET['prefx'], $_GET['suffx'], $_GET['longphrase'], $_GET['tid']);
}
else {
	// error
}

/* the actual functions */

function sbox_wordpress_make_subsite($prefx, $suffx, $longphrase, $tid) {
	global $settings;

	if ($source = @file_get_contents($settings['baseline'])) {
		// good to go!

		$machine = str_replace(" ","-",sbox_defang(strtolower($prefx." ".$suffx)));
		$full = sbox_defang($prefx.", ".strtoupper($suffx));
		$underscore = str_replace(" ","_",sbox_defang(strtolower($prefx." ".$suffx)));
		$filedir = str_replace(" ","_",sbox_defang(strtolower($prefx." ".$suffx)));
		// $sbox_wordpress_dbprefix = sbox_wordpress_dbprefix($prefx,$suffx)."_";
		$tid = intval($tid);

		$from = array('{machine}', '{full}', '{underscore}', '{sbox_wordpress_dbprefix}', '{filedir}', '{tid}', '{longphrase}', '{prefx}', '{lat}', '{long}');
		$to = array($machine, $full, $underscore, $sbox_wordpress_dbprefix, $filedir, $tid, $longphrase, $prefx, $lat, $long);
		$output = str_replace($from, $to, $source);

		// $ln_call = "ln -s ".$_SERVER["DOCUMENT_ROOT"]." ".$_SERVER["DOCUMENT_ROOT"]."/".$filedir;

		$dr = "."; // $_SERVER["DOCUMENT_ROOT"];
		if (!symlink($dr, $dr."/".$filedir)) {
			print "<br/>Tried to symlink ".$dr." to ".$dr."/".$filedir;
			print "<br/>What is there: ".readlink($dr."/".$filedir);
		}
		else {
			print "<br/>Success: ".readlink($dr."/".$filedir);
		}

		// makedir
		$structure = './sites/www.itercom.org.'.$filedir;
		if (!mkdir($structure, 0777, true)) {
			print 'Failed to create folders...<br/>';
		}
		else {
			$filename = $structure.'/settings.php';
			$fpc = file_put_contents($filename, $output);		
			// mkdir($structure."/files", 0777, true);
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
			foreach ($db_prefix as $table) {
				if(copy_table('drup_'.$table, $sbox_wordpress_dbprefix.$table)) {
					echo "success - $sbox_wordpress_dbprefix$table created\n";
				}
				else {
					echo "failure $sbox_wordpress_dbprefix$table already exists\n";
				}
			}		
		}
	}
}

function sbox_wordpress_dbprefix($prefx, $suffx) {
	$output = "";
	$words = explode(" ", strtolower(sbox_defang($prefx)));
	$suffx = strtolower($suffx);
	if (sizeof($words) == 1) {
		$output = substr($prefx, 0, 3).substr($suffx, 0, 2);
		return $output;
	}
	foreach ($words as $word) {
		$output .= substr($word, 0, 1);
	}
	$output .= substr($suffx, 0, 2);
	return strtolower($output);
}

?>