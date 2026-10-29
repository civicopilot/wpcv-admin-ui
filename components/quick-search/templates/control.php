<?php
/** Display the admin-bar contact search input and error message. */
defined( 'ABSPATH' ) || exit;
?>
			<div class="wpcv-admin-ui-quick-search crm-container">
				<label class="screen-reader-text" for="wpcv-admin-ui-quick-search"><?php esc_html_e( 'Search contacts', 'wpcv-admin-ui' ); ?></label>
				<input type="text" id="wpcv-admin-ui-quick-search" class="crm-form-text" placeholder="<?php esc_attr_e( 'Search contacts', 'wpcv-admin-ui' ); ?>" autocomplete="off" spellcheck="false" />
				<i class="crm-i fa-spinner fa-spin wpcv-admin-ui-quick-search-spinner" aria-hidden="true"></i>
				<p id="wpcv-admin-ui-quick-search-status" role="status" hidden></p>
			</div>
