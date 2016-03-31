/*global prettyPrint:true, ga:true */
$(function () {
	'use strict';

	var loadSocialPlugin = function (id, url) {
		var d = document,
			js,
			fjs = d.getElementsByTagName('script')[0];

        if (d.getElementById(id)) {
			return;
        }

        js = d.createElement('script');
        js.id = id;
        js.src = url;
        js.defer = true;
        js.async = true;
        fjs.parentNode.insertBefore(js, fjs);
	},
	customMap;

	// make code pretty
	prettyPrint();

	setTimeout(function () {
		loadSocialPlugin('facebook-jssdk', '//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=1443322352613478&version=v2.0');
		loadSocialPlugin('twitter-wjs', '//platform.twitter.com/widgets.js');
		loadSocialPlugin('google-plus-js', '//apis.google.com/js/platform.js');

		$('#default_map').tweetMap({
			q: '#newyork',
			latitude: '40.714353',
			longitude: '-74.005973',
			geocode: '40.714353, -74.005973, 1mi',
			zoom: 14
		});

		customMap = $('#custom_map').tweetMap({
			q: '#sanfran',
			latitude: '37.77493',
			longitude: '-122.419416',
			zoom: 10,
			geocode: '37.77493, -122.419416, 3mi',
			maptype: 'TERRAIN'
		});
	}, 200);

	$('#reloadFeed').on('click', function (e) {
		e.preventDefault();
		if (customMap) {
			customMap.tweetMap('refresh');
		}
	});

	$(document).on('click', 'a', function () {
		if (ga) {
			ga('send', 'pageview', this.href);
		}
	});

});