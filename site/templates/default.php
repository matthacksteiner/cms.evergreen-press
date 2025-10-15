<?php

$path = $kirby->request()->path();
$targetUrl = $site->frontendUrl();
$targetUrl .= '/' . $path;

$fallback = 'https://baukasten.netlify.app';

if (!filter_var($targetUrl, FILTER_VALIDATE_URL)) {
  go($fallback);
} else {
  go($targetUrl);
}
