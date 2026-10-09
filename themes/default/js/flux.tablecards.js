// Copies each column heading onto its cells (data-label) so wide list tables
// can be shown as one card per row on small screens (see the CSS cards-ready rules).
document.addEventListener('DOMContentLoaded', function () {
	var clean = function (text) {
		return text.replace(/[▲▼↑↓]/g, '').replace(/\s+/g, ' ').trim();
	};

	var tables = document.querySelectorAll('table.horizontal-table, table.vertical-table');

	for (var t = 0; t < tables.length; ++t) {
		var rows = tables[t].rows;
		var head = rows.length ? rows[0] : null;

		if (!head || !head.cells.length) {
			continue;
		}

		var labels = [];
		var headerOnly = true;

		for (var h = 0; h < head.cells.length; ++h) {
			if (head.cells[h].tagName !== 'TH') {
				headerOnly = false;
				break;
			}

			// A heading that spans columns labels the last of them (e.g. icon + name).
			for (var k = 1; k < head.cells[h].colSpan; ++k) {
				labels.push('');
			}
			labels.push(clean(head.cells[h].textContent));
		}

		var isVertical = /(^|\s)vertical-table(\s|$)/.test(tables[t].className);

		if (!headerOnly || rows.length < 2 || (isVertical && labels.length < 3)) {
			continue;
		}

		var mapped = 0;

		for (var r = 1; r < rows.length; ++r) {
			var cells = rows[r].cells;
			var span = 0;

			for (var c = 0; c < cells.length; ++c) {
				span += cells[c].colSpan;
			}

			if (span !== labels.length) {
				continue;
			}

			// A cell spanning columns takes the label of the last column it covers.
			var col = 0;

			for (var d = 0; d < cells.length; ++d) {
				col += cells[d].colSpan;
				cells[d].setAttribute('data-label', labels[col - 1]);
			}

			// Long cards start collapsed to their first fields, with a toggle for the rest.
			if (cells.length > 5) {
				var toggle = document.createElement('td');
				toggle.className = 'card-toggle';
				toggle.setAttribute('role', 'button');
				toggle.setAttribute('tabindex', '0');
				rows[r].appendChild(toggle);
				rows[r].className += ' card-collapsed';
			}

			++mapped;
		}

		if (mapped > 0) {
			head.className += ' cards-head';
			tables[t].className += ' cards-ready';
		}
		else if (isVertical) {
			// A heading-row list we cannot label: keep it a normal table.
			tables[t].className += ' table-fallback';
		}
	}
});

// Expand or collapse a card when its toggle is clicked (or activated by keyboard).
(function () {
	var flip = function (target) {
		if (!target.closest) {
			return;
		}
		var toggle = target.closest('td.card-toggle');
		if (!toggle) {
			return;
		}
		var row = toggle.parentNode;
		if (/(^|\s)card-collapsed(\s|$)/.test(row.className)) {
			row.className = row.className.replace(/(^|\s)card-collapsed(\s|$)/, ' ').trim() + ' card-open';
		}
		else {
			row.className = row.className.replace(/(^|\s)card-open(\s|$)/, ' ').trim() + ' card-collapsed';
		}
	};

	document.addEventListener('click', function (e) {
		flip(e.target);
	});
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Enter' || e.key === ' ') {
			if (e.target.closest && e.target.closest('td.card-toggle')) {
				e.preventDefault();
				flip(e.target);
			}
		}
	});
})();
