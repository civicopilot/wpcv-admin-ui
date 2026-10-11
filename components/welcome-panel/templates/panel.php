<?php
/** Display the Welcome Panel heading, message, and report cards. */
defined( 'ABSPATH' ) || exit;
?>
		<div id="wpcv-admin-dashboard" class="welcome-panel" data-persist="<?php echo empty( Wpcv_Admin_Settings::get_settings()['persist_panel'] ) ? '0' : '1'; ?>" style="display:none; <?php echo esc_attr( $background_style ); ?>">
		<div class="welcome-panel-content">
			<div class="welcome-panel-header">
				<?php if ( '' !== trim( $heading ) ) : ?>
					<h2><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== trim( $subheading ) ) : ?>
					<p><?php echo esc_html( $subheading ); ?></p>
				<?php endif; ?>
			</div>

			<?php // Keep loading failures distinct from an empty report selection. ?>
			<div class="welcome-panel-column-container">
				<?php if ( is_wp_error( $cards ) ) : ?>
					<div class="welcome-panel-column">
						<div class="welcome-panel-column-content">
							<h3><?php esc_html_e( 'Reports temporarily unavailable', 'wpcv-admin' ); ?></h3>
							<p><?php esc_html_e( 'We could not load your reports. Please try again later.', 'wpcv-admin' ); ?></p>
						</div>
					</div>
				<?php elseif ( empty( $cards ) ) : ?>
					<div class="welcome-panel-column">
						<div class="welcome-panel-column-content">
							<h3><?php esc_html_e( 'No reports available', 'wpcv-admin' ); ?></h3>
							<?php if ( Wpcv_Admin_Settings::user_can_manage_settings() ) : ?>
								<p>
									<?php
									echo wp_kses(
										sprintf(
											/* translators: %s: Settings page URL. */
											__( 'Choose which reports appear here in <a href="%s">WPCV Admin settings</a>.', 'wpcv-admin' ),
											esc_url( Wpcv_Admin_Settings::get_settings_page_url() )
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
						<div class="welcome-panel-column">
							<div class="welcome-panel-column-content wpcv-afform-card">
								<?php if ( ! empty( $card['icon'] ) ) : ?>
									<i class="crm-i <?php echo esc_attr( $card['icon'] ); ?> wpcv-afform-icon" aria-hidden="true"></i>
								<?php endif; ?>
								<div class="wpcv-afform-details">
									<h3><a href="<?php echo esc_url( $card['url'] ); ?>"><?php echo esc_html( $card['title'] ); ?></a></h3>
									<?php if ( '' !== $card['description'] ) : ?>
										<p class="wpcv-afform-description"><?php echo esc_html( $card['description'] ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
		</div>
