<?php

use Kirby\Cms\App;
use Kirby\Cms\App as Kirby;
use Kirby\Cms\Page;

// Load plugin classes manually
require_once __DIR__ . '/src/Helper.php';
require_once __DIR__ . '/src/PageMeta.php';
require_once __DIR__ . '/src/SiteMeta.php';
require_once __DIR__ . '/src/Sitemap.php';
require_once __DIR__ . '/src/SitemapPage.php';

App::plugin('baukastenMeta/meta', [
    'options' => [
        'cache' => true,
        'schema' => true,
        'social' => true,
        'twitter' => false,
        'robots' => true,
        'robots.canonical' => true,
        'robots.index' => true,
        'robots.follow' => true,
        'robots.archive' => true,
        'robots.imageindex' => true,
        'robots.snippet' => true,
        'robots.translate' => true,
        'title.separators' => ['~', '-', '–', '—', ':', '/', '⋆', '·', '•', '~', '×', '*', '‣', '→', '←', '<', '>', '«', '»', '‹', '›', '♠︎', '♣︎', '♥︎', '♦︎', '☙', '❦', '❧', '☭'],
    ],

    'blueprints' => [
        'fields/meta/general-group'             => require __DIR__ . '/blueprints/fields/meta/general-group.php',
        'fields/meta/global-general-group'      => require __DIR__ . '/blueprints/fields/meta/global-general-group.php',
        'fields/meta/global-opengraph-group'    => __DIR__ . '/blueprints/fields/meta/global-opengraph-group.yml',
        'fields/meta/global-robots-group'       => require __DIR__ . '/blueprints/fields/meta/global-robots-group.php',
        'fields/meta/global-schema-group'       => __DIR__ . '/blueprints/fields/meta/global-schema-group.yml',
        'fields/meta/og-image'                  => __DIR__ . '/blueprints/fields/meta/og-image.yml',
        'fields/meta/opengraph-group'           => __DIR__ . '/blueprints/fields/meta/opengraph-group.yml',
        'fields/meta/robots-group'              => require __DIR__ . '/blueprints/fields/meta/robots-group.php',
        'files/meta-logo'                       => __DIR__ . '/blueprints/files/meta-logo.yml',
        'files/meta-og-image'                   => __DIR__ . '/blueprints/files/meta-og-image.yml',
        'tabs/meta/page'                        => require __DIR__ . '/blueprints/tabs/page.php',
        'tabs/meta/site'                        => require __DIR__ . '/blueprints/tabs/site.php',
    ],
    'filesMethods' => require __DIR__ . '/config/files-methods.php',
    'pageMethods' => require __DIR__ . '/config/page-methods.php',
    'siteMethods' => require __DIR__ . '/config/site-methods.php',
    'translations' => [
        'da' => require __DIR__ . '/translations/da.php',
        'de' => require __DIR__ . '/translations/de.php',
        'en' => require __DIR__ . '/translations/en.php',
        'fr' => require __DIR__ . '/translations/fr.php',
        'sv_SE' => require __DIR__ . '/translations/sv_SE.php',
    ],

    // Merged sitemap hooks (from baukasten/sitemap)
    'hooks' => [
        // Exclude specific pages from sitemap
        'meta.sitemap.url' => function (
            Page $page,
        ) {
            // Exclude pages with coverOnly set to true
            if ($page->intendedTemplate()->name() == 'item' && $page->coverOnly()->toBool(false)) {
                return false;
            }

            // Exclude section pages when designSectionToggle is disabled
            if ($page->intendedTemplate()->name() === 'section' && !getSectionToggleState()) {
                return false;
            }
        },

        // Transform URLs to frontend domain, handle language prefixes and flat URLs
        'meta.sitemap:after' => function (
            Kirby $kirby,
            \DOMElement $root
        ) {
            $site = $kirby->site();
            $cmsUrl = $kirby->url('index');
            $frontendUrl = rtrim($site->frontendUrl(), '/');
            $allLanguages = $kirby->languages();
            $defaultLanguage = $kirby->defaultLanguage();
            $sectionToggleEnabled = getSectionToggleState();

            if ($frontendUrl) {
                // Helper to transform any absolute CMS URL to frontend URL
                $transformUrl = function (string $originalAbsoluteUrl) use (
                    $cmsUrl,
                    $frontendUrl,
                    $allLanguages,
                    $defaultLanguage
                ) {
                    // Build relative path from CMS root
                    $relative = ltrim(str_replace($cmsUrl, '', $originalAbsoluteUrl), '/');

                    // Remove default language prefix when not prefixed
                    if (count($allLanguages) === 1 || (option('prefixDefaultLocale') === false)) {
                        $defaultCode = $defaultLanguage->code();
                        if ($relative === $defaultCode) {
                            $relative = '';
                        } elseif (strpos($relative, $defaultCode . '/') === 0) {
                            $relative = substr($relative, strlen($defaultCode . '/'));
                        }
                    }

                    $url = $frontendUrl . '/' . $relative;
                    // Ensure trailing slash except for plain domain
                    if ($url !== $frontendUrl && substr($url, -1) !== '/') {
                        $url .= '/';
                    }
                    return $url;
                };

                foreach ($root->getElementsByTagName('url') as $url) {
                    foreach ($url->getElementsByTagName('loc') as $loc) {
                        $originalUrl = $loc->nodeValue;

                        // Start with domain + language handling
                        $transformedUrl = $transformUrl($originalUrl);

                        // Handle flat URL structure when section toggle is disabled
                        if (!$sectionToggleEnabled) {
                            // Determine page and compute flat URI
                            $pageUri = ltrim(str_replace($cmsUrl, '', $originalUrl), '/');

                            // Strip leading language segment for lookup
                            foreach ($allLanguages as $lang) {
                                $langPrefix = $lang->code() . '/';
                                if (strpos($pageUri, $langPrefix) === 0) {
                                    $pageUri = substr($pageUri, strlen($langPrefix));
                                    break;
                                }
                            }

                            if ($page = $kirby->page($pageUri)) {
                                $flatUri = generatePageUri($page, true);

                                // Re-add language prefix if needed
                                $languagePrefix = '';
                                foreach ($allLanguages as $lang) {
                                    if (strpos($originalUrl, '/' . $lang->code() . '/') !== false) {
                                        if (count($allLanguages) > 1 && (option('prefixDefaultLocale') === true || $lang->code() !== $defaultLanguage->code())) {
                                            $languagePrefix = '/' . $lang->code();
                                        }
                                        break;
                                    }
                                }

                                $transformedUrl = $frontendUrl . $languagePrefix . '/' . $flatUri;
                                if (substr($transformedUrl, -1) !== '/') {
                                    $transformedUrl .= '/';
                                }
                            }
                        }

                        $loc->nodeValue = $transformedUrl;
                    }

                    // Handle alternate language links
                    foreach ($url->getElementsByTagName('xhtml:link') as $xhtml) {
                        $originalHref = $xhtml->getAttribute('href');
                        $transformedHref = $transformUrl($originalHref);

                        if (!$sectionToggleEnabled) {
                            $pageUri = ltrim(str_replace($cmsUrl, '', $originalHref), '/');
                            foreach ($allLanguages as $lang) {
                                $langPrefix = $lang->code() . '/';
                                if (strpos($pageUri, $langPrefix) === 0) {
                                    $pageUri = substr($pageUri, strlen($langPrefix));
                                    break;
                                }
                            }

                            if ($page = $kirby->page($pageUri)) {
                                $flatUri = generatePageUri($page, true);

                                $languagePrefix = '';
                                foreach ($allLanguages as $lang) {
                                    if (strpos($originalHref, '/' . $lang->code() . '/') !== false) {
                                        if (count($allLanguages) > 1 && (option('prefixDefaultLocale') === true || $lang->code() !== $defaultLanguage->code())) {
                                            $languagePrefix = '/' . $lang->code();
                                        }
                                        break;
                                    }
                                }

                                $transformedHref = $frontendUrl . $languagePrefix . '/' . $flatUri;
                                if (substr($transformedHref, -1) !== '/') {
                                    $transformedHref .= '/';
                                }
                            }
                        }

                        $xhtml->setAttribute('href', $transformedHref);
                    }
                }
            }
        }
    ]
]);
