<?php
$uris = ['/', '/booking', '/en/booking', '/en/', '/en?foo=bar', '/booking?foo=bar'];

foreach ($uris as $uri) {
    $uri_no_en = preg_replace('#^/en(/|\\?|$)#i', '', $uri);
    $uri_vi = $uri_no_en ?: '/';
    
    // For EN:
    if ($uri_vi === '/' || $uri_vi === '') {
        $uri_en = '/en/';
    } else {
        $uri_en = '/en' . (strpos($uri_vi, '/') === 0 ? $uri_vi : '/' . $uri_vi);
    }
    
    echo "$uri -> VI: $uri_vi | EN: $uri_en\n";
}
