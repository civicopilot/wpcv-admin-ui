<?php
/** Find Contact autocomplete displays that the current user can access. */
class WPCV_Admin_UI_Quick_Search_Displays {
	/** Suggest the companion display only when its extension is enabled and accessible. */
	public static function companion_default() {
		try {
			if ( ! civi_wp()->initialize() ) { return array(); }

			// Extension status is configuration metadata; display access is checked below.
			$extension = \Civi\Api4\Extension::get( false )
				->addWhere( 'key', '=', 'wpcvquicksearch' )
				->addWhere( 'status', '=', 'installed' )
				->setLimit( 1 )->execute()->first();
			if ( ! $extension ) { return array(); }
			$selection = array( 'saved_search' => 'WPCV_Quick_Search', 'display' => 'WPCV_Quick_Search_Autocomplete' );
			$displays = self::get( $selection );
			return ! is_wp_error( $displays ) && $displays ? $selection : array();
		} catch ( \Throwable $e ) {
			return array();
		}
	}

	/** Names identify a display across installations; numeric IDs are site-specific. */
	public static function selection( $display ) {
		return array( 'saved_search' => $display['saved_search_id.name'], 'display' => $display['name'] );
	}

	/** Encode the two names as one native dropdown option value. */
	public static function key( $selection ) {
		return wp_json_encode( $selection );
	}

	/** Return eligible displays, optionally restricted to one saved name pair. */
	public static function get( $selection = null ) {
		try {
			if ( ! civi_wp()->initialize() ) {
				return new WP_Error( 'quick_search_unavailable', __( 'CiviCRM contact search is unavailable.', 'wpcv-admin-ui' ) );
			}
			if ( ! CRM_Core_Permission::check( 'access CiviCRM' ) ) {
				return new WP_Error( 'quick_search_access', __( 'CiviCRM access is required to use Quick Search.', 'wpcv-admin-ui' ) );
			}
			$query = \Civi\Api4\SearchDisplay::get( true )
				->addSelect( 'name', 'label', 'saved_search_id.name', 'saved_search_id.api_entity' )
				->addWhere( 'type', '=', 'autocomplete' )
				->addWhere( 'saved_search_id.api_entity', '=', 'Contact' )
				->addOrderBy( 'label', 'ASC' )
				->setLimit( 0 );
			if ( null !== $selection ) {
				$query->addWhere( 'name', '=', $selection['display'] )
					->addWhere( 'saved_search_id.name', '=', $selection['saved_search'] );
			}
			$displays = array();
			foreach ( $query->execute() as $display ) {
				$displays[ self::key( self::selection( $display ) ) ] = $display;
			}
			return $displays;
		} catch ( \Throwable $e ) {
			return new WP_Error( 'quick_search_discovery', __( 'Autocomplete displays could not be loaded. Please try again.', 'wpcv-admin-ui' ) );
		}
	}
}
