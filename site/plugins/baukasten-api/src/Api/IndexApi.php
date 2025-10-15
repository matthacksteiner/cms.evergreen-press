<?php

namespace BaukastenApi\Api;

use Kirby\Http\Response;

/**
 * IndexApi class handles the index.json endpoint
 *
 * This class extracts the indexJsonData and indexJson functions from config.php
 * and maintains identical JSON response structure.
 */
class IndexApi
{
    /**
     * Handle the index.json endpoint request
     *
     * @return \Kirby\Http\Response JSON response with index data
     */
    public static function handle()
    {
        return Response::json(self::getData());
    }

    /**
     * Generate the index data
     *
     * @return array Array of page data for the index
     */
    public static function getData()
    {
        $site = site();
        $kirby = kirby();
        $index = [];

        foreach (site()->index() as $page) {
            // Skip pages that have coverOnly set to true
            if ($page->intendedTemplate()->name() == 'item' && $page->coverOnly()->toBool(false)) {
                continue;
            }

            // Skip pages with empty URIs when they shouldn't be accessible
            $pageUri = generatePageUri($page);
            if ($pageUri === '') {
                // Skip sections when toggle is disabled, but keep home page
                if ($page->intendedTemplate()->name() === 'section' && !getSectionToggleState()) {
                    continue;
                }
            }

            $translations = [];
            foreach ($kirby->languages() as $language) {
                // Generate URI for specific language without changing global context
                if (getSectionToggleState()) {
                    // When toggle is enabled, use hierarchical URIs
                    $translations[$language->code()] = $page->uri($language->code());
                } else {
                    // When toggle is disabled, generate flat URI for this language
                    if ($page->isHomePage()) {
                        $translations[$language->code()] = 'home';
                    } else {
                        // Generate flat URI by skipping section parents
                        $segments = [];
                        $current = $page;

                        while ($current && !$current->isHomePage()) {
                            // Only add non-section pages to the URI segments
                            if ($current->intendedTemplate()->name() !== 'section') {
                                array_unshift($segments, $current->slug($language->code()));
                            }
                            $current = $current->parent();
                        }

                        $translations[$language->code()] = implode('/', $segments);
                    }
                }
            }

            // Get effective parent for parent reference
            $effectiveParent = null;
            if ($page->intendedTemplate()->name() == 'item') {
                $parent = getEffectiveParent($page);
                $effectiveParent = $parent ? generatePageUri($parent) : null;
            }

            $index[] = [
                "id"               => $page->id(),
                "uri"              => generatePageUri($page),
                "status"           => $page->status(),
                "intendedTemplate" => $page->intendedTemplate()->name(),
                "parent"           => $effectiveParent,
                "coverOnly"        => $page->intendedTemplate()->name() == 'item'
                    ? $page->coverOnly()->toBool(false)
                    : null,
                "translations"     => $translations,
                "navigation"       => getPageNavigation($page),
            ];
        }

        return $index;
    }
}
