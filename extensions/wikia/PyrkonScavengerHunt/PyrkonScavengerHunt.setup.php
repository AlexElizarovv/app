<?php
/**
 * Pyrkon Scavenger Hunt for Fandom
 *
 * @author bart <bart(at)wikia-inc.com>
 */
$dir = __DIR__ . '/';

/**
 * Classes
 */
$wgAutoloadClasses['PyrkonScavengerHuntHooks'] = $dir . 'PyrkonScavengerHuntHooks.class.php';

/**
 * Hooks
 */
$wgHooks['BeforePageDisplay'][] = 'PyrkonScavengerHuntHooks::onBeforePageDisplay';

$wgExtensionCredits['other'][] = [
	'name' => 'Pyrkon Scavenger Hunt',
	'version' => '1.0',
	'author' => 'Bartłomiej Bart Kowalczyk',
	'url' => 'https://github.com/Wikia/app/tree/dev/extensions/wikia/PyrkonScavengerHunt'
];
