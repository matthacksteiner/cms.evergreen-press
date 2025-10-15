<?php

/**
 * Global helper functions that delegate to the appropriate helper classes
 * This maintains backward compatibility with the existing function calls
 */

use BaukastenApi\Helpers\NavigationHelper;
use BaukastenApi\Helpers\UrlHelper;
use BaukastenApi\Helpers\LanguageHelper;
use BaukastenApi\Helpers\SiteDataHelper;
use BaukastenApi\Services\FlatUrlResolver;

// Navigation helper functions
if (!function_exists('getPageNavigation')) {
    function getPageNavigation($page)
    {
        return NavigationHelper::getPageNavigation($page);
    }
}

if (!function_exists('getNavigationSiblings')) {
    function getNavigationSiblings($page, $effectiveParent)
    {
        return NavigationHelper::getNavigationSiblings($page, $effectiveParent);
    }
}

if (!function_exists('getEffectiveParent')) {
    function getEffectiveParent($page)
    {
        return NavigationHelper::getEffectiveParent($page);
    }
}

// URL helper functions
if (!function_exists('getSectionToggleState')) {
    function getSectionToggleState(): bool
    {
        return UrlHelper::getSectionToggleState();
    }
}

if (!function_exists('generatePageUri')) {
    function generatePageUri($page, bool $respectSectionToggle = true): string
    {
        return UrlHelper::generatePageUri($page, $respectSectionToggle);
    }
}

// Language helper functions
if (!function_exists('getTranslations')) {
    function getTranslations($kirby)
    {
        return LanguageHelper::getTranslations($kirby);
    }
}

if (!function_exists('getAllLanguages')) {
    function getAllLanguages($kirby)
    {
        return LanguageHelper::getAllLanguages($kirby);
    }
}

if (!function_exists('getDefaultLanguage')) {
    function getDefaultLanguage($kirby)
    {
        return LanguageHelper::getDefaultLanguage($kirby);
    }
}

// Site data helper functions
if (!function_exists('getFavicon')) {
    function getFavicon($site)
    {
        return SiteDataHelper::getFavicon($site);
    }
}

if (!function_exists('getNavigation')) {
    function getNavigation($site, $field)
    {
        return SiteDataHelper::getNavigation($site, $field);
    }
}

if (!function_exists('getLogoFile')) {
    function getLogoFile($site)
    {
        return SiteDataHelper::getLogoFile($site);
    }
}

if (!function_exists('getLogoCta')) {
    function getLogoCta($site)
    {
        return SiteDataHelper::getLogoCta($site);
    }
}

if (!function_exists('getFonts')) {
    function getFonts($site)
    {
        return SiteDataHelper::getFonts($site);
    }
}

if (!function_exists('getFontSizes')) {
    function getFontSizes($site)
    {
        return SiteDataHelper::getFontSizes($site);
    }
}

if (!function_exists('getHeadlines')) {
    function getHeadlines($site)
    {
        return SiteDataHelper::getHeadlines($site);
    }
}

if (!function_exists('getAnalytics')) {
    function getAnalytics($site)
    {
        return SiteDataHelper::getAnalytics($site);
    }
}

// Flat URL resolver functions
if (!function_exists('handleFlatUrlResolution')) {
    function handleFlatUrlResolution($path)
    {
        return FlatUrlResolver::handleFlatUrlResolution($path);
    }
}

if (!function_exists('findPagesByFlatUrl')) {
    function findPagesByFlatUrl($path, $segments)
    {
        return FlatUrlResolver::findPagesByFlatUrl($path, $segments);
    }
}

if (!function_exists('resolvePriorityConflict')) {
    function resolvePriorityConflict($candidates, $path)
    {
        return FlatUrlResolver::resolvePriorityConflict($candidates, $path);
    }
}

if (!function_exists('tryHierarchicalFallback')) {
    function tryHierarchicalFallback($path, $segments)
    {
        return FlatUrlResolver::tryHierarchicalFallback($path, $segments);
    }
}

if (!function_exists('isSectionPageAccessible')) {
    function isSectionPageAccessible($page)
    {
        return FlatUrlResolver::isSectionPageAccessible($page);
    }
}
