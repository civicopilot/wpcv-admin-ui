/* Filter, select, and reorder reports on the settings page. */
(function ($) {
	'use strict';
	$(function () {
		var list = document.getElementById('wpcv-afform-list');
		if (!list) return;
		var max = Number(list.dataset.maxCards);
		var search = document.getElementById('wpcv-afform-search');
		var status = document.getElementById('wpcv-afform-search-status');
		// Filter locally and enforce the selection limit without clearing checked inputs.
		function refresh() {
			var term = search ? search.value.trim().toLocaleLowerCase() : '';
			var checked = list.querySelectorAll('input:checked').length;
			var matches = 0;
			Array.from(list.children).forEach(function (row) {
				var checkbox = row.querySelector('input');
				var match = (row.dataset.search || row.textContent).toLocaleLowerCase().includes(term);
				if (match) matches++;
				checkbox.disabled = !checkbox.checked && checked >= max;
				// Hidden checked inputs still submit in their saved order.
				row.hidden = !match;
			});
			status.textContent = term && !matches ? status.dataset.empty : '';
			if ($(list).data('ui-sortable')) $(list).sortable('refresh');
		}
		if (search) {
			search.addEventListener('keydown', function (event) {
				if (event.key === 'Enter') event.preventDefault();
			});
			search.addEventListener('input', refresh);
		}
		$(list).on('change', 'input', refresh);
		refresh();
		// Drag changes DOM order, which is also the order submitted on save.
		if ($.fn.sortable) {
			$(list).sortable({
				items: '> li:not([hidden])',
				handle: '.wpcv-afform-handle', axis: 'y', placeholder: 'wpcv-afform-placeholder'
			});
		}
	});
})(jQuery);
