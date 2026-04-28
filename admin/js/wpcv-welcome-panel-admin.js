(function( $ ) {
	'use strict';

	$( function() {
		var $dashboardPanel = $( '#wpcv-welcome-panel-dashboard' );

		if ( $dashboardPanel.length ) {
			$dashboardPanel.insertBefore( '#dashboard-widgets-wrap' ).show();
			$( '#wpcv_welcome_panel_widget' ).remove();
		}

		var $list = $( '.wpcv-welcome-panel-settings-list' );

		if ( ! $list.length ) {
			return;
		}

		var maxCards = parseInt( $list.data( 'max-cards' ), 10 ) || 6;

		function updateRowTitles() {
			$list.find( '[data-card-row]' ).each( function( index ) {
				$( this ).find( '[data-card-title]' ).text( 'Card ' + ( index + 1 ) );
			} );
		}

		function updateAddButtonState() {
			var visibleRows = $list.find( '[data-card-row]' ).not( '.is-empty' ).length;
			$( '[data-add-card]' ).prop( 'disabled', visibleRows >= maxCards );
		}

		$( '[data-add-card]' ).on( 'click', function() {
			var $row = $list.find( '[data-card-row].is-empty' ).first();

			if ( ! $row.length ) {
				return;
			}

			$row.removeClass( 'is-empty' );
			updateRowTitles();
			updateAddButtonState();
		} );

		$list.on( 'click', '[data-remove-card]', function() {
			var $row = $( this ).closest( '[data-card-row]' );

			$row.find( 'input[type="text"], textarea' ).val( '' );
			$row.find( 'select' ).prop( 'selectedIndex', 0 );
			$row.addClass( 'is-empty' );
			updateRowTitles();
			updateAddButtonState();
		} );

		$list.on( 'click', '[data-move-up]', function() {
			var $row = $( this ).closest( '[data-card-row]' );
			var $prev = $row.prev( '[data-card-row]' );

			if ( ! $prev.length ) {
				return;
			}

			$row.insertBefore( $prev );
			updateRowTitles();
		} );

		$list.on( 'click', '[data-move-down]', function() {
			var $row = $( this ).closest( '[data-card-row]' );
			var $next = $row.next( '[data-card-row]' );

			if ( ! $next.length ) {
				return;
			}

			$row.insertAfter( $next );
			updateRowTitles();
		} );

		updateRowTitles();
		updateAddButtonState();
	} );

})( jQuery );
