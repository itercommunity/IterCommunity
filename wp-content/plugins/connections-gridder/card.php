<?php printf(
	'<div class="cn-gridder-thumb" style="background-color: %1$s; width: %2$dpx; height: %3$dpx;">',
	$atts['background_color'],
	$atts['image_width'],
	$atts['image_height']
); ?>

	<?php $entry->getImage(
		array(
			'image'    => $atts['image'],
			'height'   => $atts['image_height'],
			'width'    => $atts['image_width'],
			'fallback' => array(
				'type'     => 'block',
				'string'   => ''
				),
			'style'    => $atts['image_opacity'] ? array( 'opacity' => $atts['image_opacity'], 'filter' => 'alpha(opacity=' . $atts['image_opacity'] * 100 . ')' ) : array(),
		)
	); ?>

	<?php

	if ( $atts['overlay'] == 'none' ) {

		?>

		<span class="cn-gridder-toggle cn-gridder-toggle-bottom-right" style="color: <?php echo $atts['color']; ?>;"></span>

		<?php

	} else {

		?>

		<?php

		printf(
			'<span class="cn-gridder-overlay%1$s" style="background-color: %2$s; color: %3$s;">',
			$atts['overlay'] == 'hover' ? ' cn-gridder-overlay-hover' : ' cn-gridder-overlay-static',
			$atts['background_color'],
			$atts['color']
			);

			$entry->getNameBlock( array( 'format' => $atts['name_format'], 'link' => FALSE ) );
			if ( $atts['show_title'] ) $entry->getTitleBlock();

		?>
			<span class="cn-gridder-toggle cn-gridder-toggle-top-right"></span>
		</span> <!-- END .cn-gridder-overlay -->

		<?php

	}

	?>

</div> <!-- END .cn-gridder-thumb -->

<?php printf(
	'<div class="cn-gridder-content" style="background-color: %1$s; color: %2$s;">',
	$atts['background_color'],
	$atts['color']
); ?>

	<?php if ( $atts['overlay'] == 'none' ) : ?>

		<div class="cn-gridder-name">
			<?php $entry->getNameBlock( array( 'format' => $atts['name_format'], 'link' => FALSE ) ); ?>
			<?php if ( $atts['show_title'] ) $entry->getTitleBlock(); ?>
		</div>

	<?php endif; ?>

	<div class="cn-gridder-contact">

		<?php

		$number = $entry->getPhoneNumberBlock( array( 'preferred' => TRUE, 'format' => '%number%', 'return' => TRUE ) );

		if ( $number ) {

			echo $number;

		} else {

			$entry->getPhoneNumberBlock( array( 'format' => '%number%', 'limit' => 1 ) );
		}

		$email = $entry->getEmailAddressBlock( array( 'preferred' => TRUE, 'format' => '%address%', 'return' => TRUE ) );

		if ( $email ) {

			echo $email;

		} else {

			$entry->getEmailAddressBlock( array( 'format' => '%address%', 'limit' => 1 ) );
		}

		?>

		<?php if ( $atts['show_social_media'] ) $entry->getSocialMediaBlock(); ?>

	</div>

	<div class="cn-gridder-excerpt">
		<p>
			<?php

			if ( $atts['excerpt_length'] ) {

				echo wp_trim_words( $entry->getBio(), $atts['excerpt_length'], $atts['excerpt_more'] );

			} else {

				echo $entry->getBio();
			}

			?>
		</p>
	</div>


	<?php

	if ( $atts['show_profile_link'] ) {

		echo '<div class="cn-gridder-detail-link">';

		cnURL::permalink(
			array(
				'type' => 'name',
				'slug' => $entry->getSlug(),
				'text' => __( 'View Profile', 'cnt_gridder' ),
				'home_id' => $atts['home_id'],
				)
			);

		echo '</div>';
	}


	?>


</div> <!-- END .cn-gridder-thumb -->
