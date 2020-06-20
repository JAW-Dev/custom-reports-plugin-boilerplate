/* global document, jQuery */

const datePicker = target => {
	if (typeof target === 'undefined' || target === null) {
		return false;
	}

	jQuery(document).ready(() => {
		const from = jQuery(`#${target}-date-from`);
		if (from.length > 0) {
			jQuery(from).datepicker();
		}

		const to = jQuery(`#${target}-date-to`);
		if (to.length > 0) {
			jQuery(to).datepicker();
		}
	});
};

export default datePicker;
