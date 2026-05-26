<?php

/**
 * Dev-only router for `php -S`.
 *
 * The built-in PHP server ignores .htaccess, so the CORS header we set there
 * for 3D models never applies in local dev — and Kirby's own router.php just
 * `return false`s for existing media files, letting PHP serve them statically
 * with no CORS header. That breaks Three.js GLTFLoader (crossOrigin) when the
 * front runs on a different port.
 *
 * This wrapper serves .glb/.gltf with the right Content-Type + CORS header,
 * and delegates everything else to the same logic as kirby/router.php.
 *
 * Launch with:  php -S localhost:8000 dev-router.php
 * (Production stays on Apache + .htaccess; this file is only for local dev.)
 */

$uri = parse_url('https://getkirby.com/' . ltrim($_SERVER['REQUEST_URI'], '/'), PHP_URL_PATH) ?? '/';
$uri = urldecode($uri);

$path = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($uri, '/');

$isFile =
	$uri !== '/' &&
	file_exists($path) === true &&
	substr(realpath($path), 0, strlen($_SERVER['DOCUMENT_ROOT'])) === $_SERVER['DOCUMENT_ROOT'];

// Serve 3D models ourselves with CORS so the front-end (different origin in
// dev) can load them via fetch/GLTFLoader.
if ($isFile === true && preg_match('/\.(glb|gltf)$/i', $path) === 1) {
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: ' . (preg_match('/\.glb$/i', $path) === 1 ? 'model/gltf-binary' : 'model/gltf+json'));
	header('Content-Length: ' . filesize($path));
	readfile($path);
	return true;
}

// Any other existing static file → let the built-in server handle it.
if ($isFile === true) {
	return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
require $_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME'];
