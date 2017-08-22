/*global define*/
define('ext.wikia.adEngine.provider.gpt.adSizeFilter', [
	'ext.wikia.adEngine.adContext',
	'wikia.abTest',
	'wikia.document',
	'wikia.log',
	require.optional('wikia.breakpointsLayout')
], function (adContext, abTest, doc, log, breakpointsLayout) {
	'use strict';

	var logGroup = 'ext.wikia.adEngine.provider.gpt.adSizeFilter',
		context = adContext.getContext(),
		maxAdSize = 704,
		minSkinWidth = 1240;

	function isLargeBreakpoints() {
		return breakpointsLayout &&
			doc.getElementById('WikiaPageBackground').offsetWidth >= breakpointsLayout.getLargeContentWidth();
	}

	function getNewSizes(sizes, maxWidth, fallbackSizes) {
		var goodSizes = [];

		for (var i = 0; i < sizes.length; i += 1) {
			if (sizes[i][0] <= maxWidth) {
				goodSizes.push(sizes[i]);
			}
		}

		log(['getNewSizes', 'sizes: ', sizes, 'goodSizes: ', goodSizes], 'debug', logGroup);

		return goodSizes.length ? goodSizes : fallbackSizes;
	}

	function filterSizes(slotName, slotSizes) {
		var isPremiumLayout = context.opts.premiumAdLayoutEnabled,
			footerSize;
		log(['filterSizes', slotName, slotSizes], 'debug', logGroup);

		switch (true) {
			case slotName.indexOf('TOP_LEADERBOARD') > -1:
				return getNewSizes(slotSizes, doc.documentElement.offsetWidth, [[728, 90]]);
			case slotName === 'INVISIBLE_SKIN':
				return doc.documentElement.offsetWidth >= minSkinWidth ? slotSizes : [[1, 1]];
			case slotName === 'PREFOOTER_LEFT_BOXAD' && context.opts.overridePrefootersSizes:
				return isLargeBreakpoints() ? slotSizes : getNewSizes(slotSizes, maxAdSize, [[300, 250]]);
			case slotName === 'BOTTOM_LEADERBOARD' && isPremiumLayout:
				footerSize = doc.getElementById('WikiaFooter').offsetWidth;
				return getNewSizes([[970, 250], [728, 90]], footerSize, [[728, 90]]);
			case slotName === 'BOTTOM_LEADERBOARD':
				footerSize = doc.getElementById('WikiaFooter').offsetWidth;
				return getNewSizes(slotSizes, footerSize, [[728, 90]]);
			case slotName === 'INCONTENT_BOXAD_1' && isPremiumLayout && context.targeting.hasFeaturedVideo:
				return [ [300, 250] ];
			default:
				return slotSizes;
		}
	}

	return {
		filter: filterSizes
	};
});
