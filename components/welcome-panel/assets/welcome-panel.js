/* Position the Welcome Panel and control visibility and text contrast. */
(function ($) {
	'use strict';
	$(function () {
		// Place the welcome panel above widgets and apply its visibility policy.
		var panel = $('#wpcv-admin-ui-welcome-panel');
		if (panel.length) {
			var visibility = $('#wpcv_admin_ui_widget-hide');
			var persist = panel.attr('data-persist') === '1';
			panel.insertBefore('#dashboard-widgets-wrap');
			if (persist) {
				visibility.prop('checked', true).prop('disabled', true);
			}

			function updateVisibility() {
				panel.toggle(persist || !visibility.length || visibility.prop('checked'));
			}
			visibility.on('change', updateVisibility);
			updateVisibility();

			// Resolve the CSS variable in the browser, including the CiviCRM theme default.
			var context = document.createElement('canvas').getContext('2d');
			if (context) {
				context.fillStyle = window.getComputedStyle(panel.find('.welcome-panel-header')[0]).backgroundColor;
				context.fillRect(0, 0, 1, 1);
				var pixel = context.getImageData(0, 0, 1, 1).data;
				var channels = Array.from(pixel).slice(0, 3).map(function (value) {
					value /= 255;
					return value <= 0.04045 ? value / 12.92 : Math.pow((value + 0.055) / 1.055, 2.4);
				});
				var luminance = channels[0] * 0.2126 + channels[1] * 0.7152 + channels[2] * 0.0722;

				// Choose whichever of black or white has the higher contrast ratio.
				var foreground = (luminance + 0.05) / 0.05 > 1.05 / (luminance + 0.05) ? '#000' : '#fff';
				panel[0].style.setProperty('--wpcv-admin-ui-welcome-panel-foreground', foreground);
			}

			// Retain the registered widget so WordPress can save its visibility preference.
		}
	});
})(jQuery);
