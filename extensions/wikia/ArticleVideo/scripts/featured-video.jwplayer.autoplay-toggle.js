define('wikia.articleVideo.featuredVideo.jwplayer.autoplayToggle', ['wikia.articleVideo.featuredVideo.autoplay'], function (featuredVideoAutoplay) {

	function addAutoplayToggle() {
		var $player = $('#featured-video__player'),
			$settingsTopbar = $player.find('.jw-settings-menu .jw-settings-topbar');

		$settingsTopbar
			.find('.jw-settings-close')
			.before(
				'<input type="checkbox" id="featuredVideoAutoplayToggle" class="wds-switch__input"' +
				(featuredVideoAutoplay.isAutoplayEnabled() ? ' checked>' : '>') +
				'<label for="featuredVideoAutoplayToggle" class="wds-switch__label">Autoplay</label>'
			);

		$settingsTopbar
			.find('.wds-switch__label')
			.on('mouseup pointerup touchstart', function (event) {
				event.stopPropagation();
				featuredVideoAutoplay.toggleAutoplay(!event.target.previousSibling.checked);
			});
	}

	return function (playerInstance) {
		if (featuredVideoAutoplay.inAutoplayCountries) {
			addAutoplayToggle();

			playerInstance.on('resize', function (data) {
				if (data.width > 600 && !$settingsTopbar.contains('#featuredVideoAutoplayToggle')) {
					addAutoplayToggle();
				}
			});
		}
	}
});
