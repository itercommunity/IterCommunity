<?php

/*
 * BP Profile Search - filters template 'bps-filters'
 *
 * See http://dontdream.it/bp-profile-search/form-templates/ if you wish to modify this template or develop a new one.
 *
 */

	$F = bps_escaped_request_data ();
	$filters = '';

	foreach ($F->fields as $f)
	{
		switch ($f->display)
		{
		case 'range':
			if ($f->min === '' && $f->max === '')  break;
			$filters .= "<strong>$f->label:</strong>";
			if ($f->min !== '')
				$filters .= " <strong>". __('min', 'bps'). "</strong> $f->min";
			if ($f->max !== '')
				$filters .= " <strong>". __('max', 'bps'). "</strong> $f->max";
			$filters .= "<br>\n";
			break;

		case 'textbox':
		case 'number':
		case 'textarea':
		case 'selectbox':
		case 'radio':
		case 'url':
			if ($f->value === '')  break;
			$filters .= "<strong>$f->label:</strong> $f->value<br>\n";
			break;

		case 'multiselectbox':
		case 'checkbox':
			$values = implode (', ', $f->values);
			if ($values === '')  break;
			$filters .= "<strong>$f->label:</strong> $values<br>\n";
			break;

		default:
			$filters .= "<p>BP Profile Search: don't know how to display the <em>$f->display</em> filter type.</p>\n";
			break;
		}
	}

	if ($filters)
	{
		echo "<p class='bps_filters'>\n";
		echo $filters;
		echo "<a href='$F->action'>". __('Clear', 'buddypress'). "</a><br>\n";
		echo "</p>\n";
	}

// BP Profile Search - end of template
