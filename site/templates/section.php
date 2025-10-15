<?php

// Check if section pages should be accessible based on toggle setting
if (!isSectionPageAccessible($page)) {
  // Return 404 when section toggle is disabled
  $kirby->response()->code(404);
  return;
}

$path = $kirby->request()->path();
$targetUrl = $site->frontendUrl();
$targetUrl .= '/' . $path;

$fallback = 'https://baukasten.netlify.app';

if (!filter_var($targetUrl, FILTER_VALIDATE_URL)) {
  go($fallback);
} else {
  go($targetUrl);
}
