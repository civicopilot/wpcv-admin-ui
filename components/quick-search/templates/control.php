<?php
/** Display the admin-bar contact search input and error message. */
defined( 'ABSPATH' ) || exit;
?>
			<div class="wpcv-admin-ui-quick-search crm-container">
				<label class="screen-reader-text" for="wpcv-admin-ui-quick-search"><?php esc_html_e( 'Search contacts', 'wpcv-admin-ui' ); ?></label>
				<input type="hidden" id="wpcv-admin-ui-quick-search" />
				<p id="wpcv-admin-ui-quick-search-status" role="status" hidden></p>
			</div>
