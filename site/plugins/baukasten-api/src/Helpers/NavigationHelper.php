<?php

namespace BaukastenApi\Helpers;

/**
 * NavigationHelper class containing navigation-related functions
 */
class NavigationHelper
{
    /**
     * Get next and previous page navigation for pages within the same parent/section
     *
     * @param \Kirby\Cms\Page $page The page to get navigation for
     * @return array Array containing nextPage and prevPage information
     */
    public static function getPageNavigation($page)
    {
        // Get the effective parent based on section toggle setting
        $effectiveParent = static::getEffectiveParent($page);

        // Get siblings - this works even if effective parent is null
        $siblings = static::getNavigationSiblings($page, $effectiveParent);

        $nextPage = null;
        $prevPage = null;

        // Use Kirby's built-in navigation methods with custom collection
        if ($next = $page->nextListed($siblings)) {
            $nextPage = [
                'title' => (string) $next->title(),
                'uri' => \BaukastenApi\Helpers\UrlHelper::generatePageUri($next),
                'id' => $next->id(),
            ];
        }

        if ($prev = $page->prevListed($siblings)) {
            $prevPage = [
                'title' => (string) $prev->title(),
                'uri' => \BaukastenApi\Helpers\UrlHelper::generatePageUri($prev),
                'id' => $prev->id(),
            ];
        }

        return [
            'nextPage' => $nextPage,
            'prevPage' => $prevPage,
        ];
    }

    /**
     * Get navigation siblings for a page, handling section toggle logic
     *
     * @param \Kirby\Cms\Page $page The page to get siblings for
     * @param \Kirby\Cms\Page|null $effectiveParent The effective parent page
     * @return \Kirby\Cms\Pages Collection of sibling pages
     */
    public static function getNavigationSiblings($page, $effectiveParent)
    {
        $sectionToggleEnabled = \BaukastenApi\Helpers\UrlHelper::getSectionToggleState();

        if ($sectionToggleEnabled) {
            // Standard behavior: get siblings from actual parent
            $parent = $page->parent();
            if (!$parent) {
                return new \Kirby\Cms\Pages();
            }

            return $parent->children()->listed()->filter(function ($sibling) {
                return !($sibling->intendedTemplate()->name() == 'item' && $sibling->coverOnly()->toBool(false));
            });
        } else {
            // Flat URL mode: find siblings that share the same original parent (section)
            $originalParent = $page->parent();
            if (!$originalParent) {
                return new \Kirby\Cms\Pages();
            }

            // Get all children of the original parent (section)
            return $originalParent->children()->listed()->filter(function ($sibling) {
                return !($sibling->intendedTemplate()->name() == 'item' && $sibling->coverOnly()->toBool(false));
            });
        }
    }

    /**
     * Get the effective parent of a page when sections might be excluded.
     *
     * @param \Kirby\Cms\Page $page The page to get the effective parent for
     * @return \Kirby\Cms\Page|null The effective parent page
     */
    public static function getEffectiveParent($page)
    {
        if (!$page->parent()) {
            return null;
        }

        // If toggle is enabled, return the actual parent
        if (\BaukastenApi\Helpers\UrlHelper::getSectionToggleState()) {
            return $page->parent();
        }

        // If toggle is disabled, skip section parents
        $current = $page->parent();
        while ($current && !$current->isHomePage()) {
            if ($current->intendedTemplate()->name() !== 'section') {
                return $current;
            }
            $current = $current->parent();
        }

        // If we've reached the homepage or no parent, return the homepage
        return $current;
    }
}
