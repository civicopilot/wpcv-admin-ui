<?php
/** Display a report using WordPress's icon-and-content column structure. */
defined( 'ABSPATH' ) || exit;
?>
<div<?php echo empty( $card['icon'] ) ? '' : ' class="welcome-panel-column"'; ?>>
	<?php if ( ! empty( $card['icon'] ) ) : ?>
		<i class="crm-i <?php echo esc_attr( $card['icon'] ); ?> wpcv-admin-ui-afform-icon" aria-hidden="true"></i>
	<?php endif; ?>
	<div class="welcome-panel-column-content">
		<h3><a href="<?php echo esc_url( $card['url'] ); ?>"><?php echo esc_html( $card['title'] ); ?></a></h3>
		<?php if ( '' !== $card['description'] ) : ?>
			<p><?php echo esc_html( $card['description'] ); ?></p>
		<?php endif; ?>
	</div>
</div>
