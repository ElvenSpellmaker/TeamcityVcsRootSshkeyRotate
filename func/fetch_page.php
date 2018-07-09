<?php

/**
 * Fetches a page, optionally treating the page as a JSON request.
 *
 * @param string $url
 * @param array  $opts The options array.
 * @param bool   $json Treat the request as a JSON request and return a decoded
 *                     array.
 *
 * @return array|string Returns the request, optionally decoded as an array.
 */
function fetch_page(string $url, array $opts, bool $json = true)
{
	($json === true) && $opts['http']['header'] .= "Accept: application/json\n";

	$ctx = stream_context_create($opts);
	$page = file_get_contents($url, null, $ctx);

	($json === true) && $page = json_decode($page, true);

	return $page;
}
