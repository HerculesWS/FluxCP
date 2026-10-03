// Turns the grid of search fields (see flux.searchform.js) into a GitHub-style filter bar:
// a single bar, a menu of available filters, and an editable "chip" for each active filter.
// The original fields are moved (not copied) into the chips, so the form still submits exactly
// what it always did.
document.addEventListener('DOMContentLoaded', function () {
	var forms = document.querySelectorAll('form.search-form, form.search-form2');

	var el = function (tag, className, text) {
		var node = document.createElement(tag);
		if (className) {
			node.className = className;
		}
		if (text) {
			node.textContent = text;
		}
		return node;
	};

	// Replaces a comparison <select> with a small centered popup; the select stays in place
	// (hidden) and is what the form submits.
	var openPopups = [];
	var closePopups = function () {
		while (openPopups.length) {
			openPopups.pop().hidden = true;
		}
	};
	document.addEventListener('click', closePopups);

	var operatorPicker = function (select) {
		var wrap = el('span', 'fb-opwrap');
		var button = el('button', 'fb-opbtn');
		button.type = 'button';
		button.title = 'Comparison';
		var popup = el('span', 'fb-oppop');
		popup.hidden = true;

		select.parentNode.insertBefore(wrap, select);
		select.className += ' fb-op-hidden';
		select.tabIndex = -1;
		wrap.appendChild(button);
		wrap.appendChild(popup);
		wrap.appendChild(select);

		var sync = function () {
			var current = select.options[select.selectedIndex];
			button.textContent = current ? current.textContent : '';
			for (var i = 0; i < popup.children.length; ++i) {
				popup.children[i].className = 'fb-opitem' + (popup.children[i].getAttribute('data-index') === String(select.selectedIndex) ? ' fb-opitem-on' : '');
			}
		};
		select.fbSync = sync;

		for (var i = 0; i < select.options.length; ++i) {
			(function (index) {
				var choice = el('button', 'fb-opitem', select.options[index].textContent);
				choice.type = 'button';
				choice.title = select.options[index].title || '';
				choice.setAttribute('data-index', String(index));
				choice.addEventListener('click', function (e) {
					e.stopPropagation();
					select.selectedIndex = index;
					select.dispatchEvent(new Event('change', {bubbles: true}));
					sync();
					popup.hidden = true;
					button.focus();
				});
				popup.appendChild(choice);
			})(i);
		}

		button.addEventListener('click', function (e) {
			e.stopPropagation();
			var wasHidden = popup.hidden;
			closePopups();
			popup.hidden = !wasHidden;
			if (!popup.hidden) {
				openPopups.push(popup);
			}
		});
		button.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				popup.hidden = true;
			}
		});
		sync();
	};

	for (var f = 0; f < forms.length; ++f) {
		build(forms[f]);
	}

	function build(form) {
		var wraps = Array.prototype.slice.call(form.querySelectorAll('.sf-field'));
		if (!wraps.length) {
			return;
		}

		var items = [];
		for (var w = 0; w < wraps.length; ++w) {
			var label = wraps[w].querySelector('label');
			var controls = wraps[w].querySelector('.sf-controls');
			if (!label || !controls) {
				continue;
			}
			items.push({
				wrap: wraps[w],
				label: label,
				controls: controls,
				text: label.textContent.replace(/:\s*$/, '').trim(),
				chip: null
			});
		}
		if (!items.length) {
			return;
		}

		// Comparison dropdowns (is equal to / less than / greater than) read better as symbols.
		var symbols = {eq: '=', ne: '≠', lt: '<', gt: '>', lte: '≤', gte: '≥'};
		for (var o = 0; o < items.length; ++o) {
			var opSelects = items[o].controls.querySelectorAll('select');
			for (var os = 0; os < opSelects.length; ++os) {
				if (!/_op$/.test(opSelects[os].name)) {
					continue;
				}
				opSelects[os].className += ' fb-op';
				for (var oi = 0; oi < opSelects[os].options.length; ++oi) {
					var option = opSelects[os].options[oi];
					if (symbols[option.value]) {
						option.title = option.textContent.trim();
						option.textContent = symbols[option.value];
					}
				}
				operatorPicker(opSelects[os]);
			}
		}

		// The "main" field is the one plain typed text searches: the first name-like text field.
		var main = null;
		for (var m = 0; m < items.length; ++m) {
			if (/name/i.test(items[m].text) && items[m].controls.querySelector('input[type=text]')) {
				main = items[m];
				break;
			}
		}

		// ---- UI skeleton
		var ui = el('div', 'fb');
		var row = el('div', 'fb-row');
		var bar = el('div', 'fb-bar');
		var icon = el('span', 'fb-icon');
		var chips = el('div', 'fb-chips');
		var input = el('input', 'fb-input');
		input.type = 'text';
		input.setAttribute('autocomplete', 'off');
		input.setAttribute('aria-label', 'Filter');
		input.placeholder = main ? 'Search by ' + main.text.toLowerCase() + ' or add a filter' : 'Add a filter';
		var clear = el('button', 'fb-clear', '×');
		clear.type = 'button';
		clear.title = 'Clear all filters';
		clear.hidden = true;
		bar.appendChild(icon);
		bar.appendChild(chips);
		bar.appendChild(input);
		bar.appendChild(clear);
		var actions = el('div', 'fb-actions');
		row.appendChild(bar);
		row.appendChild(actions);
		var menu = el('div', 'fb-menu');
		menu.hidden = true;
		ui.appendChild(row);
		ui.appendChild(menu);

		// Existing action buttons (Search / Reset) sit beside the bar.
		var oldActions = form.querySelectorAll('.sf-actions');
		for (var a = 0; a < oldActions.length; ++a) {
			while (oldActions[a].firstChild) {
				actions.appendChild(oldActions[a].firstChild);
			}
		}

		// The fields stay in the form (hidden) so unused filters still submit their defaults.
		var store = el('div', 'fb-store');
		store.style.display = 'none';
		for (var s = 0; s < items.length; ++s) {
			store.appendChild(items[s].wrap);
		}
		var firstPara = form.querySelector('p');
		form.insertBefore(ui, firstPara);
		form.appendChild(store);
		var paras = form.querySelectorAll(':scope > p');
		for (var p = 0; p < paras.length; ++p) {
			paras[p].style.display = 'none';
		}

		// Show the bar right away; the old "Search..." toggle link is no longer needed.
		form.style.display = 'block';
		form.className += ' fb-ready';
		var toggler = form.previousElementSibling;
		if (toggler && /(^|\s)toggler(\s|$)/.test(toggler.className)) {
			toggler.style.display = 'none';
		}

		// ---- helpers
		function isActive(item) {
			var texts = item.controls.querySelectorAll('input[type=text],input[type=number],input:not([type])');
			for (var t = 0; t < texts.length; ++t) {
				if (texts[t].value.replace(/\s+/g, '') !== '') {
					return true;
				}
			}
			var selects = item.controls.querySelectorAll('select');
			for (var q = 0; q < selects.length; ++q) {
				if (/_op$/.test(selects[q].name)) {
					continue;
				}
				if (selects[q].selectedIndex > 0) {
					return true;
				}
			}
			return false;
		}

		function resetControls(item) {
			var texts = item.controls.querySelectorAll('input[type=text],input[type=number],input:not([type])');
			for (var t = 0; t < texts.length; ++t) {
				texts[t].value = '';
			}
			var selects = item.controls.querySelectorAll('select');
			for (var q = 0; q < selects.length; ++q) {
				selects[q].selectedIndex = 0;
				if (selects[q].fbSync) {
					selects[q].fbSync();
				}
			}
		}

		function refresh() {
			var any = chips.children.length > 0 || input.value !== '';
			clear.hidden = !any;
		}

		function activate(item, focus) {
			if (item.chip) {
				return;
			}
			var chip = el('div', 'fb-chip');
			chip.appendChild(item.label);
			chip.appendChild(item.controls);
			var remove = el('button', 'fb-chip-remove', '×');
			remove.type = 'button';
			remove.title = 'Remove filter';
			remove.addEventListener('click', function (e) {
				e.stopPropagation();
				deactivate(item);
				input.focus();
			});
			chip.appendChild(remove);
			chips.appendChild(chip);
			item.chip = chip;
			refresh();
			if (focus) {
				var first = item.controls.querySelector('input,select');
				if (first) {
					first.focus();
				}
			}
		}

		function deactivate(item) {
			if (!item.chip) {
				return;
			}
			resetControls(item);
			item.wrap.appendChild(item.label);
			item.wrap.appendChild(item.controls);
			chips.removeChild(item.chip);
			item.chip = null;
			refresh();
			renderMenu();
		}

		function renderMenu() {
			var query = input.value.trim().toLowerCase();
			menu.innerHTML = '';
			menu.appendChild(el('div', 'fb-menu-title', 'Filter by…'));
			var shown = 0;
			for (var i = 0; i < items.length; ++i) {
				if (items[i].chip) {
					continue;
				}
				if (query && items[i].text.toLowerCase().indexOf(query) === -1) {
					continue;
				}
				(function (item) {
					var option = el('button', 'fb-item', item.text);
					option.type = 'button';
					option.addEventListener('click', function () {
						activate(item, true);
						input.value = '';
						closeMenu();
					});
					menu.appendChild(option);
					++shown;
				})(items[i]);
			}
			if (!shown) {
				menu.appendChild(el('div', 'fb-empty', 'No more filters'));
			}
		}

		function openMenu() {
			renderMenu();
			menu.hidden = false;
		}

		function closeMenu() {
			menu.hidden = true;
		}

		function submit() {
			if (form.requestSubmit) {
				form.requestSubmit();
			}
			else {
				form.submit();
			}
		}

		// ---- initial chips from the current query
		for (var n = 0; n < items.length; ++n) {
			if (isActive(items[n])) {
				activate(items[n], false);
			}
		}
		refresh();

		// ---- events
		bar.addEventListener('click', function (e) {
			if (e.target === bar || e.target === chips || e.target === icon) {
				input.focus();
			}
		});

		input.addEventListener('focus', openMenu);
		input.addEventListener('input', function () {
			renderMenu();
			menu.hidden = false;
			refresh();
		});

		input.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				closeMenu();
			}
			else if (e.key === 'ArrowDown') {
				var firstItem = menu.querySelector('.fb-item');
				if (firstItem) {
					e.preventDefault();
					firstItem.focus();
				}
			}
			else if (e.key === 'Backspace' && input.value === '') {
				var last = chips.lastElementChild;
				if (last) {
					for (var k = items.length - 1; k >= 0; --k) {
						if (items[k].chip === last) {
							deactivate(items[k]);
							break;
						}
					}
				}
			}
			else if (e.key === 'Enter') {
				var typed = input.value.trim();
				e.preventDefault();
				if (typed && main) {
					main.controls.querySelector('input[type=text]').value = typed;
					activate(main, false);
					input.value = '';
					submit();
				}
				else if (typed) {
					var firstMatch = menu.querySelector('.fb-item');
					if (firstMatch) {
						firstMatch.click();
					}
				}
				else {
					submit();
				}
			}
		});

		menu.addEventListener('keydown', function (e) {
			var current = document.activeElement;
			if (e.key === 'ArrowDown' && current.nextElementSibling) {
				e.preventDefault();
				current.nextElementSibling.focus();
			}
			else if (e.key === 'ArrowUp') {
				e.preventDefault();
				var previous = current.previousElementSibling;
				if (previous && previous.className.indexOf('fb-item') !== -1) {
					previous.focus();
				}
				else {
					input.focus();
				}
			}
			else if (e.key === 'Escape') {
				closeMenu();
				input.focus();
			}
		});

		clear.addEventListener('click', function () {
			for (var i = 0; i < items.length; ++i) {
				deactivate(items[i]);
			}
			input.value = '';
			refresh();
			input.focus();
		});

		document.addEventListener('click', function (e) {
			if (!ui.contains(e.target)) {
				closeMenu();
			}
		});
	}
});
