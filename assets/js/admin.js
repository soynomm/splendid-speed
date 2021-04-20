window.addEventListener('load', () => {
	// When the switch changes, submit the form
	const form = document.querySelector('#splendid-speed');

	form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
		checkbox.addEventListener('change', () => {
			setTimeout(() => form.submit(), 250);
		});
	});
});