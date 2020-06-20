/* global ajaxurl, document, Blob */

// Import Packages
import axios from 'axios';
import { Parser } from 'json2csv';

const buttonReset = (button, buttonText) => {
	setTimeout(() => {
		button.innerHTML = buttonText; // eslint-disable-line
	}, 3000);
};

const updateButton = (button, text) => {
	button.innerHTML = text; // eslint-disable-line
	button.classList.remove('loading');
};

const buildReport = (response, params, button, buttonText) => {
	let text;

	try {
		const json2csvParser = new Parser();
		const csv = json2csvParser.parse(response.data);
		const today = new Date();
		const date = `${today.getFullYear()}-${today.getMonth() + 1}-${today.getDate()}`;
		const downloadLink = document.createElement('a');
		const blob = new Blob(['\ufeff', csv]);
		const url = URL.createObjectURL(blob);
		text = 'Report Successfully Built'; // eslint-disable-line

		downloadLink.href = url;
		downloadLink.download = `${params.filename}-${date}.csv`;
		document.body.appendChild(downloadLink);
		downloadLink.click();
		document.body.removeChild(downloadLink);
	} catch (error) {
		if (Object.keys(response.data).length === 0) {
			text = 'Report Is Empty'; // eslint-disable-line
		} else if (response.data === 'sent') {
			text = 'Report Was Sent'; // eslint-disable-line
		} else {
			text = 'Report Build Failed'; // eslint-disable-line
			console.error(error); // eslint-disable-line
		}

		updateButton(button, text);
		buttonReset(button, buttonText);
		return false;
	}

	updateButton(button, text);
	buttonReset(button, buttonText);
};

const ajaxHandler = (data, params, button, buttonText) => {
	// Bail if params are not set.
	if (
		typeof data === 'undefined' ||
		data === null ||
		typeof params === 'undefined' ||
		params === null ||
		typeof button === 'undefined' ||
		button === null ||
		typeof buttonText === 'undefined' ||
		buttonText === null
	) {
		return false;
	}

	axios
		.post(ajaxurl, data)
		.then(response => {
			buildReport(response, params, button, buttonText);
		})
		.catch(error => {
			if (error) {
				updateButton(button, 'Report Build Failed');
				buttonReset(button, buttonText);
			}
			console.error(error); // eslint-disable-line
		});
};

export default ajaxHandler;
