/* global document */

// Import Modules.
import datePicker from './datePicker';
import clickHandler from './clickHandler';

const report = params => {
	// Ba if action is not defined
	if (typeof params === 'undefined' || params === null) {
		console.error('Action was not defined!'); // eslint-disable-line
		return false;
	}

	const target = params.target !== 'undefined' ? document.querySelector(`#${params.target}`) : null;

	datePicker(params.target);
	clickHandler(target, params);
};

export default report;
