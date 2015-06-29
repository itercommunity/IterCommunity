<?php
/*
Plugin Name: WordPress Table Rename
Plugin URI: http://www.seoegghead.com/software/wordpress-table-rename.seo
Description: Renames WordPress tables to mitigate some SQL injection attacks.
Author: SEO Egghead, Inc.
Version: 1.2 for WP 2.x
Author URI: http://www.seoegghead.com/
*/ 

add_option('TR_new_table_prefix', 'wp' . rand(1, 9999) . '_');
add_action('admin_menu', 'TR_admin_menu');

function TR_admin_menu() {
	add_submenu_page('options-general.php', 'Table Rename', 'Table Rename',
	10, __FILE__, 'TR_submenu');
}

function TR_escape_db_identifier($str) {
	$stripped = preg_replace('/[^A-Z0-9_.]/i', '', $str);
	return preg_replace('/(.+?)(\.|$)/', '`\\1`\\2', $stripped);
}	

function TR_escape_like($str) {
	global $wpdb; return $wpdb->escape(addcslashes($str, '_%'));
}

function TR_submenu() {
	global $wpdb, $TR_qstr;

	if($_REQUEST['create_tables']){
		
		$new_prefix = $_REQUEST['new_table_prefix'] . '_';
		update_option('TR_new_table_prefix', $new_prefix);
		
		$table_list = 
		$wpdb->get_results("SHOW TABLES LIKE '". TR_escape_like($new_prefix) 
		. "%'", ARRAY_N);		
		
		if($table_list) {
			echo '<div class="updated fade"><p><strong>
			Prefix already exists on at least one table; please 
			select a different table prefix.
			</strong></p></div>';
		} else {
			$success = create_new_tables($new_prefix);
			if ($success === true) {
				$success = update_option_table($new_prefix);
				if ($success === true) {
					$success = update_usermeta_table($new_prefix);
				}
			}
			echo '<div class="wrap">
			<h3>We executed the following queries ...</h3></div>
			<div class="updated fade" style="height:75px;  overflow: auto">
			<strong>' . $TR_qstr . '</strong>
			</div>';
			if($success === true) {
				echo '<div class="updated fade"><p><strong>
				New WordPress tables with prefix "' 
				. $new_prefix . '" successfully created.
				</strong></p></div>';
			} else {
				echo $success;	
			}
		}
	} elseif ($_REQUEST['update_table_prefix']) {
		
		$new_prefix = get_option('TR_new_table_prefix');
		// To keep the below easy-to-read.
		$old_prefix = $GLOBALS['table_prefix'];
		
		$new_table_list = $wpdb->get_results("SHOW TABLES LIKE '"
		. TR_escape_like($new_prefix) ."%'", ARRAY_N);		
		$old_table_list = $wpdb->get_results("SHOW TABLES LIKE '"
		. TR_escape_like($old_prefix) ."%'", ARRAY_N);		
		
		if(sizeof($new_table_list) != sizeof($old_table_list) ||
		 $new_prefix == $old_prefix) {
			echo '<div class="updated fade"><p><strong>
			New tables not created; please complete step 1 
			before changing table prefix.
			</strong></p></div>';
		} else {
			$config_path = '../wp-config.php';
			$_error_str = 
			"<div class=\"updated fade\"><p><strong>
			We could not automatically change prefix value; 
			please edit wp-config.php 
			and set \$table_prefix to \"$new_prefix\".
			</strong></p></div>";
			if (file_exists($config_path) && is_writable($config_path)) {
				$config_file = file_get_contents($config_path);
				$new_config_file = 
				preg_replace('#\$table_prefix\s*=\s*\'(.+)\';#',
				 "\$table_prefix = '$new_prefix';", $config_file);
				if ($config_file == $new_config_file) {
					echo $_error_str;
				} else {
					$result = 
					file_put_contents($config_path, $new_config_file);	
					if ($result !== FALSE){ 
						echo '<div class="updated fade"><p><strong>
						Your wp-config.php file has been successfully updated.
						</strong></p></div>'; }
					else { echo $_error_str; }
				}		
			} else {
				echo $_error_str;
			}	
		}	
	}
	?>
	<script type="text/javascript">
	function checktext() {
		document.getElementById('create_tables_submit1').disabled = 
		(document.getElementById('original_prefix1').value ==
		 document.getElementById('new_prefix1').value);
		 
	}
	function settextfield(size, value){
		if(value.search(/_/) != -1){
			var y=document.getElementById("new_prefix1").value;
			alert('Underscores are reserved for last character of table name.');
			document.getElementById("new_prefix1").value=y.replace(new RegExp('_',"g"), '');			
		}		
		
		if(value.search(/\W/) != -1){
			var y=document.getElementById("new_prefix1").value;
			alert('Special characters are not permitted in table name.');
			document.getElementById("new_prefix1").value=y.replace(new RegExp('\\W',"g"), '');			
		}			
		size =  (value.length );
		size = size + '' + 'em';
		return size;
	}
	</script>

	<div class="wrap">
	<h2>Table Rename Setup</h2>
	<h3>Step 1: Generate New Tables with Preexisting Data</h3>	

	Current Table Prefix:<br />
	<form>
	<input size="<?php echo strlen($GLOBALS['table_prefix'])+2?>" type="text" id="original_prefix1" 
	style="min-width:6em;" value=
	"<?php echo str_replace('_', '', htmlspecialchars($GLOBALS['table_prefix'], ENT_QUOTES)) ?>" readonly> <span style="font-size:large;">_</span>
	</form>
	<br />
	New Table Prefix:
	
	<form name="generate_new_tables" 
	action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
	<input type="text" size="<?php echo strlen(get_option('TR_new_table_prefix'))+2?>" style="min-width:6em;"
	id="new_prefix1"

	onkeyup="checktext(); this.style.width = settextfield(this.style.width, this.value);" 

	id="new_prefix1" 
	name="new_table_prefix" value="<?php if(get_option('TR_new_table_prefix') != $GLOBALS['table_prefix'])
	echo str_replace('_', '', htmlspecialchars(get_option('TR_new_table_prefix'), ENT_QUOTES))?>" >

	<span style="font-size:large;">_</span>
	<br /><br />
	<input type="submit" id="create_tables_submit1"
	onclick="
		 if(document.getElementById('new_prefix1').value == ''){
		 	alert('Please enter a valid table name.');
		 	return false;
		 }"
	<?php echo
    (get_option('TR_new_table_prefix') ==
    $GLOBALS['table_prefix']) ? 'disabled' : ''
	?>  
	name="create_tables"  value="Generate New Tables">
	</form>
	
	<h3>Step 2: Change $table_prefix in wp-config.php to Use New Tables</h3>

	<form name='change_table_prefix' 
	action="<?php echo $_SERVER['REQUEST_URI']; ?>"
	 method="post">
	<input type="submit" name="update_table_prefix" 
	 value="Change $table_prefix">
	</form>

	<?php
	TR_show_link();
	echo '</div>';	
}

function TR_show_link() {
    ?>
	<div style="float:right; position:relative; top:-70px;">
	<a href="http://www.seoegghead.com/software/wordpress-table-rename.seo"
     style="text-decoration:none;" target="_blank">
	<?php
	if(preg_match('#MSIE#', $_SERVER['HTTP_USER_AGENT']) == 0) {
	?>
	<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAAUCAMAAA
	BxjAnBAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAADBQTFRFz6Ol
	opmRs21w48/P+Pb218bF9Orr6OTit7CpqVpdqaGZrmJlvYCD0ry769vc////wFM5SwAAAB
	B0Uk5T////////////////////AOAjXRkAAAJLSURBVHjatFXtDiMhCERF3Q/R93/bGxDX
	Xu5Pr2lJNk7pVHQYujS+FImov09Ng75VmM4zvU+V7xVu583vU7sVZo/CG7+kDRcL27qs4J
	VWfJ+N/+GMzdi7cAQVhfm4PEJlcXjUkVf64CHxtogySnN8N64Ppn6erc10Gd3zERQn3DGN
	ZIQGKq5LQ8ITnB+Y6073dK5oTBtDsxWUH3gmXGlF33nytFIFhfkKV68avSpOQDhLwhmk9t
	6vECoKUEpJtHA8bwFWg5T7jHlifZSSsXnBORtwArWWZIEdREUBvYAK/1MJ4bC6aAhumRkQ
	ResRLusW1oKmDJxBj1pMJ3OmCiET36Wdkadxon1XwNcc6+G76tTFfT+ptCWVkVAwGb7qFQ
	5lQYRDm1JdRhTPy8Tz6EPNUvVsaqpoHbh5dqEVW+e5QC9KuY1KnK+nxTKfoCLg8rqTrmkW
	xAZ/FfOjDxWhuxC2RuuI0oXcGTNldKfSGhuVVh8PXNz+iHS1Yj4Ubf+a/ZaquLgQKqd1w8
	cIxSrGFHqR33Q4lbIHJIWsQeYn9fqBVWA3lauRBWNcb6ymOAootnO5jNregu/ipCfQDWIH
	1c2wWk6Ze2oSP/CApx78Mh3McxXzj7cdLa0uo8qpV/KQsoes7Jk0Kh0rEvR98CjiMDMG3w
	MKioFqhqWJo3qoWYtt9TyiD1mw8U7Psfjoj5m9X2Ob5b9fKp+9A1+KiU/zbwvn6ZqozpQH
	958X3j6Ly2c2pj8vXB5/VLyCFi7j9zeWGQSvF/IP6ZMLjz8CDACmemOuUH7ZzQAAAABJRU
	5ErkJggg==" />
	<?php } ?>
	<br />
	<small>Click here for plugin documentation.</small>
	</a>
	<br />
	<small>Got Questions or Feedback? 
	<a style="text-decoration:none;" 
	href=
"http://www.seoegghead.com/about/contact-us.seo?subject=Table+Rename+Feedback"
	target="_blank">
	Click here.
	</a>
	</small>	
	<br />
	<small>By using this plugin you agree to 
	<a style="text-decoration:none;" 
	href="http://www.seoegghead.com/software/free-software-disclaimer.seo"
	target="_blank">
	this simple disclaimer.
	</a>
	</small>	
	</div>
	<?php
}

function create_new_tables($new_prefix) {
	
	global $wpdb, $TR_qstr;
	$old_prefix = $GLOBALS['table_prefix'];
	
	$table_list = $wpdb->get_results("SHOW TABLES LIKE '".
	TR_escape_like($old_prefix) . "%'", ARRAY_N);
	
	foreach($table_list as $table) {
		$new_table = preg_replace("#^" . preg_quote($old_prefix, '#') 
		. "#", $new_prefix, $table[0]);
		$createquery = 'CREATE TABLE ' . TR_escape_db_identifier($new_table) 
		. ' LIKE ' . TR_escape_db_identifier($table[0]);  
		$insertquery = 'INSERT INTO ' . TR_escape_db_identifier($new_table) 
		. ' SELECT * FROM ' . TR_escape_db_identifier($table[0]);
		$TR_qstr .= $createquery . '<br />' . $insertquery  . '<br />';
		$result = $wpdb->query($createquery);
		if($result === FALSE ){
			return 
			'<div class="updated fade"><p><strong>
			Failed to create new tables.
			</strong></p></div>';
		}
		else{
			$result = $wpdb->query($insertquery);
			if($result === FALSE){
				return '<div class="updated fade"><p><strong>
				Failed to insert data into new tables.
				</strong></p></div>';
			}
		}
	}
	return true;	
}

function update_option_table($new_prefix) {
	global $wpdb, $TR_qstr;
	// To keep the below easy-to-read.
	$old_prefix = $GLOBALS['table_prefix'];
	$updateoptionsquery = "UPDATE " 
	. TR_escape_db_identifier($new_prefix . 'options') 
	. " SET   option_name = '" 
	. $wpdb->escape($new_prefix . 'user_roles') . "'"
	. " WHERE option_name = '" 
	. $wpdb->escape($old_prefix . 'user_roles') . "'";
	$TR_qstr .= $updateoptionsquery . '<br />';
	$update_result = $wpdb->query($updateoptionsquery);
	if($update_result === FALSE){
		return $success = 
		'<div class="updated fade"><p><strong>
		Failed to update prefix refences in ' . $new_options . ' table.
		</strong></p></div>';				
	}
	return true;
}

function update_usermeta_table($new_prefix){
	global $wpdb, $TR_qstr;
	// This is OK because we're doing something repetitive/annoying/safe below.
	$escaped_old_table_prefix = 
	$wpdb->escape($old_prefix = $GLOBALS['table_prefix']);
	$getusermetaquery = 
	"SELECT meta_key FROM " . TR_escape_db_identifier($new_prefix . 'usermeta') 
	. " WHERE meta_key = 
	'{$escaped_old_table_prefix}user_level' OR meta_key =
	'{$escaped_old_table_prefix}capabilities' OR meta_key =
	'{$escaped_old_table_prefix}autosave_draft_ids'";
	$meta_arr = $wpdb->get_col($getusermetaquery);
	if(sizeof($meta_arr) > 0){
		foreach($meta_arr as $meta){
			
			$new_metakey_name = 
			preg_replace("#^" . preg_quote($old_prefix, '#')
			. "#", $new_prefix, $meta);
						
			$update_meta_query = "UPDATE " 
			. TR_escape_db_identifier($new_prefix . 'usermeta') 
			. "  SET   meta_key = '" . $wpdb->escape($new_metakey_name) 
			. "' WHERE meta_key = '" . $wpdb->escape($meta) . "'";
			$TR_qstr .= $update_meta_query . '<br />';
			$res = $wpdb->query($update_meta_query);
			if($res === FALSE){
				return 
				'<div class="updated fade"><p><strong>
				Failed to update prefix references in ' 
				. $new_prefix . 'usermeta' . ' table
				</strong></p></div>';	
			}
		}
	}
	else {
		return 
		'<div class="updated fade"><p><strong>
		No prefix references to be updated in '
		 . $new_prefix . 'usermeta' . ' table
		</strong></p></div>';
	}	
	return true;
}
?>