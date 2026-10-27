/* Add content-type labels beside Activity widget titles. */
(function ($) {
	'use strict';
	$(function () {
		// Keep each Activity title and its type label in the same grid cell.
		var activityLabels = window.wpcvAdminUIActivityLabels || {};
		document.querySelectorAll('#dashboard_activity #published-posts li > a, #dashboard_activity #future-posts li > a').forEach(function (link) {
			var label = activityLabels[link.href];
			if (!label) return;
			var badge = document.createElement('small');
			badge.className = 'wpcv-admin-ui-activity-type';
			badge.textContent = label;
			var title = document.createElement('div');
			title.className = 'wpcv-admin-ui-activity-title';
			link.before(title);
			title.append(link, badge);
		});
	});
})(jQuery);
