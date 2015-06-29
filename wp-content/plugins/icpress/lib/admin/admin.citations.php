<?php

// Restrict to those who can edit posts and the implied authority
if ( current_user_can('edit_posts') )
{	
	if ((isset( $_REQUEST['edit'] )) && ($_REQUEST['edit'] == 'citation')) {
		if ((isset( $_REQUEST['entry_id'] )) && ($entry_id = $_REQUEST['entry_id'])) {
			// proceed "entry_id" is a test
			
			if ($_REQUEST['updated'] !== "true") {
				// get the data from the db
				$data = icp_get_edit_item($entry_id);			
			}			
			else {
				// get the data from the form input
				$data = $_REQUEST;
			}
		}
		if ($_REQUEST['updated'] === "true") 
		{
			if (check_admin_referer('update_icpress_'.$entry_id)) 
			{
				icp_put_edit_item($_REQUEST);
			}
		}
			?>
		<div id="icp-ICPress" class="wrap">
		<!-- ICPRESS EDIT A CITATION -->		
		<form method="POST">
		<? 
		print icp_make_form_item($data); 
		wp_nonce_field( 'update_icpress_'.$entry_id); 	
		?>	
		<input type="submit" value="Update Record" />
		</form>	
		</div>	
	<?
	}
	
	if ((isset( $_GET['add'] )) && ($_GET['add'] == 'citation')) {
	?>
	
		<div id="icp-ICPress" class="wrap">
		<form>
		<!-- ICPRESS EDIT A CITATION -->		
		This is the add screen
		</form>
		</div>	
	<?
	}	
}


function icp_make_form_item($input) 
{
	$row = "";
	$row .= '<input name="id" type="hidden" value="'.esc_attr(@$input['id']).'"/>';
	$row .= '<input name="updated" type="hidden" value="true"/>';	
	$row .= '<input name="page" type="hidden" value="ICPress"/>';	
	$row .= '<input name="edit" type="hidden" value="citation"/>';
	$row .= '<input name="citation" type="hidden" value="true"/>';
	
	$row .= '<table>';
	$row .= '<tr><td>ID#</td><td><input name="id" type="hidden" value="'.esc_attr(@$input['id']).'"/>'.@$input['id'].'</td></tr>'."\n";
	
	// regular fields
	$fields = icp_get_fields();
	
	foreach ($fields as $label => $name) {
		if (!(function_exists('icp_field_item_'.$name))) {			
			$row .= "<tr><td>".$label."&nbsp;</td><td>".icp_field_item_generic($label, $name, @$input[$name])."</td></tr>";
		}
		else {
			// we do have an alternative...			
			$row .= call_user_func_array('icp_field_item_'.$name, array($label, $name, $input));
		}
	}	
	
	$row .= "</table>\n";
	
	return $row;
}


function icp_field_item_json($label, $db_name, $input) {
	$index = 0;
	$row = "";
	$row .= '<tr><td colspan="2" style="border: 1px solid #dddddd;"><strong>'.$label.'</strong><br/><table>';

	if ($json = json_decode($input[$db_name], true)) 
	{
		// if yes, then this is a valid array
		// json gets split into three fields - json_n[] (name), json_v[] (value), json_d[] (delete?)
		foreach ($json as $name => $value) 
		{
			if (is_array($value)) {
				$row .= icp_field_item_json_in_array($db_name, $name, $value, array($index));
			}	
			else {
				$row .= '<tr>';
				$row .= '<td><input name="'.$db_name.'_n_'.$index.'" size="12" type="text" value="'.esc_attr($name).'"/></td>';
				$row .= '<td><input name="'.$db_name.'_v_'.$index.'" size="30" type="text" value="'.esc_attr($value).'"/>&nbsp;';
				$row .= '<nobr>Delete? <input name="'.$db_name.'_'.$name.'_delete" type="checkbox" value="checked"/></nobr></td>';				
				$row .= "</tr>\n";	
			}
			$index++;
		}	
	}
	else {
		// we pull from form input
		// if no, then this is from a form
		// json gets split into three fields - json_n[] (name), json_v[] (value), json_d[] (delete?)
		
		foreach (@$input as $key => $value) 
		{
			if (strpos($key, "_n_") > 0) // we have one 
			{
				$index_raw = str_replace($db_name."_n_", "", $key);
				$indexes = explode("_", $index_raw);
				
				if ($indexes[0] > $index) 
				{
					$index = $indexes[0];
				}

				if (@$input[$db_name.'_'.$index_raw.'_delete'] !== 'checked')
				{
					$row .= '<tr>';
					$row .= '<td>'.str_repeat("&nbsp; ", sizeof($indexes)).'<input name="'.$db_name.'_n_'.$index_raw.'" size="12" type="text" value="'.esc_attr(@$input[$db_name.'_n_'.$index_raw]).'"/></td>'."\n";
					$row .= '<td><input name="'.$db_name.'_v_'.$index_raw.'" size="30" type="text" value="'.esc_attr(@$input[$db_name.'_v_'.$index_raw]).'"/>&nbsp;';
					$row .= '<nobr>Delete? <input name="'.$db_name.'_'.$index_raw.'_delete" type="checkbox" value="checked"/></nobr></td>'."\n";	
					$row .= "</tr>\n";	
				}
			}								
		}
		$index++;	
	}

	for ($x = 0; $x < 3; $x++) {
		$row .= '<tr>';
		$row .= '<td><input name="'.$db_name.'_n_'.$index.'" size="16" type="text" value=""/></td>';
		$row .= '<td><input name="'.$db_name.'_v_'.$index.'" size="30" type="text" value=""/>&nbsp;';
		$row .= '</td>';				
		$row .= '</tr>';
		$index++;
	}
	$row .= "</table></td></tr>\n";
	$row .= '<input name="'.$db_name.'_max" type="hidden" value="'.$index.'"/>';	
	
	return $row;
}
	

