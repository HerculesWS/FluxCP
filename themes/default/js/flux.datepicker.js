// Date selects (year / month / day):
//  - months are shown by name instead of a number,
//  - the day list only offers days that exist for the chosen year and month
//    (no Feb 30/31, and Feb 29 only in leap years),
//  - a field marked data-progressive reveals month after a year is picked, and day after a month.
document.addEventListener('DOMContentLoaded', function () {
	var monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July',
		'August', 'September', 'October', 'November', 'December'];

	var daysIn = function (year, month) {
		// Month is 1-12; day 0 of the next month is the last day of this one.
		return new Date(year, month, 0).getDate();
	};

	var fields = document.querySelectorAll('.date-field');

	for (var f = 0; f < fields.length; ++f) {
		enhance(fields[f]);
	}

	function enhance(field) {
		var year = field.querySelector('select[name$="_year"]');
		var month = field.querySelector('select[name$="_month"]');
		var day = field.querySelector('select[name$="_day"]');
		if (!year || !month || !day) {
			return;
		}

		var progressive = field.parentNode && field.parentNode.hasAttribute('data-progressive');

		// Month names.
		for (var m = 0; m < month.options.length; ++m) {
			var number = parseInt(month.options[m].value, 10);
			if (number >= 1 && number <= 12) {
				month.options[m].textContent = monthNames[number - 1];
			}
		}

		// Remember every possible day so the list can be rebuilt for any month length.
		var allDays = [];
		for (var d = 0; d < day.options.length; ++d) {
			allDays.push({value: day.options[d].value, text: day.options[d].textContent});
		}

		var rebuildDays = function () {
			var y = parseInt(year.value, 10);
			var mo = parseInt(month.value, 10);
			// Without a year, assume a leap year so Feb 29 stays selectable until a year is known.
			var max = mo ? daysIn(y || 2000, mo) : 31;
			var selected = day.value;

			day.innerHTML = '';
			for (var i = 0; i < allDays.length; ++i) {
				var n = parseInt(allDays[i].value, 10);
				if (allDays[i].value !== '' && n > max) {
					continue;
				}
				var option = document.createElement('option');
				option.value = allDays[i].value;
				option.textContent = allDays[i].text;
				day.appendChild(option);
			}

			// Keep the chosen day if it still exists; otherwise fall back to the last valid day.
			if (selected !== '' && parseInt(selected, 10) > max) {
				selected = allDays.length ? ('0' + max).slice(-2) : selected;
			}
			day.value = selected;
			if (day.value !== selected) {
				day.value = '';
			}
		};

		var reveal = function () {
			if (!progressive) {
				return;
			}
			month.hidden = year.value === '';
			day.hidden = year.value === '' || month.value === '';
		};

		year.addEventListener('change', function () {
			if (progressive && year.value === '') {
				month.value = '';
				day.value = '';
			}
			rebuildDays();
			reveal();
		});

		month.addEventListener('change', function () {
			if (progressive && month.value === '') {
				day.value = '';
			}
			rebuildDays();
			reveal();
		});

		rebuildDays();
		reveal();
	}
});
