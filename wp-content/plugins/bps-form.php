<?php

add_action ('bp_before_directory_members_tabs', 'bps_add_form');
function bps_add_form ()
{
	global $post;

	$page = $post->ID;
	if ($page == 0)
	{
		$bp_pages = bp_core_get_directory_page_ids ();
		$page = $bp_pages['members'];
	}
	$len = strlen ((string)$page);

	$args = array (
		'post_type' => 'bps_form',
		'orderby' => 'ID',
		'order' => 'ASC',
		'nopaging' => true,
		'meta_query' => array (
			array (
				'key' => 'bps_options',
				'value' => 's:9:"directory";s:3:"Yes";',
				'compare' => 'LIKE',
			),
			array (
				'key' => 'bps_options',
				'value' => "s:6:\"action\";s:$len:\"$page\";",
				'compare' => 'LIKE',
			),
		)
	);

	$posts = get_posts ($args);
	foreach ($posts as $post)
	{
		$meta = bps_options ($post->ID);
		$template = $meta['template'];
		bps_display_form ($post->ID, $template, 'directory');
	}
}

add_action ('bps_display_form', 'bps_display_form', 10, 3);
function bps_display_form ($form, $template='', $location='')
{
	if (!function_exists ('bp_has_profile'))
	{
		printf ('<p class="bps_error">'. __('%s: The BuddyPress Extended Profiles component is not active.', 'bps'). '</p>',
			'<strong>BP Profile Search '. BPS_VERSION. '</strong>');
		return false;
	}

	$meta = bps_options ($form);
	if (empty ($meta['field_name']))
	{
		printf ('<p class="bps_error">'. __('%s: Form %d was not found, or has no fields.', 'bps'). '</p>',
			'<strong>BP Profile Search '. BPS_VERSION. '</strong>', $form);
		return false;
	}

	$version = BPS_VERSION;
	if (empty ($template))  $template = bps_default_template ();
	bps_set_request_data ($form, $location);

	echo "\n<!-- BP Profile Search $version $form $template $location -->\n";
	$found = bp_get_template_part ($template);
	if (!$found)  printf ('<p class="bps_error">'. __('%s: The form template "%s" was not found.', 'bps'). '</p>',
		'<strong>BP Profile Search '. BPS_VERSION. '</strong>', $template);
	echo "\n<!-- BP Profile Search $version $form $template $location - end -->\n";

	return true;
}

add_action ('bp_before_directory_members_content', 'bps_display_filters');
function bps_display_filters ()
{
	$request = bps_get_request ();
	if (empty ($request))  return false;

	$version = BPS_VERSION;
	$form = $request['bp_profile_search'];
	$template = 'members/bps-filters';
	$location = 'filters';
	bps_set_request_data ($form, $location);

	echo "\n<!-- BP Profile Search $version $form $template $location -->\n";
	$found = bp_get_template_part ($template);
	if (!$found)  printf ('<p class="bps_error">'. __('%s: The filters template "%s" was not found.', 'bps'). '</p>',
		'<strong>BP Profile Search '. BPS_VERSION. '</strong>', $template);
	echo "\n<!-- BP Profile Search $version $form $template $location - end -->\n";

	return true;
}

function bps_set_request_data ($form, $location)
{
	global $bps_request_data;

	$meta = bps_options ($form);
	list ($x, $fields) = bps_get_fields ();
	$request = stripslashes_deep ($_REQUEST);

	$F = new stdClass;
	$F->id = $form;
	$F->location = $location;
	$F->header = $meta['header'];
	$F->toggle = ($meta['toggle'] == 'Enabled');
	$F->toggle_text = $meta['button'];

	$F->action = get_page_link ($meta['action']);
	$F->method = $meta['method'];
	$F->fields = array ();

	foreach ($meta['field_name'] as $k => $id)
	{
		if (empty ($fields[$id]))  continue;

		$field = $fields[$id];

		$f = new stdClass;
		$f->id = $id;
		$f->name = $field->name;
		$f->type = apply_filters ('bps_field_request_data_type', $field->type, $field);
		$f->display = $f->type;

		$f->label = $meta['field_label'][$k];
		$f->description = $meta['field_desc'][$k];
		if (empty ($f->label))  $f->label = $field->name;
		if (empty ($f->description))  $f->description = $field->description;

		$range = isset ($meta['field_range'][$k]);
		$f->code = 'field_'. $f->id;
		$f->value = '';
		$f->values = array ();
		$f->options = array ();

		if (bps_custom_field ($f->type))
		{
			$f = apply_filters ('bps_field_request_data', $f, $field);
		}
		else if ($range)
		{
			$f->display = 'range';
			list ($f->min, $f->max) = bps_minmax ($request, $f->id, $f->type);
		}
		else switch ($f->type)
		{
		case 'textbox':
		case 'number':
		case 'url':
		case 'textarea':
			$f->value = isset ($request[$f->code])? $request[$f->code]: '';
			break;

		case 'selectbox':
		case 'radio':
			$f->value = isset ($request[$f->code])? $request[$f->code]: '';
			$f->options = array ();
			$options = bps_get_options ($f->id);
			foreach ($options as $option)
				$f->options[$option] = ($option == $f->value);
			break;

		case 'multiselectbox':
		case 'checkbox':
			$f->values = isset ($request[$f->code])? (array)$request[$f->code]: array ();
			$f->options = array ();
			$options = bps_get_options ($f->id);
			foreach ($options as $option)
				$f->options[$option] = in_array ($option, $f->values);
			break;
		}

		$F->fields[] = $f;
	}

	$bps_request_data = $F;
	return true;
}

