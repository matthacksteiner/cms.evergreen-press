<?php

namespace BaukastenApi\Helpers;

/**
 * UrlHelper class containing URL generation and routing functions
 */
class UrlHelper
{
    /**
     * Get the current state of the designSectionToggle setting.
     *
     * @return bool True if sections should be included in URLs, false otherwise
     */
    public static function getSectionToggleState(): bool
    {
        return site()->designSectionToggle()->toBool(true);
    }

    /**
     * Generate a page URI based on the section toggle setting.
     *
     * @param \Kirby\Cms\Page $page The page to generate URI for
     * @param bool $respectSectionToggle Whether to respect the toggle setting
     * @return string The generated URI
     */
    public static function generatePageUri($page, bool $respectSectionToggle = true): string
    {
        // Handle home page specially - always return 'home' instead of empty string
        if ($page->isHomePage()) {
            return 'home';
        }

        // If not respecting toggle or toggle is enabled, use standard URI
        if (!$respectSectionToggle || static::getSectionToggleState()) {
            return $page->uri();
        }

        // Generate flat URI by skipping section parents
        $segments = [];
        $current = $page;

        while ($current && !$current->isHomePage()) {
            // Only add non-section pages to the URI segments
            if ($current->intendedTemplate()->name() !== 'section') {
                $slug = $current->slug();


                array_unshift($segments, $slug);
            }
            $current = $current->parent();
        }

        $result = implode('/', $segments);


        return $result;
    }
}
