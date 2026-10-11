<?php
/** Display the admin-bar contact search input and error message. */
defined( 'ABSPATH' ) || exit;
?>
			<div class="wpcv-quick-search crm-container">
				<label class="screen-reader-text" for="wpcv-quick-search"><?php esc_html_e( 'Search contacts', 'wpcv-admin' ); ?></label>
				<input type="hidden" id="wpcv-quick-search" />
				<p id="wpcv-quick-search-status" role="status" hidden></p>
			</div>
