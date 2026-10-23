<?php
/** Find accessible CiviCRM reports and build their links. */
class Wpcv_Admin_Afforms {
	/** Bootstrap CiviCRM before using its API or resources. */
	public static function initialize() {
		try {
			if ( ! civi_wp()->initialize() || ! class_exists( '\Civi\Api4\Afform' ) ) {
				return new WP_Error( 'afform_unavailable', __( 'CiviCRM Form Builder is unavailable.', 'wpcv-admin' ) );
			}
		} catch ( \Throwable $e ) {
			return new WP_Error( 'afform_unavailable', __( 'CiviCRM Form Builder could not be initialized.', 'wpcv-admin' ) );
		}
		return true;
	}

	/** No cross-user cache: API4 checks the current user's Afform permissions. */
	public static function get( $names = null ) {
		$ready = self::initialize();
		if ( is_wp_error( $ready ) ) {
			return $ready;
		}
		try {
			// Discover routed forms explicitly placed in the Reports listing.
			$query = \Civi\Api4\Afform::get( true )
				->addSelect( 'name', 'title', 'description', 'server_route', 'icon', 'is_public' )
				->addWhere( 'type', 'IN', array( 'search', 'form' ) )
				->addWhere( 'placement', 'CONTAINS', 'reports' )
				->addWhere( 'server_route', 'IS NOT EMPTY' )
				->addOrderBy( 'title', 'ASC' )
				->setLimit( 0 );

			// Restrict dashboard and validation queries to the selected names.
			if ( null !== $names ) {
				if ( empty( $names ) ) {
					return array();
				}
				$query->addWhere( 'name', 'IN', $names );
			}

			// Index results by name so callers can restore the saved order.
			$forms = array();
			foreach ( $query->execute() as $form ) {
				$forms[ $form['name'] ] = $form;
			}
			return $forms;
		} catch ( \Throwable $e ) {
			return new WP_Error( 'afform_query_failed', __( 'Unable to retrieve CiviCRM forms. Please try again.', 'wpcv-admin' ) );
		}
	}

	/** Use the machine name only when the title is blank. */
	public static function title( $form ) {
		return isset( $form['title'] ) && '' !== trim( $form['title'] ) ? $form['title'] : $form['name'];
	}

	/** Let CiviCRM build the destination for this site and form visibility. */
	public static function url( $form ) {
		$frontend = ! empty( $form['is_public'] );
		return CRM_Utils_System::url( $form['server_route'], '', true, null, false, $frontend, ! $frontend );
	}

	/** Load icon styles from the installed CiviCRM assets. */
	public static function enqueue_icons() {
		if ( is_wp_error( self::initialize() ) ) {
			return;
		}
		$files = array( 'css/crm-i.css', 'bower_components/font-awesome/css/all.min.css', 'bower_components/font-awesome/css/v4-font-face.min.css', 'css/crm-i-v4-shims.css' );
		$previous = array();
		foreach ( $files as $index => $file ) {
			$handle = 'wpcv-afform-icons-' . $index;
			wp_enqueue_style( $handle, CRM_Core_Resources::singleton()->getUrl( 'civicrm', $file, true ), $previous, null );
			$previous = array( $handle );
		}
	}
}
