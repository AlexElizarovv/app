<?php
$wgExtensionCredits['other'][] = [
	'name' => 'CategoryPage3 Extension',
	'author' => [
		'Igor Rogatty',
	],
	'description' => 'SEO friendly category pages',
	'url' => 'https://github.com/Wikia/app/tree/dev/extensions/wikia/CategoryPage3Hooks'
];

$wgAutoloadClasses['CategoryPage3'] = __DIR__ . '/CategoryPage3.class.php';
$wgAutoloadClasses['CategoryPage3CacheHelper'] = __DIR__ . '/CategoryPage3CacheHelper.class.php';
$wgAutoloadClasses['CategoryPage3Hooks'] = __DIR__ . '/CategoryPage3Hooks.class.php';
$wgAutoloadClasses['CategoryPage3Member'] = __DIR__ . '/CategoryPage3Member.class.php';
$wgAutoloadClasses['CategoryPage3Model'] = __DIR__ . '/CategoryPage3Model.class.php';
$wgAutoloadClasses['CategoryPage3TrendingPages'] = __DIR__ . '/CategoryPage3TrendingPages.class.php';
$wgAutoloadClasses['CategoryPage3Pagination'] = __DIR__ . '/CategoryPage3Pagination.class.php';

$wgHooks['AfterCategoriesUpdate'][] = 'CategoryPage3Hooks::onAfterCategoriesUpdate';
$wgHooks['ArticleFromTitle'][] = 'CategoryPage3Hooks::onArticleFromTitle';

$wgResourceModules['ext.wikia.CategoryPage3.scripts'] = [
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'wikia/CategoryPage3',
	'scripts' => 'scripts/category-page3.js',
];
