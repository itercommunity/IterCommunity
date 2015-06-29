<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<div class="wrap">
	<h2><?php _e('Available Modules'); ?></h2><br/>
	<?php 

	$action_url = $_SERVER['REQUEST_URI'];
	
	?>
	<form method="post" action="<?php print $action_url; ?>">
		<?php settings_fields('sandboxer-modules'); ?>
		These are the modules available for use by project creators. By checking or unchecking the modules, you can choose which modules are available.
		<table class="form-table">
			<tbody>
			<?php 
			$module_list = sboxr_modules_discover();
			// $return_modules[$module] = array('title' => $title[0][0], 'description' => $description[0][0], 'location' => $file);
			
			$module_status = sboxr_modules_get();
			foreach ($module_list as $key => $module) {
				$index++;
				$input['module'] = $key;				
				$input['status'] = $module_status[$key]['status'];
				$input['title'] = $module['title'];
				$input['description'] = $module['description'];			
				print sboxr_modules_row($index, $input);
			}
			
			?>
			</tbody>
		</table>	
		<input type="hidden" name="sboxr_module_index" value="<?php echo $index; ?>" />
		<input type="hidden" name="action" value="edit" />
		<?php // submit_button(); ?>
		<input type="submit" value="Update Modules" />
	</form>
<?php


function sboxr_modules_row($index, $input = array()) {
	$zebra = "zebra-".($index % 2);
		
	$row = '<tr class="'.$zebra.'">';
	$row .= '<td><input name="module_'.$index.'" type="checkbox" value="'.esc_attr(@$input['module']).'"'.((@$input['status'] != "1") ? "" : " checked").'/></td>';
	$row .= '<td nowrap><strong>'.@$input['title'].'</strong><br/>'.@$input['description'].'</td>';
	$row .= "</tr>";
	return $row;
}

?>