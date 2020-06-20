/* global document */

const filterFields = (data, params) => {
	// Bail if params are not set.
	if (typeof data === 'undefined' || data === null || typeof params === 'undefined' || params === null) {
		return false;
	}

	const fromDateField = document.getElementById(`${params.target}-date-from`);
	const toDateField = document.getElementById(`${params.target}-date-to`);
	const testField = document.getElementById(`${params.target}-test`);

	if (typeof fromDateField !== 'undefined' && fromDateField != null) {
		data.append('from-date', fromDateField.value);
	}

	if (typeof toDateField !== 'undefined' && toDateField != null) {
		data.append('to-date', toDateField.value);
	}

	if (typeof testField !== 'undefined' && testField != null) {
		if (testField.checked) {
			data.append('test', 1);
		} else {
			data.append('test', 0);
		}
	}

	return data;
};

export default filterFields;
