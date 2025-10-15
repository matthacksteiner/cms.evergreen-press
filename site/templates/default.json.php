<?php

/** @var Page $page */
/** @var Array $json */

use Kirby\Cms\Page;

// Set proper JSON headers
header('Content-Type: application/json; charset=utf-8');

if ($page->layout()->isNotEmpty()) {
  $json["layouts"] = [];

  foreach ($page->layout()->toLayouts() as $layout) {
    $layoutData = getLayoutArray($layout);

    if (!$layoutData) {
      continue;
    }

    if (isset($layoutData['attributes'])) {
      $newAttributes = [];
      foreach ($layoutData['attributes'] as $attribute) {
        $value = $attribute['value'];
        if ($value === "true") {
          $value = true;
        }
        $newAttributes[$attribute['attribute']] = $value;
      }
      $layoutData['attributes'] = $newAttributes;
    }

    $json["layouts"][] = $layoutData;
  }
}

if ($site->layoutFooter()->isNotEmpty()) {
  $json["layoutFooter"] = [];

  foreach ($site->layoutFooter()->toLayouts() as $layout) {
    $layoutData = getLayoutArray($layout);

    if (!$layoutData) {
      continue;
    }

    $json["layoutFooter"][] = $layoutData;
  }
}

if ($page->baukastenbuilder()->isNotEmpty()) {
  $json["blocks"] = [];

  foreach ($page->baukastenbuilder()->toBlocks() as $block) {
    $blockData = getBlockArray($block);

    if (!$blockData) {
      continue;
    }

    $json["blocks"][] = $blockData;
  }
}

// Add navigation data for section items
$json["navigation"] = getPageNavigation($page);

if (method_exists($page, 'getJsonData')) {
  $content = $page->content()->toArray();
  $unsetFields = [
    'title',
    'meta_title',
    'meta_description',
    'meta_canonical_url',
    'meta_author',
    'meta_image',
    'meta_phone_number',
    'og_title',
    'og_description',
    'og_image',
    'og_site_name',
    'og_url',
    'og_audio',
    'og_video',
    'og_determiner',
    'og_type',
    'og_type_article_published_time',
    'og_type_article_modified_time',
    'og_type_article_expiration_time',
    'og_type_article_author',
    'og_type_article_section',
    'og_type_article_tag',
    'twitter_title',
    'twitter_description',
    'twitter_image',
    'twitter_card_type',
    'twitter_site',
    'twitter_creator',
    'robots_noindex',
    'robots_nofollow',
    'robots_noarchive',
    'robots_noimageindex',
    'robots_nosnippet',
    'blocks'
  ];

  foreach ($unsetFields as $key) {
    unset($content[$key]);
  }
}

echo json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
