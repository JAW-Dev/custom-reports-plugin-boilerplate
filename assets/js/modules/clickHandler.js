/* global document, FormData */

// Import Modules
import ajaxHandler from './ajaxHandler';
import filterFields from './filterFields';

const clickHandler = (target, params) => {
	// Bail if params are not set.
	if (target === null || typeof params === 'undefined' || params === null) {
		return false;
	}

	target.addEventListener('click', event => {
		event.preventDefault();

		const button = params.target !== 'undefined' ? document.querySelector(`#${params.target}`) : '';
		const buttonText = button ? button.textContent : '';

		if (button) {
			button.innerHTML = 'Building Report';
			button.classList.add('loading');
		}

		let data = new FormData();
		const { nonce } = target.dataset;

		data.append('action', params.action);
		data.append('nonce', nonce);

		data = filterFields(data, params);

		if (params.data !== undefined) {
			Object.keys(params.data).forEach(key => {
				data.append(key, params.data[key]);
			});
		}

		ajaxHandler(data, params, button, buttonText);
	});
};

export default clickHandler;
