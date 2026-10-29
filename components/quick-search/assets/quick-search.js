/* Search from one toolbar input using CiviCRM's SearchKit autocomplete API. */
/* global CRM, wpcvAdminUIQuickSearch */
document.addEventListener('DOMContentLoaded', function () {
	var input = document.getElementById('wpcv-admin-ui-quick-search');
	if (!input) return;
	var status = document.getElementById('wpcv-admin-ui-quick-search-status');
	var config = wpcvAdminUIQuickSearch;

	function announce(message) {
		status.textContent = message;
		status.hidden = !message;
	}

	try {
		var $ = CRM.$;
		var $input = $(input);
		var request = null;
		var generation = 0;
		var more = false;

		// Cancel immediately when typing or leaving, including during the debounce delay.
		function cancel() {
			generation++;
			if (request) request.abort();
			request = null;
			$input.attr('aria-busy', 'false');
			more = false;
			$input.autocomplete('close');
			announce('');
		}

		$input.autocomplete({
			appendTo: document.body,
			minLength: 1,
			delay: 250,
			autoFocus: false,
			source: function (query, respond) {
				var current = ++generation;
				if (request) request.abort();
				more = false;
				announce('');
				$input.attr('aria-busy', 'true');

				// Fetch one batch; admins can adjust "Autocomplete Results" in CiviCRM Search Preferences.
				request = $.ajax({
					url: CRM.url('civicrm/ajax/api4/Contact/autocomplete'),
					dataType: 'json',
					data: {params: JSON.stringify({
						input: query.term,
						savedSearch: config.savedSearch,
						display: config.display,
						quickEdit: false
					})}
				}).done(function (data) {
					if (current !== generation) { respond([]); return; }
					if (!Array.isArray(data.values)) {
						more = false;
						respond([]);
						announce(config.error);
						return;
					}
					more = data.countMatched > data.countFetched;
					respond(data.values);
					announce(data.values.length ? '' : config.noResults);
				}).fail(function (xhr, reason) {
					if (current !== generation || reason === 'abort') { respond([]); return; }
					more = false;
					respond([]);
					announce(config.error);
				}).always(function () {
					if (current === generation) {
						request = null;
						$input.attr('aria-busy', 'false');
					}
				});
			},
			focus: function (event, ui) {
				// Arrow keys highlight results without replacing the typed search term.
				var menu = $input.autocomplete('instance').menu;
				if (menu.active) $input.attr('aria-activedescendant', menu.active.children().first().attr('id'));
				return false;
			},
			select: function (event, ui) {
				if (/^\d+$/.test(String(ui.item.id))) {
					var url = new URL(config.contactUrl);
					url.searchParams.set('cid', ui.item.id);
					window.location.assign(url.href);
				}
				return false;
			},
			open: function () { $input.attr('aria-expanded', 'true'); },
			close: function () {
				$input.attr('aria-expanded', 'false').removeAttr('aria-activedescendant');
			}
		});

		var widget = $input.autocomplete('instance');
		widget.menu.option('role', 'listbox');
		widget.menu.option('items', '> :not(.wpcv-admin-ui-quick-search-hint)');
		widget.menu.element.addClass('crm-container wpcv-admin-ui-quick-search-dropdown');
		$input.attr({role: 'combobox', 'aria-autocomplete': 'list', 'aria-expanded': 'false',
			'aria-controls': widget.menu.element.attr('id'), 'aria-busy': 'false'});

		// Keep CiviCRM's escaped labels, icons and description formatting.
		widget._renderItem = function (list, item) {
			var content = $('<div>').html(CRM.utils.formatSelect2Result(item));
			return $('<li>').attr('aria-label', [item.label].concat(item.description || []).join(', '))
				.append(content).appendTo(list);
		};
		// Keep the refinement hint outside the selectable contact results.
		widget._renderMenu = function (list, items) {
			var autocomplete = this;
			items.forEach(function (item) { autocomplete._renderItemData(list, item); });
			if (more) $('<li>').addClass('wpcv-admin-ui-quick-search-hint')
				.attr('role', 'presentation').text(config.refine).appendTo(list);
		};
		widget._resizeMenu = function () { this.menu.element.outerWidth($input.outerWidth()); };

		$input.on('input', cancel).on('blur', cancel).on('keydown', function (event) {
			if (event.key === 'Escape') cancel();
		}).on('focus', function () {
			if (input.value) $input.autocomplete('search', input.value);
		});
		window.addEventListener('pagehide', cancel);
		document.body.classList.add('wpcv-admin-ui-quick-search-ready');
	} catch (error) {
		announce(config.error);
		console.warn('CiviCRM search could not initialize.', error);
	}
});
