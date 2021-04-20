window.addEventListener('load', () => {
	/**
	 * Converts images only if the option has been enabled.
	 */
	const sp_convertImagesEnabled = document.querySelector('input[name="splendid[convert_images_webp]"]').checked
	const sp_convertImagesJS = document.querySelector('.sp-convert-images-webp-js');

	if(sp_convertImagesEnabled && sp_convertImagesJS) {
		sp_convertImagesJS.querySelector('.sp-convert-images-webp-js-label').innerHTML = '0%';
		sp_convertImages();
	}	
})

/**
 * Recursively converts images until
 * all images are converted or until an error has occured.
 */
function sp_convertImages() {
	const sp_convertImagesJSProgress = document.querySelector('.sp-convert-images-webp-js-progress');
	const sp_convertImagesJSLabel = document.querySelector('.sp-convert-images-webp-js-label');

	wp.ajax.post('splendid_speed_convert_images_webp', {}).done((response) => {
		if(typeof response.error !== 'undefined' && response.error === 'Can\'t convert images. Contact your webmaster about lacking Imagick.') {
			return;
		}

		sp_convertImagesJSProgress.setAttribute('style', 'width: ' + response.progress + '%');
		sp_convertImagesJSLabel.innerHTML = response.progress + '%';

		if(response.progress !== 100) {
			sp_convertImages();
		}
	});
}