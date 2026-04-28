<?php

/**
 * Card helpers for the plugin.
 *
 * @since      1.0.0
 * @package    Wpcv_Welcome_Panel
 * @subpackage Wpcv_Welcome_Panel/includes
 */
class Wpcv_Welcome_Panel_Cards {

	/**
	 * Return supported access rules for welcome panel cards.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public static function get_access_rules() {
		return array(
			'everyone'              => '',
			'access_civicrm'        => 'access_civicrm',
			'access_civicontribute' => 'access_civicontribute',
			'access_civievent'      => 'access_civievent',
			'access_civimail'       => 'access_civimail',
			'access_civimember'     => 'access_civimember',
			'access_civireport'     => 'access_civireport',
			'publish_posts'         => 'publish_posts',
			'publish_events'        => 'publish_events',
		);
	}

	/**
	 * Return the icon that should be used for an access rule.
	 *
	 * @since    1.0.0
	 * @param    string $access_rule Access rule key.
	 * @return   string
	 */
	public static function get_icon_for_access_rule( $access_rule ) {
		$icon_map = array(
			'access_civicrm'        => 'civicrm',
			'access_civicontribute' => 'contributions',
			'access_civievent'      => 'events',
			'access_civimail'       => 'mail',
			'access_civimember'     => 'members',
			'access_civireport'     => 'civicrm',
			'publish_posts'         => 'posts',
			'publish_events'        => 'events',
		);

		if ( isset( $icon_map[ $access_rule ] ) ) {
			return $icon_map[ $access_rule ];
		}

		return 'generic';
	}

	/**
	 * Determine whether a card should be shown to the current user.
	 *
	 * @since    1.0.0
	 * @param    array $card Card definition.
	 * @return   bool
	 */
	public static function user_can_view_card( $card ) {
		if ( ! empty( $card['access_rule'] ) ) {
			$access_rules = self::get_access_rules();

			if ( ! isset( $access_rules[ $card['access_rule'] ] ) ) {
				return false;
			}

			$capability = $access_rules[ $card['access_rule'] ];

			if ( '' !== $capability && ! current_user_can( $capability ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Normalize a single welcome panel card definition.
	 *
	 * @since    1.0.0
	 * @param    array $card Raw card data.
	 * @return   array
	 */
	public static function normalize_card( $card ) {
		$access_rules = self::get_access_rules();
		$access_rule  = isset( $card['access_rule'] ) ? sanitize_key( $card['access_rule'] ) : 'everyone';

		if ( ! array_key_exists( $access_rule, $access_rules ) ) {
			$access_rule = 'everyone';
		}

		return array(
			'icon'        => self::get_icon_for_access_rule( $access_rule ),
			'title'       => isset( $card['title'] ) ? sanitize_text_field( $card['title'] ) : '',
			'description' => isset( $card['description'] ) ? sanitize_textarea_field( $card['description'] ) : '',
			'url'         => isset( $card['url'] ) ? esc_url_raw( $card['url'] ) : '',
			'access_rule' => $access_rule,
		);
	}

	/**
	 * Return the stored cards after normalization.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public static function get_configured_cards() {
		$cards = Wpcv_Welcome_Panel_Settings::get_stored_cards();

		if ( ! is_array( $cards ) ) {
			return array();
		}

		$normalized_cards = array();

		foreach ( $cards as $card ) {
			if ( ! is_array( $card ) ) {
				continue;
			}

			$normalized_card = self::normalize_card( $card );

			if ( '' === $normalized_card['title'] || '' === $normalized_card['url'] ) {
				continue;
			}

			$normalized_cards[] = $normalized_card;
		}

		return $normalized_cards;
	}

	/**
	 * Return the cards that should be displayed on the dashboard.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public static function get_cards() {
		$cards = self::get_configured_cards();
		$cards = apply_filters( 'wpcv_welcome_panel_cards', $cards );

		return array_values(
			array_filter(
				$cards,
				array( __CLASS__, 'user_can_view_card' )
			)
		);
	}

	/**
	 * Render a welcome panel icon.
	 *
	 * @since    1.0.0
	 * @param    array $card Card definition.
	 * @return   string
	 */
	public static function get_card_icon_markup( $card ) {
		return sprintf(
			'<div class="welcome-panel-icon-%1$s" aria-hidden="true"></div>',
			esc_attr( $card['icon'] )
		);
	}
}
