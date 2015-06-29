<!-- <div class="cn-entry-single"> -->

	<div class="cn-right">

		<?php
		$cropMode = array( 0 => 'none', 1 => 'crop', 2 => 'fill', 3 => 'fit' );

		$entry->getImage( array(
			'image'    => $atts['image_single'],
			'height'   => $atts['image_single_height'],
			'width'    => $atts['image_single_width'],
			'quality'  => cnSettingsAPI::get( 'connections', 'image_large', 'quality' ),
			'zc'       => array_search( cnSettingsAPI::get( 'connections', 'image_large', 'ratio' ), $cropMode ),
			'fallback' => array(
				'type'     => $atts['image_single_fallback'],
				'string'   => $atts['str_image_single']
				)
			)
		);

		?>

	</div>

	<div class="cn-left">

		<div style="margin-bottom: 24px;">
			<h2><?php $entry->getNameBlock( array( 'format' => $atts['name_format'], 'link' => FALSE ) ); ?></h2>
			<?php if ( $atts['show_title'] ) $entry->getTitleBlock(); ?>
			<?php if ( $atts['show_org'] ) $entry->getOrgUnitBlock(); ?>
			<?php if ( $atts['show_contact_name'] ) $entry->getContactNameBlock( array( 'format' => $atts['contact_name_format'] , 'label' => $atts['str_contact'] ) ); ?>
		</div>

		<?php

		if ( $atts['show_addresses'] ) $entry->getAddressBlock( array( 'format' => $atts['addr_format'] , 'type' => $atts['address_types'] ) );

		if ( $atts['show_phone_numbers'] ) $entry->getPhoneNumberBlock( array( 'format' => $atts['phone_format'] , 'type' => $atts['phone_types'] ) );

		if ( $atts['show_email'] ) $entry->getEmailAddressBlock( array( 'format' => $atts['email_format'] , 'type' => $atts['email_types'] ) );

		if ( $atts['show_im'] ) $entry->getImBlock();

		if ( $atts['show_social_media'] ) $entry->getSocialMediaBlock();

		if ( $atts['show_dates'] ) $entry->getDateBlock( array( 'format' => $atts['date_format'], 'type' => $atts['date_types'] ) );

		if ( $atts['show_links'] ) $entry->getLinkBlock( array( 'format' => $atts['link_format'], 'type' => $atts['link_types'] ) );

		if ( $atts['show_family'] )$entry->getFamilyMemberBlock();

		?>

	</div>


	<div class="cn-clear"></div>

	<?php

	if ( $atts['enable_bio'] && $entry->getBio() != '' ) {

		echo '<div class="cn-bio">';

			if ( $atts['enable_bio_head'] ) echo '<h4>' , $atts['str_bio_head'] , '</h4>';

			$entry->getBioBlock();

			echo '<div class="cn-clear"></div>';

		echo '</div>';
	}

	if ( $atts['enable_note'] && $entry->getNotes() != '' ) {

		echo '<div class="cn-notes">';

			if ( $atts['enable_note_head'] ) echo '<h4>' , $atts['str_note_head'] , '</h4>';

			$entry->getNotesBlock();

			echo '<div class="cn-clear"></div>';

		echo '</div>';
	}

	if ( $atts['enable_map'] ) {

		$gMap = $entry->getMapBlock( array(
			'height' => $atts['map_frame_height'] ,
			'width'  => ( $atts['map_frame_width'] ) ? $atts['map_frame_width'] : NULL ,
			'return' => TRUE ,
			'zoom'   => $atts['map_zoom']
			)
		);

		if ( ! empty( $gMap ) )  $mapDiv = '<div class="cn-gmap-single" id="cn-gmap-single" data-gmap-id="' . $entry->getRuid() . '">' . $gMap . '</div>';

	}

	if ( isset($mapDiv) ) echo $mapDiv;

	$entry->getContentBlock( $atts['content'], $atts, $template );

	?>
<!-- </div> -->
