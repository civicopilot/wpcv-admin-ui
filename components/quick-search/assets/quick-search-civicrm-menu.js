/* Move the existing search when CiviCRM replaces the WordPress toolbar. */
/* global CRM */
document.addEventListener('DOMContentLoaded', function () {
	var input = document.getElementById('wpcv-admin-ui-quick-search');
	var toolbar = document.getElementById('wpadminbar');
	if (!input || !toolbar || !document.body.classList.contains('wpcv-admin-ui-quick-search-ready')) return;

	var control = input.closest('.wpcv-admin-ui-quick-search');
	var home = control.parentNode;
	var slot = document.createElement('li');
	slot.id = 'wpcv-admin-ui-quick-search-menu';

	function updatePlacement() {
		var menu = document.getElementById('civicrm-menu');
		var useMenu = menu && document.body.classList.contains('crm-menubar-visible') &&
			document.body.classList.contains('crm-menubar-over-cms-menu') &&
			window.getComputedStyle(toolbar).visibility === 'hidden';
		var target = useMenu ? slot : home;
		if (control.parentNode === target && (!useMenu || slot.parentNode === menu)) return;

		// Cancel pending results before moving; preserve the input and its search text.
		CRM.$(input).triggerHandler('blur');
		if (useMenu) {
			var logo = menu.querySelector(':scope > li[data-name="Home"]');
			if (logo) logo.after(slot);
			else menu.prepend(slot);
		}
		target.appendChild(control);
		if (!useMenu) slot.remove();
	}

	new MutationObserver(updatePlacement).observe(document.body, {
		attributes: true,
		attributeFilter: ['class']
	});
	CRM.$(document).on('crmLoad', '#civicrm-menu', updatePlacement);
	window.addEventListener('resize', updatePlacement);
	updatePlacement();
});
