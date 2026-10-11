<?php
/** Turn selected reports into ordered dashboard cards. */
class Wpcv_Admin_Cards {
	public static function get_cards() {
		// Read the ordered selection; skip discovery when nothing is selected.
		$names = Wpcv_Admin_Welcome_Panel_Settings::get_selected_afforms();
		if ( ! $names ) {
			return array();
		}
		// Refresh metadata under the viewing user’s permissions.
		$forms = Wpcv_Admin_Afforms::get( $names );
		if ( is_wp_error( $forms ) ) {
			return $forms;
		}
		// Build only resolvable destinations, preserving the selected order.
		$cards = array();
		foreach ( $names as $name ) {
			if ( ! isset( $forms[ $name ] ) ) {
				continue;
			}
			$form = $forms[ $name ];
			try {
				$url = Wpcv_Admin_Afforms::url( $form );
			} catch ( \Throwable $e ) {
				continue;
			}
			if ( ! $url ) {
				continue;
			}
			$cards[] = array(
				'title'       => Wpcv_Admin_Afforms::title( $form ),
				'description' => trim( wp_strip_all_tags( $form['description'] ?? '' ) ),
				'url'         => $url,
				'icon'        => $form['icon'] ?? '',
			);
		}
		return $cards;
	}
}
