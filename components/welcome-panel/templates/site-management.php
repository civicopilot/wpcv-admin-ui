<?php
/** Display site tasks using the native welcome-panel grid and WordPress buttons. */
defined( 'ABSPATH' ) || exit;
?>
<div class="welcome-panel-column-container">
	<?php foreach ( $section['items'] as $item ) : ?>
		<div class="welcome-panel-column-content">
			<?php if ( ! empty( $item['dashicon'] ) ) : ?>
				<span class="dashicons <?php echo esc_attr( $item['dashicon'] ); ?>" aria-hidden="true"></span>
			<?php endif; ?>
			<a<?php echo ! empty( $item['primary'] ) ? ' class="button button-primary button-large"' : ''; ?> href="<?php echo esc_url( $item['url'] ); ?>">
				<?php echo esc_html( $item['title'] ); ?>
			</a>
			<?php if ( '' !== $item['description'] ) : ?>
				<p><?php echo esc_html( $item['description'] ); ?></p>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
