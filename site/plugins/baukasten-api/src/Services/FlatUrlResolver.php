<?php

namespace BaukastenApi\Services;

use BaukastenApi\Helpers\UrlHelper;
use Kirby\Cms\Page;

/**
 * Service for handling flat URL resolution when section toggle is disabled.
 * This service manages complex URL routing logic and edge cases.
 */
class FlatUrlResolver
{
    /**
     * Handle flat URL resolution when section toggle is disabled.
     * This route handler attempts to find pages by slug when direct URI match fails.
     *
     * @param string $path The requested path
     * @return mixed Page response or redirect
     */
    public static function handleFlatUrlResolution($path)
    {
        $site = site();

        // First, try to find the page using standard Kirby resolution
        $page = $site->find($path);
        if ($page && $page->isReadable()) {
            return $page;
        }

        // If section toggle is enabled, let Kirby handle normally
        if (UrlHelper::getSectionToggleState()) {
            return null; // Let Kirby's default 404 handling take over
        }

        // Section toggle is disabled - try to resolve flat URLs
        $segments = explode('/', trim($path, '/'));

        // Find all pages that could match this flat URL structure
        $candidatePages = self::findPagesByFlatUrl($path, $segments);

        if (empty($candidatePages)) {
            // No candidates found - try fallback to hierarchical URL
            return self::tryHierarchicalFallback($path, $segments);
        }

        // If we have exactly one candidate, use it
        if (count($candidatePages) === 1) {
            $page = $candidatePages[0];
            if ($page->isReadable()) {
                return $page;
            }
        }

        // Multiple candidates - use priority resolution
        $resolvedPage = self::resolvePriorityConflict($candidatePages, $path);
        if ($resolvedPage && $resolvedPage->isReadable()) {
            return $resolvedPage;
        }

        // Still no resolution - try hierarchical fallback
        return self::tryHierarchicalFallback($path, $segments);
    }

    /**
     * Find pages that could match a flat URL structure.
     *
     * @param string $path The requested path
     * @param array $segments Path segments
     * @return array Array of candidate pages
     */
    public static function findPagesByFlatUrl($path, $segments)
    {
        $site = site();
        $candidates = [];
        $lastSegment = end($segments);

        // Search through all pages to find matches
        foreach ($site->index() as $page) {
            // Skip section pages as they shouldn't be accessible in flat mode
            if ($page->intendedTemplate()->name() === 'section') {
                continue;
            }

            // Check if this page's flat URL matches the requested path
            $flatUri = UrlHelper::generatePageUri($page, true);
            if ($flatUri === $path) {
                $candidates[] = $page;
                continue;
            }

            // Also check if the page slug matches the last segment
            if ($page->slug() === $lastSegment) {
                // Verify this page would generate the requested flat URL
                $expectedFlatUri = UrlHelper::generatePageUri($page, true);
                if ($expectedFlatUri === $path) {
                    $candidates[] = $page;
                }
            }
        }

        return $candidates;
    }

    /**
     * Resolve conflicts when multiple pages could match the same flat URL.
     *
     * @param array $candidates Array of candidate pages
     * @param string $path The requested path
     * @return Page|null The resolved page or null
     */
    public static function resolvePriorityConflict($candidates, $path)
    {
        if (empty($candidates)) {
            return null;
        }

        // Sort candidates by priority
        usort($candidates, function ($a, $b) {
            // Priority 1: Fewer parent levels (closer to root)
            $aDepth = count(explode('/', $a->uri()));
            $bDepth = count(explode('/', $b->uri()));
            if ($aDepth !== $bDepth) {
                return $aDepth - $bDepth;
            }

            // Priority 2: Listed pages over unlisted
            if ($a->isListed() !== $b->isListed()) {
                return $b->isListed() ? 1 : -1;
            }

            // Priority 3: Sort order (num field)
            $aNum = $a->num();
            $bNum = $b->num();
            if ($aNum !== $bNum) {
                return $aNum - $bNum;
            }

            // Priority 4: Alphabetical by slug as final tiebreaker
            return strcmp($a->slug(), $b->slug());
        });

        return $candidates[0];
    }

    /**
     * Try to find a page using hierarchical URL structure as fallback.
     *
     * @param string $path The requested path
     * @param array $segments Path segments
     * @return mixed Page response, redirect, or null
     */
    public static function tryHierarchicalFallback($path, $segments)
    {
        $site = site();

        // Try to find the page using hierarchical structure
        $hierarchicalPage = $site->find($path);
        if ($hierarchicalPage && $hierarchicalPage->isReadable()) {
            // If we found a hierarchical page and section toggle is disabled,
            // we might want to redirect to the flat URL equivalent
            $flatUri = UrlHelper::generatePageUri($hierarchicalPage, true);
            if ($flatUri !== $path && !UrlHelper::getSectionToggleState()) {
                // Redirect to the flat URL
                return go($flatUri, 301);
            }
            return $hierarchicalPage;
        }

        // No fallback found - return null to trigger 404
        return null;
    }

    /**
     * Check if a section page should be accessible based on the toggle setting.
     *
     * @param Page $page The page to check
     * @return bool True if the section page is accessible
     */
    public static function isSectionPageAccessible($page)
    {
        // If the page is not a section, it's always accessible
        if ($page->intendedTemplate()->name() !== 'section') {
            return true;
        }

        // Section pages are only accessible when the toggle is enabled
        return UrlHelper::getSectionToggleState();
    }
}