function bps_request_data ()
{
	global $bps_request_data;

	$F = $bps_request_data;
	return apply_filters ('bps_request_data', $F);
}

function bps_escaped_request_data ()
{
	$F = bps_request_data ();

	$F->toggle_text = esc_attr ($F->toggle_text);

	foreach ($F->fields as $f)
	{
		$f->name = esc_attr ($f->name);
		$f->label = esc_attr ($f->label);
		$f->description = esc_attr ($f->description);
		$f->value = esc_attr ($f->value);
		foreach ($f->values as $k => $value)  $f->values[$k] = esc_attr ($value);
		$options = array ();
		foreach ($f->options as $option => $selected)  $options[esc_attr ($option)] = $selected;
		$f->options = $options;
	}

	return apply_filters ('bps_escaped_request_data', $F);
}

add_shortcode ('bps_display', 'bps_show_form');
function bps_show_form ($attr, $content)
{
	ob_start ();

	if (isset ($attr['form']))
	{
		$template = isset ($attr['template'])? $attr['template']: '';
		bps_display_form ($attr['form'], $template, 'shortcode');
	}	

	return ob_get_clean ();
}

add_shortcode ('bps_directory', 'bps_show_directory');
function bps_show_directory ($attr, $content)
{
	ob_start ();

	if (!function_exists ('bp_has_profile'))
	{
		printf ('<p class="bps_error">'. __('%s: The BuddyPress Extended Profiles component is not active.', 'bps'). '</p>',
			'<strong>BP Profile Search '. BPS_VERSION. '</strong>');
	}
	else if (current_theme_supports ('buddypress'))
	{
		printf ('<p class="bps_error">'. __('%s: Multiple directories are not supported for this theme.', 'bps'). '</p>',
			'<strong>BP Profile Search '. BPS_VERSION. '</strong>');
	}
	else
	{
		$template = isset ($attr['template'])? $attr['template']: 'members/index';

		$found = bp_get_template_part ($template);
		if (!$found)  printf ('<p class="bps_error">'. __('%s: The directory template "%s" was not found.', 'bps'). '</p>',
			'<strong>BP Profile Search '. BPS_VERSION. '</strong>', $template);
	}

	return ob_get_clean ();
}

class bps_widget extends WP_Widget
{
	function bps_widget ()
	{
		$widget_ops = array ('description' => __('A Profile Search form.', 'bps'));
		$this->WP_Widget ('bps_widget', __('Profile Search', 'bps'), $widget_ops);
	}

	function widget ($args, $instance)
	{
		extract ($args);
		$title = apply_filters ('widget_title', $instance['title']);
		$form = $instance['form'];
		$template = isset ($instance['template'])? $instance['template']: '';

		echo $before_widget;
		if ($title)
			echo $before_title. $title. $after_title;
		bps_display_form ($form, $template, 'widget');
		echo $after_widget;
	}

	function update ($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['form'] = $new_instance['form'];
		$instance['template'] = $new_instance['template'];
		return $instance;
	}

	function form ($instance)
	{
		$title = isset ($instance['title'])? $instance['title']: '';
		$form = isset ($instance['form'])? $instance['form']: '';
		$template = isset ($instance['template'])? $instance['template']: '';
?>
	<p>
		<label for="<?php echo $this->get_field_id ('title'); ?>"><?php _e('Title:', 'bps'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id ('title'); ?>" name="<?php echo $this->get_field_name ('title'); ?>" type="text" value="<?php echo esc_attr ($title); ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id ('form'); ?>"><?php _e('Form:', 'bps'); ?></label>
<?php
		$posts = get_posts (array ('post_type' => 'bps_form', 'orderby' => 'ID', 'order' => 'ASC', 'nopaging' => true));
		if (count ($posts))
		{
			echo "<select class='widefat' id='{$this->get_field_id ('form')}' name='{$this->get_field_name ('form')}'>";
			foreach ($posts as $post)
			{
				$id = $post->ID;
				$name = !empty ($post->post_title)? $post->post_title: __('(no title)');
				echo "<option value='$id'";
				if ($id == $form)  echo " selected='selected'";
				echo ">$name &nbsp;</option>\n";
			}
			echo "</select>";
		}
		else
		{
			echo '<br/>';
			_e('You have not created any form yet.', 'bps');
		}
?>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id ('template'); ?>"><?php _e('Template:', 'bps'); ?></label>
<?php
		$templates = bps_templates ();
		echo "<select class='widefat' id='{$this->get_field_id ('template')}' name='{$this->get_field_name ('template')}'>";
		foreach ($templates as $option)
		{
			echo "<option value='$option'";
			if ($option == $template)  echo " selected='selected'";
			echo ">$option &nbsp;</option>\n";
		}
		echo "</select>";
?>
	</p>
<?php
	}
}

add_action ('widgets_init', 'bps_widget_init');
function bps_widget_init ()
{
	register_widget ('bps_widget');
}
