// Regroups the "label control ... label control" paragraphs of search forms into
// separate fields (label over control) so the CSS can lay them out in a grid.
document.addEventListener('DOMContentLoaded', function () {
	var forms = document.querySelectorAll('form.search-form, form.search-form2');

	var isButton = function (el) {
		if (el.tagName === 'BUTTON') {
			return true;
		}
		return el.tagName === 'INPUT' && /^(submit|button|reset)$/i.test(el.type);
	};

	var isControl = function (el) {
		return /^(INPUT|SELECT|TEXTAREA)$/.test(el.tagName);
	};

	for (var f = 0; f < forms.length; ++f) {
		var paras = forms[f].querySelectorAll(':scope > p');

		for (var p = 0; p < paras.length; ++p) {
			var para = paras[p];
			var nodes = Array.prototype.slice.call(para.childNodes);
			var fields = [];
			var actions = null;
			var field = null;
			var controls = null;

			var start = function () {
				field = document.createElement('div');
				field.className = 'sf-field';
				controls = document.createElement('div');
				controls.className = 'sf-controls';
				field.appendChild(controls);
				fields.push(field);
			};

			for (var n = 0; n < nodes.length; ++n) {
				var node = nodes[n];

				if (node.nodeType === 3) {
					// Plain text: drop whitespace and the "..." separators, keep anything else.
					if (/^[\s.\u2026]*$/.test(node.nodeValue)) {
						continue;
					}
					if (!field) {
						start();
					}
					controls.appendChild(node);
				}
				else if (node.nodeType === 1 && node.tagName === 'LABEL') {
					start();
					field.insertBefore(node, controls);
				}
				else if (node.nodeType === 1 && isButton(node)) {
					if (!actions) {
						actions = document.createElement('div');
						actions.className = 'sf-actions';
					}
					actions.appendChild(node);
				}
				else if (node.nodeType === 1 && isControl(node)) {
					if (!field) {
						start();
					}
					controls.appendChild(node);
				}
				else if (node.nodeType === 1) {
					if (!field) {
						start();
					}
					controls.appendChild(node);
				}
			}

			// Whatever is left (the "..." separators) is dropped.
			while (para.firstChild) {
				para.removeChild(para.firstChild);
			}

			para.className += ' sf-grid';

			for (var i = 0; i < fields.length; ++i) {
				para.appendChild(fields[i]);
			}
			if (actions) {
				para.appendChild(actions);
			}
		}
	}
});
