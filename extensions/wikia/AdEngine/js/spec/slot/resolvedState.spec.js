/*global describe, it, expect, modules*/
describe('ext.wikia.adEngine.slot.resolvedState', function () {
	'use strict';

	var BIG_IMAGE = 'bigImage.png',
		BIG_IMAGE_2 = 'bigImage2.png',
		DEFAULT_IMAGE = 'oldImage.png',
		RESOLVED_IMAGE = 'resolvedImage.png',
		RESOLVED_IMAGE_2 = 'resolvedImage2.png',
		mocks = {
			log: function () {},
			qs: {
				getVal: function () {}
			},
			QueryString: function () {
				return mocks.qs;
			},
			uapContext: {
				getUapId: function () {
					return 12345;
				}
			},
			cache: {
				get: function() {
					return [];
				},
				set: function () {}
			},
			win: {}
		},
		data = {
			PARAMS: {
				CORRECT: {
					imageSrc: BIG_IMAGE,
					aspectRatio: 1,
					resolvedState: {
						aspectRatio: 2,
						imageSrc: RESOLVED_IMAGE
					},
					backgroundImage: {
						src: DEFAULT_IMAGE
					}
				},
				CORRECT_WITH_TWO_ASSETS: {
					aspectRatio: 1,
					resolvedStateAspectRatio: 2,
					image1: {
						element: {
							src: DEFAULT_IMAGE
						},
						defaultStateSrc: BIG_IMAGE,
						resolvedStateSrc: RESOLVED_IMAGE
					},
					image2: {
						element: {
							src: DEFAULT_IMAGE
						},
						defaultStateSrc: BIG_IMAGE_2,
						resolvedStateSrc: RESOLVED_IMAGE_2
					}
				},
				INCORRECT: {
					aspectRatio: 1,
					imageSrc: BIG_IMAGE,
					resolvedState: {
						aspectRatio: 0,
						imageSrc: ''
					},
					backgroundImage: {
						src: DEFAULT_IMAGE
					}
				},
				EMPTY: {}
			}
		},
		testCases = [
			{
				params: data.PARAMS.CORRECT,
				queryParam: null,
				expected: RESOLVED_IMAGE
			},
			{
				params: data.PARAMS.CORRECT,
				queryParam: 'blocked',
				expected: BIG_IMAGE
			},
			{
				params: data.PARAMS.CORRECT,
				queryParam: 'true',
				expected: RESOLVED_IMAGE
			},
			{
				params: data.PARAMS.CORRECT,
				queryParam: 'a',
				expected: RESOLVED_IMAGE
			},
			{
				params: data.PARAMS.CORRECT,
				queryParam: '0',
				expected: BIG_IMAGE
			},
			{
				params: data.PARAMS.INCORRECT,
				queryParam: 'true',
				expected: BIG_IMAGE
			},
			{
				params: data.PARAMS.INCORRECT,
				queryParam: 'blocked',
				expected: BIG_IMAGE
			},
			{
				params: data.PARAMS.INCORRECT,
				queryParam: '1',
				expected: BIG_IMAGE
			}
		],
		testCasesWithTwoAssets = [
			{
				params: data.PARAMS.CORRECT_WITH_TWO_ASSETS,
				queryParam: '0',
				expectedImage1: BIG_IMAGE,
				expectedImage2: BIG_IMAGE_2
			},
			{
				params: data.PARAMS.CORRECT_WITH_TWO_ASSETS,
				queryParam: null,
				expectedImage1: RESOLVED_IMAGE,
				expectedImage2: RESOLVED_IMAGE_2
			}
		];

	mocks.log.levels = {debug: ''};

	function getModule() {
		return modules['ext.wikia.adEngine.slot.resolvedState'](
			mocks.uapContext,
			mocks.cache,
			mocks.log,
			mocks.QueryString,
			mocks.win
		);
	}

	testCases.forEach(function (testCase) {
		var testName = 'Should return ' + testCase.expected + ' when params: ' + JSON.stringify(testCase.params) +
			' and resolvedState query param equals: ' + testCase.queryParam;

		it(testName, function () {
			spyOn(mocks.qs, 'getVal');
			var rs = getModule();
			mocks.qs.getVal.and.returnValue(testCase.queryParam);

			expect(testCase.expected).toEqual(rs.setImage(testCase.params).backgroundImage.src);
		});
	});

	testCasesWithTwoAssets.forEach(function (testCase) {
		var testName = 'Should return ' + testCase.expectedImage1 + ' and ' + testCase.expectedImage2 +
			' when params: ' + JSON.stringify(testCase.params) + ' and resolvedState query param equals: ' +
			testCase.queryParam;

		it(testName, function () {
			spyOn(mocks.qs, 'getVal');
			var rs = getModule();
			mocks.qs.getVal.and.returnValue(testCase.queryParam);

			expect(testCase.expectedImage1).toEqual(rs.setImage(testCase.params).image1.element.src);
			expect(testCase.expectedImage2).toEqual(rs.setImage(testCase.params).image2.element.src);
		});
	});

	it('Should update params aspect ratio', function () {
		var rs = getModule();

		expect(rs.setImage(data.PARAMS.CORRECT).aspectRatio).toEqual(data.PARAMS.CORRECT.resolvedState.aspectRatio);
	});

	it('Should update image src', function () {
		var params = data.PARAMS.CORRECT,
			rs = getModule();

		expect(rs.setImage(params).backgroundImage.src).toEqual(params.resolvedState.imageSrc);
	});

	it('Should not update image if there is no background image (template without backgroundImage)', function () {
		var params = {},
			rs = getModule();

		expect(rs.setImage(params)).toEqual(params);
	});

});