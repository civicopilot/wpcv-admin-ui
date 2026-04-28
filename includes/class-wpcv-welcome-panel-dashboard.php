<?php

/**
 * Dashboard rendering helpers for the plugin.
 *
 * @since      1.0.0
 * @package    Wpcv_Welcome_Panel
 * @subpackage Wpcv_Welcome_Panel/includes
 */
class Wpcv_Welcome_Panel_Dashboard {

	/**
	 * Determine whether the current user should see the custom welcome panel.
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	public static function user_can_view_panel() {
		return current_user_can( 'access_civicrm' );
	}

	/**
	 * Remove the core WordPress welcome panel.
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	public static function remove_core_welcome_panel() {
		remove_action( 'welcome_panel', 'wp_welcome_panel' );
	}

	/**
	 * Render the custom welcome panel.
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	public static function render_panel() {
		if ( ! self::user_can_view_panel() ) {
			return;
		}

		$user             = wp_get_current_user();
		$cards            = Wpcv_Welcome_Panel_Cards::get_cards();
		$subheading       = Wpcv_Welcome_Panel_Settings::get_subheading();
		$background_color = Wpcv_Welcome_Panel_Settings::get_background_color();
		?>
		<div id="wpcv-welcome-panel-dashboard" class="welcome-panel" style="display:none; --wpcv-welcome-panel-bg-color: <?php echo esc_attr( $background_color ); ?>;">
		<div class="welcome-panel-content">
			<div class="welcome-panel-header">
				<h2>
					<?php echo sprintf(
						esc_html__( 'Hi %s, welcome to the %s', 'wpcv-welcome-panel' ),
						esc_html( $user->first_name ),
						esc_html( get_bloginfo( 'name' ) )
					); ?>
				</h2>
				<h3><?php echo esc_html( $subheading ); ?></h3>
			</div>

			<div class="welcome-panel-column-container">
				<?php if ( empty( $cards ) ) : ?>
					<div class="welcome-panel-column">
						<div class="welcome-panel-icon-generic" aria-hidden="true"></div>
						<div class="welcome-panel-column-content">
							<h3><?php esc_html_e( 'No welcome links configured yet', 'wpcv-welcome-panel' ); ?></h3>
							<?php if ( Wpcv_Welcome_Panel_Settings::user_can_manage_settings() ) : ?>
								<p>
									<?php
									echo wp_kses(
										sprintf(
											/* translators: %s: Settings page URL. */
											__( 'You can add dashboard cards from the <a href="%s">WPCV Welcome Panel settings screen</a>.', 'wpcv-welcome-panel' ),
											esc_url( Wpcv_Welcome_Panel_Settings::get_settings_page_url() )
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
							<?php echo wp_kses_post( Wpcv_Welcome_Panel_Cards::get_card_icon_markup( $card ) ); ?>
							<div class="welcome-panel-column-content">
								<h3><a href="<?php echo esc_url( $card['url'] ); ?>"><?php echo esc_html( $card['title'] ); ?></a></h3>
								<?php if ( ! empty( $card['description'] ) ) : ?>
									<p><?php echo esc_html( $card['description'] ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
		</div>
		<?php
	}

	/**
	 * Register the hidden dashboard widget used to place the custom welcome panel.
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	public static function register_dashboard_widget() {
		if ( ! self::user_can_view_panel() || ! is_blog_admin() ) {
			return;
		}

		wp_add_dashboard_widget(
			'wpcv_welcome_panel_widget',
			'&nbsp;',
			array( __CLASS__, 'render_panel' )
		);
	}
}
