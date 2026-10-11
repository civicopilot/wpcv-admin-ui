/* Start contact autocomplete and open the selected contact. */
/* global CRM, wpcvQuickSearch */
document.addEventListener('DOMContentLoaded', function () {
	var input = document.getElementById('wpcv-quick-search');
	if (!input) return;
	try {
		// Use CiviCRM's jQuery instance, where its autocomplete plugin is registered.
		CRM.$(input).crmAutocomplete('Contact', {
			savedSearch: wpcvQuickSearch.savedSearch,
			display: wpcvQuickSearch.display
		}, {
			placeholder: wpcvQuickSearch.placeholder,
			width: '100%',
			dropdownCssClass: 'crm-container wpcv-quick-search-dropdown'
		}).on('select2-selected.wpcv', function (event) {
			if (!/^\d+$/.test(String(event.val))) return;
			var url = new URL(wpcvQuickSearch.contactUrl);
			url.searchParams.set('cid', event.val);
			window.location.assign(url.href);
		});
		var search = input.closest('.wpcv-quick-search');
		// Capture outside clicks before Select2's overlay stops propagation.
		document.addEventListener('click', function (event) {
			if (search.contains(event.target) || event.target.closest('.wpcv-quick-search-dropdown')) return;
			// Select2 restores focus when closing; release it after that handler finishes.
			// Keyboard dismissal has no click, so Escape keeps its focus ring.
			window.setTimeout(function () {
				if (search.contains(document.activeElement)) document.activeElement.blur();
			}, 0);
		}, true);
		// This class also hides a CiviCRM menu inserted later by its AJAX loader.
		document.body.classList.add('wpcv-quick-search-ready');
	} catch (error) {
		var status = document.getElementById('wpcv-quick-search-status');
		status.textContent = wpcvQuickSearch.error;
		status.hidden = false;
		console.warn('CiviCRM search could not initialize.', error);
	}
});
