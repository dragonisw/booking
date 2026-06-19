<?php
// Set the language variable
$_GET['lang'] = 'en';

// Strip /en from the request so WordPress can route properly
$_SERVER['REQUEST_URI'] = preg_replace('#^/en/?#i', '/', $_SERVER['REQUEST_URI']);

// Fix PHP script names to avoid 404s
$_SERVER['SCRIPT_NAME'] = preg_replace('#/en/index\.php$#i', '/index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['PHP_SELF'] = preg_replace('#/en/index\.php$#i', '/index.php', $_SERVER['PHP_SELF']);

// Load WordPress
require dirname(__FILE__) . '/../index.php';
