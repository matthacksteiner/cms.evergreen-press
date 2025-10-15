<?php

/** @var Kirby\Cms\Page $page */
/** @var Array $json */

use Kirby\Cms\Page;

// Set proper JSON headers
header('Content-Type: application/json; charset=utf-8');

function getItems(\Kirby\Cms\Page $page)
{
  $items = [];

  foreach ($page->children() as $item) {
    $image = $item->thumbnail()->toFile();

    $ratioMobile = explode('/', $page->displayratio()->toObject()->ratioMobile()->value() ?: '16/9');
    $ratio = explode('/', $page->displayratio()->toObject()->ratio()->value() ?: '16/9');

    $calculateHeight = function ($width, $ratio) {
      return isset($ratio[1]) ? round(($width / $ratio[0]) * $ratio[1]) : $width;
    };

    $thumbnail = [];
    if ($image) {
      $thumbnail = [
        'url' => $image->url(),
        'urlFocus' => $image->crop($image->width(), $calculateHeight($image->width(), $ratio))->url(),
        'urlFocusMobile' => $image->crop($image->width(), $calculateHeight($image->width(), $ratioMobile))->url(),
        'width' => $image->width(),
        'height' => $image->height(),
        'alt' => (string) $image->alt(),
        'name' => (string)$image->name(),
        'thumbhash' => $image->thumbhashUri(),
        'orientation' => $image->orientation(),
      ];
    }

    $items[] = [
      'title' => (string) $item->title(),
      "uri" => $item->uri(),
      "description" => (string) $item->description()->escape(),
      "parent" => strtolower((string) $page->title()),
      'thumbnail' => $thumbnail,
      'coverOnly' => $item->coverOnly()->toBool(false),
      'status' => $item->status(),
      'position' => $item->num(),
    ];
  }

  return $items;
}

if ($page->children()->isNotEmpty()) {
  $json['items'] = getItems($page);
}

function getSettings(\Kirby\Cms\Page $page)
{
  // Get site object for design fallbacks
  $site = site();

  // Helper function to get value with fallback to design settings
  $getValueWithFallback = function ($value, $fallbackSiteField) use ($site) {
    if ($value === 'default') {
      return $site->{$fallbackSiteField}()->value() ?: '16';
    }
    return ($value !== null && $value !== '') ? $value : '16';
  };

  // Get the actual values from page fields
  $gap = $page->displayGrid()->toObject()->gap()->value();
  $gapMobile = $page->displayGrid()->toObject()->gapMobile()->value();
  $gapHorizontal = $page->displayGrid()->toObject()->gapHorizontal()->value();
  $gapHorizontalMobile = $page->displayGrid()->toObject()->gapHorizontalMobile()->value();

  return [
    'ratio' => $page->displayRatio()->toObject()->ratio()->value() ?: '16/9',
    'ratioMobile' => $page->displayRatio()->toObject()->ratioMobile()->value() ?: '16/9',
    'grid' => [
      'elements' => $page->displayElements()->toObject()->elements()->value() ?: '10',
      'gap' => $getValueWithFallback($gap, 'gridBlockDesktop'),
      'gapMobile' => $getValueWithFallback($gapMobile, 'gridBlockMobile'),
      'gapHorizontal' => $getValueWithFallback($gapHorizontal, 'gridGapDesktop'),
      'gapHorizontalMobile' => $getValueWithFallback($gapHorizontalMobile, 'gridGapMobile'),
      'span' => $page->displayGrid()->toObject()->span()->value() ?: '6',
      'spanMobile' => $page->displayGrid()->toObject()->spanMobile()->value() ?: '6',
    ],
    'title' => [
      'toggle' => $page->fontTitleToggle()->toBool(true),
      'level' => $page->fontTitle()->toObject()->level()->value() ?: 'h2',
      'font' => $page->fontTitle()->toObject()->titleFont()->value(),
      'size' => $page->fontTitle()->toObject()->titleSize()->value(),
      'color' => $page->fontTitle()->toObject()->titleColor()->value(),
      'align' => $page->fontTitle()->toObject()->titleAlign()->value(),
    ],
    'text' => [
      'toggle' => $page->fontTextToggle()->toBool(true),
      'font' => $page->fontText()->toObject()->textFont()->value(),
      'size' => $page->fontText()->toObject()->textSize()->value(),
      'color' => $page->fontText()->toObject()->textColor()->value(),
      'align' => $page->fontText()->toObject()->textAlign()->value(),
    ],
    'caption' => [
      'align' => $page->captionAlign()->value() ?: 'bottom',
      'controls' => $page->captionControls()->split(','),
      'overlayRange' => $page->captionOverlayRange()->toInt() ?: 50,
      'color' => $page->captionColor()->value() ?: '#000000',
    ]
  ];
}

$json['settings'] = getSettings($page);

if ($page->layoutPre()->isNotEmpty()) {
  $json["layoutPre"] = [];

  foreach ($page->layoutPre()->toLayouts() as $layout) {
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

    $json["layoutPre"][] = $layoutData;
  }
}

if ($page->layoutPost()->isNotEmpty()) {
  $json["layoutPost"] = [];

  foreach ($page->layoutPost()->toLayouts() as $layout) {
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

    $json["layoutPost"][] = $layoutData;
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
