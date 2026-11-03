<?php
/** Display optional task sections followed by the configured reports. */
defined( 'ABSPATH' ) || exit;
?>
		<div id="wpcv-admin-ui-welcome-panel" class="welcome-panel" data-persist="<?php echo empty( WPCV_Admin_UI_Settings::get_settings()['persist_panel'] ) ? '0' : '1'; ?>" style="display:none; <?php echo esc_attr( $background_style ); ?>">
		<div class="welcome-panel-content">
			<?php if ( '' !== trim( $heading ) || '' !== trim( $subheading ) ) : ?>
				<div class="welcome-panel-header">
					<?php if ( '' !== trim( $heading ) ) : ?>
						<h2><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== trim( $subheading ) ) : ?>
						<p><?php echo esc_html( $subheading ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php foreach ( $sections as $section ) : ?>
				<?php if ( count( $sections ) > 1 ) : ?>
					<div class="welcome-panel-header wpcv-admin-ui-section-heading">
						<h2><?php echo esc_html( $section['title'] ); ?></h2>
						<?php if ( '' !== $section['description'] ) : ?>
							<p><?php echo esc_html( $section['description'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php include __DIR__ . '/site-management.php'; ?>
			<?php endforeach; ?>

			<?php if ( $show_reports ) : ?>
			<div class="welcome-panel-header wpcv-admin-ui-section-heading">
				<h2><?php esc_html_e( 'Reports', 'wpcv-admin-ui' ); ?></h2>
				<p><?php esc_html_e( 'Review submissions and follow up.', 'wpcv-admin-ui' ); ?></p>
			</div>

			<div class="welcome-panel-column-container">
				<?php
				// Keep loading failures distinct from an empty report selection.
				if ( is_wp_error( $cards ) ) :
				?>
					<div class="welcome-panel-column">
						<div class="welcome-panel-column-content">
							<h3><?php esc_html_e( 'Reports temporarily unavailable', 'wpcv-admin-ui' ); ?></h3>
							<p><?php esc_html_e( 'We could not load your reports. Please try again later.', 'wpcv-admin-ui' ); ?></p>
						</div>
					</div>
				<?php elseif ( empty( $cards ) ) : ?>
					<div class="welcome-panel-column">
						<div class="welcome-panel-column-content">
							<h3><?php esc_html_e( 'No reports available', 'wpcv-admin-ui' ); ?></h3>
							<?php if ( WPCV_Admin_UI_Settings::user_can_manage_settings() ) : ?>
								<p>
									<?php
									echo wp_kses(
										sprintf(

											/* translators: %s: Settings page URL. */
											__( 'Choose which reports appear here in <a href="%s">WPCV Admin UI settings</a>.', 'wpcv-admin-ui' ),
											esc_url( WPCV_Admin_UI_Settings::get_settings_page_url() )
										),
										array(
											'a' => array(
												'href' => array(),
											),
										)
									);
									?>
								</p>
							<?php endif; ?>
						</div>
					</div>
				<?php else : ?>
					<?php foreach ( $cards as $card ) : ?>
						<?php include __DIR__ . '/card.php'; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
		</div>
