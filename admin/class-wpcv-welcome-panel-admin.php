<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://civicopilot.com
 * @since      1.0.0
 *
 * @package    Wpcv_Welcome_Panel
 * @subpackage Wpcv_Welcome_Panel/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpcv_Welcome_Panel
 * @subpackage Wpcv_Welcome_Panel/admin
 * @author     Andy Burns <andy@andyburns.co>
 */
class Wpcv_Welcome_Panel_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpcv_Welcome_Panel_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpcv_Welcome_Panel_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpcv-welcome-panel-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpcv_Welcome_Panel_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpcv_Welcome_Panel_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpcv-welcome-panel-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the network admin settings page.
	 *
	 * @since    1.0.0
	 */
	public function add_network_admin_page() {

		if ( ! is_multisite() ) {
			return;
		}

		add_submenu_page(
			'settings.php',
			__( 'WPCV Welcome Panel', 'wpcv-welcome-panel' ),
			__( 'Network Welcome Panel', 'wpcv-welcome-panel' ),
			'manage_network_options',
			'wpcv-welcome-panel',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register the single-site admin settings page.
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	public function add_admin_page() {

		if ( is_multisite() ) {
			return;
		}

		add_options_page(
			__( 'WPCV Welcome Panel', 'wpcv-welcome-panel' ),
			__( 'Welcome Panel', 'wpcv-welcome-panel' ),
			'manage_options',
			'wpcv-welcome-panel',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Return the available access rules for the settings form.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_access_rule_choices() {
		return array(
			'everyone'              => __( 'Everyone', 'wpcv-welcome-panel' ),
			'access_civicrm'        => __( 'CiviCRM', 'wpcv-welcome-panel' ),
			'access_civicontribute' => __( 'CiviContribute', 'wpcv-welcome-panel' ),
			'access_civievent'      => __( 'CiviEvent', 'wpcv-welcome-panel' ),
			'access_civimail'       => __( 'CiviMail', 'wpcv-welcome-panel' ),
			'access_civimember'     => __( 'CiviMember', 'wpcv-welcome-panel' ),
			'access_civireport'     => __( 'CiviReport', 'wpcv-welcome-panel' ),
			'publish_posts'         => __( 'Publish Posts', 'wpcv-welcome-panel' ),
			'publish_events'        => __( 'Publish Events', 'wpcv-welcome-panel' ),
		);
	}

	/**
	 * Return saved cards for the settings form.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_settings_cards() {
		$stored_cards = array_slice( Wpcv_Welcome_Panel_Settings::get_stored_cards(), 0, 6 );
		$cards        = array();

		foreach ( $stored_cards as $card ) {
			if ( ! is_array( $card ) ) {
				continue;
			}

			$cards[] = Wpcv_Welcome_Panel_Cards::normalize_card( $card );
		}

		return $cards;
	}

	/**
	 * Return the saved subheading for the settings form.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private function get_settings_subheading() {
		return Wpcv_Welcome_Panel_Settings::get_subheading();
	}

	/**
	 * Return the saved background color for the settings form.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private function get_settings_background_color() {
		return Wpcv_Welcome_Panel_Settings::get_background_color();
	}

	/**
	 * Return the capability required to manage settings.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private function get_settings_capability() {
		return Wpcv_Welcome_Panel_Settings::get_settings_capability();
	}

	/**
	 * Return the settings form action URL.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private function get_settings_form_action() {
		if ( is_multisite() ) {
			return network_admin_url( 'edit.php?action=wpcv_welcome_panel_save_settings' );
		}

		return admin_url( 'admin-post.php?action=wpcv_welcome_panel_save_settings' );
	}

	/**
	 * Return the settings page URL.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private function get_settings_page_url() {
		return Wpcv_Welcome_Panel_Settings::get_settings_page_url();
	}

	/**
	 * Return whether the settings saved notice is active.
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	private function has_updated_notice() {
		if ( is_multisite() ) {
			return (bool) get_site_transient( 'wpcv_welcome_panel_updated_notice' );
		}

		return (bool) get_transient( 'wpcv_welcome_panel_updated_notice' );
	}

	/**
	 * Return invalid card numbers from the last save attempt.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_invalid_url_notice_cards() {
		$card_numbers = is_multisite()
			? get_site_transient( 'wpcv_welcome_panel_invalid_url_notice' )
			: get_transient( 'wpcv_welcome_panel_invalid_url_notice' );

		return is_array( $card_numbers ) ? $card_numbers : array();
	}

	/**
	 * Clear the settings saved notice.
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	private function clear_updated_notice() {
		if ( is_multisite() ) {
			delete_site_transient( 'wpcv_welcome_panel_updated_notice' );
			return;
		}

		delete_transient( 'wpcv_welcome_panel_updated_notice' );
	}

	/**
	 * Clear the invalid URL notice.
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	private function clear_invalid_url_notice() {
		if ( is_multisite() ) {
			delete_site_transient( 'wpcv_welcome_panel_invalid_url_notice' );
			return;
		}

		delete_transient( 'wpcv_welcome_panel_invalid_url_notice' );
	}

	/**
	 * Set the settings saved notice.
	 *
	 * @since    1.0.0
	 * @return   void
	 */
	private function set_updated_notice() {
		if ( is_multisite() ) {
			set_site_transient( 'wpcv_welcome_panel_updated_notice', 1, 30 );
			return;
		}

		set_transient( 'wpcv_welcome_panel_updated_notice', 1, 30 );
	}

	/**
	 * Set the invalid URL notice card numbers.
	 *
	 * @since    1.0.0
	 * @param    array $card_numbers Invalid card numbers.
	 * @return   void
	 */
	private function set_invalid_url_notice( $card_numbers ) {
		$card_numbers = array_values(
			array_filter(
				array_map( 'absint', $card_numbers )
			)
		);

		if ( empty( $card_numbers ) ) {
			$this->clear_invalid_url_notice();
			return;
		}

		if ( is_multisite() ) {
			set_site_transient( 'wpcv_welcome_panel_invalid_url_notice', $card_numbers, 30 );
			return;
		}

		set_transient( 'wpcv_welcome_panel_invalid_url_notice', $card_numbers, 30 );
	}

	/**
	 * Parse cards posted from the custom network settings form.
	 *
	 * @since    1.0.0
	 * @param    array $posted_data Submitted form values.
	 * @return   array
	 */
	private function parse_posted_cards( $posted_data ) {
		$titles       = isset( $posted_data['card_title'] ) && is_array( $posted_data['card_title'] ) ? $posted_data['card_title'] : array();
		$descriptions = isset( $posted_data['card_description'] ) && is_array( $posted_data['card_description'] ) ? $posted_data['card_description'] : array();
		$urls         = isset( $posted_data['card_url'] ) && is_array( $posted_data['card_url'] ) ? $posted_data['card_url'] : array();
		$access_rules = isset( $posted_data['card_access_rule'] ) && is_array( $posted_data['card_access_rule'] ) ? $posted_data['card_access_rule'] : array();
		$cards             = array();
		$invalid_card_urls = array();

		for ( $i = 0; $i < 6; $i++ ) {
			$raw_url = isset( $urls[ $i ] ) ? $urls[ $i ] : '';
			$card = Wpcv_Welcome_Panel_Cards::normalize_card(
				array(
					'title'       => isset( $titles[ $i ] ) ? $titles[ $i ] : '',
					'description' => isset( $descriptions[ $i ] ) ? $descriptions[ $i ] : '',
					'url'         => $raw_url,
					'access_rule' => isset( $access_rules[ $i ] ) ? $access_rules[ $i ] : 'everyone',
				)
			);

			if ( '' !== trim( (string) $raw_url ) && '' === $card['url'] ) {
				$invalid_card_urls[] = $i + 1;
			}

			if ( '' === $card['title'] && '' === $card['description'] && '' === $card['url'] ) {
				continue;
			}

			if ( '' === $card['title'] ) {
				continue;
			}

			if ( '' === $card['url'] && ! in_array( $i + 1, $invalid_card_urls, true ) ) {
				continue;
			}

			$cards[] = $card;
		}

		return array(
			'cards'             => $cards,
			'invalid_card_urls' => $invalid_card_urls,
		);
	}

	/**
	 * Render the plugin settings page.
	 *
	 * @since    1.0.0
	 */
	public function render_settings_page() {

		if ( ! current_user_can( $this->get_settings_capability() ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'wpcv-welcome-panel' ) );
		}

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'WPCV Welcome Panel', 'wpcv-welcome-panel' ); ?></h1>
			<?php if ( $this->has_updated_notice() ) : ?>
				<?php $this->clear_updated_notice(); ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Welcome panel settings saved.', 'wpcv-welcome-panel' ); ?></p>
				</div>
			<?php endif; ?>
			<?php $invalid_url_cards = $this->get_invalid_url_notice_cards(); ?>
			<?php if ( ! empty( $invalid_url_cards ) ) : ?>
				<?php $this->clear_invalid_url_notice(); ?>
				<div class="notice notice-warning is-dismissible">
					<p>
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: Card numbers with invalid relative admin URLs. */
								__( 'Card URLs not saved for card(s) %s. Use a relative admin URL beginning with /.', 'wpcv-welcome-panel' ),
								implode( ', ', $invalid_url_cards )
							)
						);
						?>
					</p>
				</div>
			<?php endif; ?>

			<?php
			$cards               = $this->get_settings_cards();
			$subheading          = $this->get_settings_subheading();
			$background_color    = $this->get_settings_background_color();
			$access_rule_choices = $this->get_access_rule_choices();
			?>
			<div class="notice notice-info inline">
				<p><?php esc_html_e( 'Manage up to 6 dashboard cards. Access Rule is optional. If selected, the card only shows for users with that required capability.', 'wpcv-welcome-panel' ); ?></p>
				<?php if ( is_multisite() ) : ?>
					<p><?php esc_html_e( 'Ensure you use relative URLs that work across every subsite (global). Site-specific report or page URLs should not be used.', 'wpcv-welcome-panel' ); ?></p>
				<?php endif; ?>
			</div>

			<form method="post" action="<?php echo esc_url( $this->get_settings_form_action() ); ?>" class="wpcv-welcome-panel-settings-form">
				<?php wp_nonce_field( 'wpcv_welcome_panel_save_settings', 'wpcv_welcome_panel_nonce' ); ?>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="wpcv-welcome-panel-subheading"><?php esc_html_e( 'Subheading', 'wpcv-welcome-panel' ); ?></label>
							</th>
							<td>
								<input type="text" id="wpcv-welcome-panel-subheading" name="subheading" value="<?php echo esc_attr( $subheading ); ?>" class="regular-text" />
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="wpcv-welcome-panel-background-color"><?php esc_html_e( 'Background Color', 'wpcv-welcome-panel' ); ?></label>
							</th>
							<td>
								<input type="color" id="wpcv-welcome-panel-background-color" name="background_color" value="<?php echo esc_attr( $background_color ); ?>" />
							</td>
						</tr>
					</tbody>
				</table>

				<div class="wpcv-welcome-panel-settings-list" data-max-cards="6">
					<?php for ( $i = 0; $i < 6; $i++ ) : ?>
						<?php
						$card = isset( $cards[ $i ] ) ? $cards[ $i ] : array(
							'title'       => '',
							'description' => '',
							'url'         => '',
							'access_rule' => 'everyone',
						);
						$is_empty = empty( $card['title'] ) && empty( $card['description'] ) && empty( $card['url'] );
						?>
						<div class="wpcv-welcome-panel-settings-row postbox<?php echo $is_empty && $i > 0 ? ' is-empty' : ''; ?>" data-card-row>
							<div class="wpcv-welcome-panel-settings-row-header">
								<h2 class="hndle" data-card-title><span><?php echo esc_html( sprintf( __( 'Card %d', 'wpcv-welcome-panel' ), $i + 1 ) ); ?></span></h2>
								<div class="wpcv-welcome-panel-settings-row-actions">
									<button type="button" class="button button-small" data-move-up><?php esc_html_e( 'Up', 'wpcv-welcome-panel' ); ?></button>
									<button type="button" class="button button-small" data-move-down><?php esc_html_e( 'Down', 'wpcv-welcome-panel' ); ?></button>
									<?php if ( $i > 0 ) : ?>
										<button type="button" class="button-link-delete" data-remove-card><?php esc_html_e( 'Remove', 'wpcv-welcome-panel' ); ?></button>
									<?php endif; ?>
								</div>
							</div>

							<div class="inside">
							<div class="wpcv-welcome-panel-settings-grid">
								<p>
									<label for="wpcv-welcome-panel-card-title-<?php echo esc_attr( $i ); ?>"><?php esc_html_e( 'Title', 'wpcv-welcome-panel' ); ?></label>
									<input type="text" id="wpcv-welcome-panel-card-title-<?php echo esc_attr( $i ); ?>" name="card_title[]" value="<?php echo esc_attr( isset( $card['title'] ) ? $card['title'] : '' ); ?>" class="regular-text" />
								</p>

								<p>
									<label for="wpcv-welcome-panel-card-url-<?php echo esc_attr( $i ); ?>"><?php esc_html_e( 'Relative Admin URL', 'wpcv-welcome-panel' ); ?></label>
									<input type="text" id="wpcv-welcome-panel-card-url-<?php echo esc_attr( $i ); ?>" name="card_url[]" value="<?php echo esc_attr( isset( $card['url'] ) ? $card['url'] : '' ); ?>" class="regular-text" placeholder="/wp-admin/admin.php?page=CiviCRM&q=civicrm%2Fvolunteers" />
								</p>

								<p>
									<label for="wpcv-welcome-panel-card-access-rule-<?php echo esc_attr( $i ); ?>"><?php esc_html_e( 'Access Rule', 'wpcv-welcome-panel' ); ?></label>
									<select id="wpcv-welcome-panel-card-access-rule-<?php echo esc_attr( $i ); ?>" name="card_access_rule[]">
										<?php foreach ( $access_rule_choices as $rule_key => $rule_label ) : ?>
											<option value="<?php echo esc_attr( $rule_key ); ?>" <?php selected( isset( $card['access_rule'] ) ? $card['access_rule'] : '', $rule_key ); ?>><?php echo esc_html( $rule_label ); ?></option>
										<?php endforeach; ?>
									</select>
								</p>

								<p class="wpcv-welcome-panel-settings-description">
									<label for="wpcv-welcome-panel-card-description-<?php echo esc_attr( $i ); ?>"><?php esc_html_e( 'Description', 'wpcv-welcome-panel' ); ?></label>
									<textarea id="wpcv-welcome-panel-card-description-<?php echo esc_attr( $i ); ?>" name="card_description[]" rows="3"><?php echo esc_textarea( isset( $card['description'] ) ? $card['description'] : '' ); ?></textarea>
								</p>
							</div>
							</div>
						</div>
					<?php endfor; ?>
				</div>

				<p>
					<button type="button" class="button" data-add-card><?php esc_html_e( 'Add Card', 'wpcv-welcome-panel' ); ?></button>
				</p>

				<?php submit_button( __( 'Save Cards', 'wpcv-welcome-panel' ) ); ?>
			</form>
		</div>
		<?php

	}

	/**
	 * Save settings for the welcome panel.
	 *
	 * @since    1.0.0
	 */
	public function save_settings() {

		if ( ! current_user_can( $this->get_settings_capability() ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'wpcv-welcome-panel' ) );
		}

		check_admin_referer( 'wpcv_welcome_panel_save_settings', 'wpcv_welcome_panel_nonce' );
		$posted_data       = wp_unslash( $_POST );
		$parsed_cards      = $this->parse_posted_cards( $posted_data );
		$cards             = isset( $parsed_cards['cards'] ) ? $parsed_cards['cards'] : array();
		$invalid_card_urls = isset( $parsed_cards['invalid_card_urls'] ) ? $parsed_cards['invalid_card_urls'] : array();
		$subheading        = isset( $posted_data['subheading'] ) ? sanitize_text_field( $posted_data['subheading'] ) : '';
		$background_color  = isset( $posted_data['background_color'] ) ? sanitize_hex_color( $posted_data['background_color'] ) : '';

		if ( '' === $subheading ) {
			Wpcv_Welcome_Panel_Settings::delete_subheading();
		} else {
			Wpcv_Welcome_Panel_Settings::update_subheading( $subheading );
		}

		if ( empty( $background_color ) ) {
			Wpcv_Welcome_Panel_Settings::delete_background_color();
		} else {
			Wpcv_Welcome_Panel_Settings::update_background_color( $background_color );
		}

		if ( empty( $cards ) ) {
			Wpcv_Welcome_Panel_Settings::delete_stored_cards();
		} else {
			Wpcv_Welcome_Panel_Settings::update_stored_cards( $cards );
		}

		$this->set_updated_notice();
		$this->set_invalid_url_notice( $invalid_card_urls );

		wp_safe_redirect( $this->get_settings_page_url() );
		exit;

	}

}
