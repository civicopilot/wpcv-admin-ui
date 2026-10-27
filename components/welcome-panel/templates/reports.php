<?php
/** Display report choices, ordering handles, and preview links. */
defined( 'ABSPATH' ) || exit;
?>
		<fieldset <?php disabled( is_wp_error( $forms ) ); ?>>
			<p>
			<?php
			if ( is_multisite() ) {
				esc_html_e( 'Report selections are shared across the network and resolved on each site.', 'wpcv-admin' );
				echo ' ';
			}
			esc_html_e( 'To make a report available below, add its Search Display to a form in CiviCRM Form Builder, give the form a page URL, and enable its Reports Listing placement.', 'wpcv-admin' ); ?></p>
			<p><?php

			/* translators: %d: Maximum number of reports. */
			echo esc_html( sprintf( __( 'Select up to %d reports, drag them into order, and save.', 'wpcv-admin' ), Wpcv_Admin_Welcome_Panel_Settings::MAX_CARDS ) );
			?></p>
			<p><label for="wpcv-afform-search"><?php esc_html_e( 'Search reports', 'wpcv-admin' ); ?></label><br>
			<input type="search" id="wpcv-afform-search" class="regular-text" aria-controls="wpcv-afform-list" placeholder="<?php esc_attr_e( 'Search by report title', 'wpcv-admin' ); ?>"></p>

			<p id="wpcv-afform-search-status" data-empty="<?php esc_attr_e( 'No matching reports.', 'wpcv-admin' ); ?>" role="status" aria-live="polite"></p>
			<ul id="wpcv-afform-list" data-max-cards="<?php echo esc_attr( Wpcv_Admin_Welcome_Panel_Settings::MAX_CARDS ); ?>">
				<?php
				$choices = is_wp_error( $forms ) ? array() : $forms;

				// Show saved selections first, retaining unavailable entries for removal.
				$ordered = array();
				foreach ( $names as $name ) {
					$ordered[ $name ] = $choices[ $name ] ?? array( 'name' => $name, 'title' => $name, 'unavailable' => true );
				}
				$ordered += $choices;
				foreach ( $ordered as $form ) :
					$title = Wpcv_Admin_Afforms::title( $form );
					$label = ! empty( $form['unavailable'] ) ? $title . ' — ' . __( 'Unavailable or inaccessible', 'wpcv-admin' ) : $title;
				?>
				<li class="wpcv-afform-row" data-search="<?php echo esc_attr( $title . ' ' . $form['name'] ); ?>">
					<span class="wpcv-afform-handle dashicons dashicons-menu" aria-hidden="true"></span>
					<label class="wpcv-afform-title"><input type="checkbox" name="afforms[]" value="<?php echo esc_attr( $form['name'] ); ?>" <?php checked( in_array( $form['name'], $names, true ) ); ?>> <?php echo esc_html( $label ); ?></label>

					<?php
					$preview_url = '';
					if ( empty( $form['unavailable'] ) ) {
						try {
							$preview_url = Wpcv_Admin_Afforms::url( $form );
						} catch ( \Throwable $e ) {
							// Omit the preview when the route cannot be resolved.
						}
					}
					?>
					<?php if ( $preview_url ) : ?>
						<a href="<?php echo esc_url( $preview_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open report ↗', 'wpcv-admin' ); ?><span class="screen-reader-text"><?php echo esc_html( ': ' . $title ); ?></span></a>
					<?php endif; ?>
				</li>
				<?php endforeach; ?>
			</ul>
			<noscript><p><?php esc_html_e( 'Without JavaScript, use the checkboxes to select forms. Enable JavaScript for search and drag-and-drop ordering.', 'wpcv-admin' ); ?></p></noscript>
		</fieldset>
