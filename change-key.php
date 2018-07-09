<?php

require 'func/fetch_page.php';
$config = require 'config.local.php';
const TEAMCITY_SSH_KEY_NAME = 'teamcitySshKey';

if (
	! is_array($config)
	|| ! isset(
		$config['oldKeyName'],
		$config['newKeyName'],
		$config['teamcityBaseUrl'],
		$config['cookie']
	)
)
{
	throw new RuntimeException('Please supply needed parameters! (Check the Readme!)');
}

$teamcitybaseUrl = $config['teamcityBaseUrl'];
$oldKeyName = $config['oldKeyName'];
$newKeyName = $config['newKeyName'];
$cookieString = "Cookie: {$config['cookie']}\n";

$opts = [
	'http' => [
		'header' =>
			$cookieString,
	],
];

$putOpts = [
	'http' => [
		'method' => 'PUT',
		'header' =>
			$cookieString .
			"Content-Type: text/plain\n" .
			"Origin: $teamcitybaseUrl\n",
	],
];

$baseVcsRootsUrl = $teamcitybaseUrl . '/app/rest/vcs-roots';
$nextVcsRootsUrl = $baseVcsRootsUrl;
$page = 1;
do
{
	$json = fetch_page($nextVcsRootsUrl, $opts);

	echo 'Page: ', $page++, ', Count: ' . $json['count'], "\n";

	foreach ($json['vcs-root'] as $root)
	{
		// Gather the VCS Root properties.
		$rootInfo = fetch_page(
			"${baseVcsRootsUrl}/{$root['id']}/properties",
			$opts
		);

		// Extract the properties into an array.
		$rootInfo = array_column($rootInfo['property'], 'value', 'name');

		// If we have an SSH Key defined (some roots are password maybe), then
		// check if the Key matches the key to replace, or an empty string
		// then replace with the new key.
		if (
			isset($rootInfo[TEAMCITY_SSH_KEY_NAME])
			&& $rootInfo[TEAMCITY_SSH_KEY_NAME] === $oldKeyName
		)
		{
			$putOpts['http']['content'] = $newKeyName;
			$newTcKeyName = fetch_page(
				"${baseVcsRootsUrl}/{$root['id']}/properties/" . TEAMCITY_SSH_KEY_NAME,
				$putOpts,
				false
			);

			$resultString = ($newTcKeyName === $newKeyName)
				? 'Success'
				: 'Failure';

			echo $resultString,
				', ',
				$root['id'],
				': ',
				$oldKeyName ,
				' --> ',
				$newKeyName,
				"\n";
		}
	}

	// If there's no next URL then we've finished.
	if (! isset($json['nextHref']))
	{
		break;
	}

	$nextVcsRootsUrl = $teamcitybaseUrl . $json['nextHref'];
}
while ($json['count'] > 0);