function icp_field_item_json_in_array($db_name, $name, $value, $indexes = array()) {
	// array size?
	$array_size = sizeof($value);
	$super_index = implode('_', $indexes);
	
	$subindex = 0;
	// the parent column
	$row = '<tr style="background-color: #dddddd; border: 1px solid #cccccc;">';
	$row .= '<td valign="top"><input name="'.$db_name.'_n_'.$super_index.'" size="12" type="text" value="'.esc_attr($name).'"/></td>';
	$row .= '<td><table>';
	
	foreach ($value as $subkey => $subvalue) 
	{
		// each column
		$row .= '<tr>';
		$row .= '<td> &nbsp; <input name="'.$db_name.'_n_'.$super_index.'_'.$subindex.'" size="20" type="text" value="'.esc_attr($subkey).'"/></td>';		
		if (is_array($subvalue)) 
		{							
			$row .= '<td><table>'.icp_field_item_json_in_array($db_name, $subkey, $subvalue, array_merge($indexes, array($subindex))).'</table></td>';			
		}
		else 
		{
			$row .= '<td> &nbsp; <input name="'.$db_name.'_v_'.$super_index.'_'.$subindex.'" size="20" type="text" value="'.esc_attr($subvalue).'"/></td>';					
		}	
		$row .= '</tr>';					
		$subindex++;
	}
	// two new rows
	for ($x = 1; $x <= 2; $x++) 
	{
		// each column
		$row .= '<tr>';
		$row .= '<td> &nbsp; <input name="'.$db_name.'_n_'.$super_index.'_'.$subindex.'" size="20" type="text" /></td>';									
		$row .= '<td> &nbsp; <input name="'.$db_name.'_v_'.$super_index.'_'.$subindex.'" size="20" type="text" /></td>';				
		$row .= '</tr>';					
		$subindex++;
	}				
	
	$row .= '</table></td>';				
	$row .= '</tr>';	

	return $row;
}


function icp_field_item_generic($label, $name, $value) {
	return '<input name="'.$name.'" type="text" size="40" value="'.esc_attr($value).'"/>'."\n";
}

function icp_get_fields() 
{
	$fields = array(
		'Key' => 'item_key', 
		'Retrieved' => 'retrieved', 
		"Author" => 'author', 
		"Zpdate" => 'zpdate', 
		"Year" => 'year', 
		"Title" => 'title', 
		"Item Type" => 'itemType', 
		"Link Mode" => 'linkMode', 
		"citation" => 'citation', 
		"Style" => 'style', 
		"Num Children" => 'numchildren', 
		"Parent" => 'parent',
		"JSON" => 'json'
	);
	return $fields;	
}

// get a record
function icp_get_edit_item ($entry_id)
{
	global $wpdb;
	$query = "SELECT * FROM ".$wpdb->prefix."icpress_zoteroItems WHERE id=".intval($entry_id);
	
	if ($item = $wpdb->get_row($query, ARRAY_A )) {
		return $item;
	}
	else {
		return false;
	}
}

// save the records
function icp_put_edit_item ($input)
{
	global $wpdb;
	$fields = icp_get_fields();
	$sql = "UPDATE ".$wpdb->prefix."icpress_zoteroItems SET ";
	$comma = "";

	// non-json piece
	foreach ($fields as $label => $field) {	
		if ($field != 'json') 
		{
			$sql .= $comma.$field." = '".$input[$field]."'";	
			$comma = ", ";
		}	
	}
	
	// json piece
	$json = array();
	$jdon_form_elements = array();

	foreach (@$input as $key => $value) 
	{
		if (strpos($key, "son_n_") > 0) // we have one 
		{
			$index_raw = str_replace("json_n_", "", $key);
			$indexes = explode("_", $index_raw);

			if (@$input['json_'.$index_raw.'_delete'] !== 'checked')
			{
				$evalstring = '$json';
				foreach ($indexes as $indexkey => $index) {
					$evalcell[$key] .= "['".$input['json_n_'.$index]."']";
				}
			}
		}								
	}
	foreach ($evalcell as $evalkey => $evalvalue) {
		$evalkey = icp_safe_eval($evalkey);
		$evalvalue = icp_safe_eval($evalvalue);
		
		$json_value = str_replace("_n_", "_v_", $evalkey);
		$evalvalue = str_replace("''", "0", $evalvalue);
		
		$evalstring = "\$json".$evalvalue." = \$input['".$json_value."'];";
		eval($evalstring);
	}
	
	$sql .= $comma."json = '".json_encode($json)."'";
	$sql .= " WHERE id = ".$input['id'];
	
	print $sql;
	
	if ($wpdb->query( $sql )) {
		return true;
	}	
	return false;
}

function icp_safe_eval($string) {
	while(strpos($string, '()') !== false) {
		$string = str_replace('()', '', $string);
		$string = trim($string, '+-/*');
	}

	return $string;
}







