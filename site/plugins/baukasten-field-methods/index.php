<?php

use Kirby\Toolkit\Str;
use Kirby\Content\Field;
use Kirby\Cms\App;

App::plugin("baukasten/field-methods", [
	"fieldMethods" => [
		"getLinkArray" => function ($field) {
			return getLinkArray($field);
		},
	],
]);

function getLinkArray($field): ?array
{
	if ($field->isEmpty()) {
		return null;
	}

	$link = $field->toObject();
	if (!$link) {
		return null;
	}

	$linkValue = stripPrefix($link->link()->value(), 'tel:');
	$linkType = getLinkType($link->link());

	$titlePage = $link->link()->toPage()?->title()->value();

	$uri = determineUri($linkType, $link->link());

	$anchorToggle = $link->anchorToggle()->toBool();
	$anchor = stripPrefix($link->anchor(), '#');

	return [
		'href' => in_array($linkType, ['url', 'tel', 'email']) ? $linkValue : null,
		'title' => getTitle($linkType, $link, $linkValue, $titlePage),
		'popup' => $link->target()->toBool(),
		'hash' => $anchorToggle ? $anchor : null,
		'type' => $linkType,
		'uri' => $uri,
		'classes' => $link->classnames()->value(),
	];
}

function getLinkType(Field $field): string
{
	$val = $field->value();
	if (empty($val)) return 'custom';

	if (Str::match($val, '/^(http|https):\/\//')) {
		return 'url';
	}

	if (Str::startsWith($val, 'page://') || Str::startsWith($val, '/@/page/')) {
		return 'page';
	}

	if (Str::startsWith($val, 'file://') || Str::startsWith($val, '/@/file/')) {
		return 'file';
	}

	if (Str::startsWith($val, 'tel:')) {
		return 'tel';
	}

	if (Str::startsWith($val, 'mailto:')) {
		return 'email';
	}

	if (Str::startsWith($val, '#')) {
		return 'anchor';
	}

	return 'custom';
}

function determineUri($linkType, $linkField)
{
	$uri = null;

	switch ($linkType) {
		case 'page':
			$page = $linkField->toPage();
			if ($page) {
				// Use generatePageUri helper function to respect designSectionToggle setting
				$uri = function_exists('generatePageUri') ? generatePageUri($page) : $page->uri();
			}
			break;
		case 'file':
			$uri = $linkField->toUrl();
			break;
	}

	// Convert 'home' to empty string for backward compatibility
	if ($uri === 'home') {
		$uri = '';
	}

	return $uri;
}

function stripPrefix($string, $prefix)
{
	if (is_null($string) || is_null($prefix)) {
		return $string;
	}

	return preg_replace('/^(' . preg_quote($prefix, '/') . ')/', '', $string);
}

function getTitle($linkType, $link, $linkValue, $titlePage)
{
	$linkText = $link->linkText()->value();

	switch ($linkType) {
		case 'url':
			return $linkText ?: $linkValue;
		case 'page':
			return $linkText ?: $titlePage;
		default:
			return $linkText ?: $titlePage;
	}
}
