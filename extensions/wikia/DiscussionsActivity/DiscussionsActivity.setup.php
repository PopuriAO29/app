<?php
define( 'RC_DISCUSSIONS', 5 );

$GLOBALS['wgAutoloadClasses']['DiscussionsActivityController'] = __DIR__ . '/DiscussionsActivityController.php';
$GLOBALS['wgAutoloadClasses']['DiscussionsActivityEvent'] = __DIR__ . '/DiscussionsActivityEvent.php';
$GLOBALS['wgAutoloadClasses']['DiscussionsActivityFormatter'] = __DIR__ . '/DiscussionsActivityFormatter.php';
$GLOBALS['wgAutoloadClasses']['DiscussionsActivityHooks'] = __DIR__ . '/DiscussionsActivityHooks.php';
$GLOBALS['wgAutoloadClasses']['DiscussionsAwareChangesList'] = __DIR__ . '/DiscussionsAwareChangesList.php';
$GLOBALS['wgAutoloadClasses']['DiscussionsAwareEnhancedChangesList'] = __DIR__ . '/DiscussionsAwareEnhancedChangesList.php';

$GLOBALS['wgHooks']['FetchChangesList'][] = 'DiscussionsActivityHooks::onFetchChangesList';
