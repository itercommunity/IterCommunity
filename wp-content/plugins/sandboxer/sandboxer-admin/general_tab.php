<?php

print "HELLO WORLD - HELLO THERE!";

// do the form

// things we need to know 
// hook into all activated modules
// - install directory for each activated module 
// - install server for each activated module (make don't display)
// - source directory for each set of optional elements

$module_list = sboxr_modules_discover();
$module_status = sboxr_modules_get();

foreach ($module_list as $key => $module) {
	if ((array_key_exists($key, $module_status)) && ($module_status[$key]['status'] == 1)) {
		$modules[$key]['module'] = $key;				
		$modules[$key]['title'] = $module['title'];
		$modules[$key]['description'] = $module['description'];			
	}
}

// iterate through active modules
foreach ($modules as $key => $value) {
	// fish out variables
	// fish out general_tab_form
	if (function_exists('sboxr_'.$key.'_general_tab_form')) {
		// get and run the form
		call_user_func('sboxr_'.$key.'_general_tab_form', $_POST);
	}
}

// do the saving step here

?>
<div class="wrap">
	<h2><?php _e('Sandboxer Options'); ?></h2>
<?php

print '<form action="'.$_SERVER['REQUEST_URI'].'" method="POST">';
print "<table>";

foreach ($modules as $key => $value) {
	// fish out variables
	// fish out general_tab_form
	if (function_exists('sboxr_'.$key.'_general_tab_update')) {
		// get and run the form
		call_user_func('sboxr_'.$key.'_general_tab_update', $_POST);
	}
}

print "</table>";
print '<input type="hidden" name="action" value="edit" />';
print '<input type="submit" value="Update Settings" />';
print "</form>";

?>
