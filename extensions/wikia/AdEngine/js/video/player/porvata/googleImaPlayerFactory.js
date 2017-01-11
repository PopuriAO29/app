/*global define, google*/
define('ext.wikia.adEngine.video.player.porvata.googleImaPlayerFactory', [
	'ext.wikia.adEngine.video.player.porvata.googleImaSetup',
	'wikia.document',
	'wikia.log'
], function(imaSetup, doc, log) {
	'use strict';
	var logGroup = 'ext.wikia.adEngine.video.player.porvata.googleImaPlayerFactory';

	function create(adDisplayContainer, adsLoader, params) {
		var isAdsManagerLoaded = false,
			status = '',
			videoMock = doc.createElement('video'),
			adsManager;

		function adsManagerLoadedCallback(adsManagerLoadedEvent) {
			adsManager = adsManagerLoadedEvent.getAdsManager(videoMock, imaSetup.getRenderingSettings());
			isAdsManagerLoaded = true;

			log('AdsManager loaded', log.levels.debug, logGroup);
		}

		function addEventListener(eventName, callback) {
			log(['addEventListener to AdManager', eventName], log.levels.debug, logGroup);

			if (isAdsManagerLoaded) {
				adsManager.addEventListener(eventName, callback);
			} else {
				adsLoader.addEventListener('adsManagerLoaded', function () {
					adsManager.addEventListener(eventName, callback);
				});
			}
		}

		function enableAutoplay() {
			var videoAd = params.container.querySelector('video');

			// videoAd DOM element is present on mobile only
			if (videoAd) {
				videoAd.autoplay = true;
				videoAd.muted = true;
			}
		}

		function playVideo(width, height) {
			function callback() {
				log('Video play: prepare player UI', log.levels.debug, logGroup);

				// https://developers.google.com/interactive-media-ads/docs/sdks/html5/v3/apis#ima.AdDisplayContainer.initialize
				adDisplayContainer.initialize();
				adsManager.init(width, height, google.ima.ViewMode.NORMAL);
				adsManager.start();
				adsLoader.removeEventListener('adsManagerLoaded', callback);

				log('Video play: started', log.levels.debug, logGroup);
			}

			if (params.autoplay) {
				enableAutoplay();
			}

			if (isAdsManagerLoaded) {
				callback();
			} else {
				// When adsManager is not loaded yet video can't start without click on mobile
				// Muted auto play is workaround to run video on adsManagerLoaded event
				enableAutoplay();
				adsLoader.addEventListener('adsManagerLoaded', callback, false);
				log(['Video play: waiting for full load of adsManager'], log.levels.debug, logGroup);
			}
		}

		function reload() {
			adsManager.destroy();
			adsLoader.contentComplete();
			adsLoader.requestAds(imaSetup.createRequest(params));

			log('IMA player reloaded', log.levels.debug, logGroup);
		}

		function resize(width, height) {
			if (adsManager) {
				adsManager.resize(width, height, google.ima.ViewMode.NORMAL);

				log(['IMA player resized', width, height], log.levels.debug, logGroup);
			}
		}

		function dispatchEvent(eventName) {
			adsManager.dispatchEvent(eventName);
		}

		function setStatus(newStatus) {
			return function () {
				status = newStatus;
			};
		}

		function getStatus() {
			return status;
		}

		function getAdsManager() {
			return adsManager;
		}

		adsLoader.addEventListener('adsManagerLoaded', adsManagerLoadedCallback, false);
		adsLoader.requestAds(imaSetup.createRequest(params));

		addEventListener('resume', setStatus('playing'));
		addEventListener('start', setStatus('playing'));
		addEventListener('pause', setStatus('paused'));

		return {
			addEventListener: addEventListener,
			dispatchEvent: dispatchEvent,
			getAdsManager: getAdsManager,
			getStatus: getStatus,
			playVideo: playVideo,
			reload: reload,
			resize: resize
		};
	}

	return {
		create: create
	};
});
