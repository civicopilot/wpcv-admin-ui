/* Enable WordPress settings metabox controls. */
(function ($) {
	'use strict';
	$(function () {
		// Enable native settings metabox controls.
		var boxes = document.querySelector('[data-wpcv-screen]');
		if (boxes && window.postboxes) window.postboxes.add_postbox_toggles(boxes.dataset.wpcvScreen);
	});
})(jQuery);
