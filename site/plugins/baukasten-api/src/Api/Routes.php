<?php

namespace BaukastenApi\Api;

use BaukastenApi\Api\IndexApi;
use BaukastenApi\Api\GlobalApi;
use BaukastenApi\Services\FlatUrlResolver;

/**
 * Routes class for managing all API routes
 */
class Routes
{
    /**
     * Get all routes for the plugin
     *
     * @return array
     */
    public static function getRoutes(): array
    {
        return [
            [
                'pattern'  => 'index.json',
                'language' => '*',
                'method'   => 'GET',
                'action'   => function () {
                    return IndexApi::handle();
                }
            ],
            [
                'pattern'  => 'global.json',
                'language' => '*',
                'method'   => 'GET',
                'action'   => function () {
                    return GlobalApi::handle();
                }
            ],
            [
                'pattern'  => '/',
                'method'   => 'GET',
                'action'   => function () {
                    return go('/panel');
                }
            ],
            [
                'pattern'  => '(:any).json',
                'language' => '*',
                'method'   => 'GET',
                'action'   => function ($path) {
                    $kirby = kirby();
                    $site = site();

                    // Get the full request URI to handle language routing correctly
                    $fullPath = $kirby->request()->path();
                    if (substr($fullPath, -5) === '.json') {
                        $pageUri = substr($fullPath, 0, -5);
                    } else {
                        $pageUri = $path;
                    }

                    // Remove language prefix if present
                    $languages = $kirby->languages();
                    if ($languages) {
                        foreach ($languages as $lang) {
                            $langPrefix = $lang->code() . '/';
                            if (substr($pageUri, 0, strlen($langPrefix)) === $langPrefix) {
                                $pageUri = substr($pageUri, strlen($langPrefix));
                                break;
                            }
                        }
                    }

                    // Skip requests for empty URIs (home page and excluded sections)
                    if ($pageUri === '') {
                        return null; // 404 for empty URI requests
                    }

                    // Try to find the page by flat URI
                    $indexData = IndexApi::getData();
                    foreach ($indexData as $pageData) {
                        if ($pageData['uri'] === $pageUri) {
                            // Found matching page by flat URI, get the actual page
                            $actualPage = $site->find($pageData['id']);
                            if ($actualPage) {
                                // Ensure proper JSON response
                                $kirby->response()->type('application/json');
                                return $actualPage->render([], 'json');
                            }
                        }
                    }

                    // Fallback: try direct page lookup
                    $page = $site->find($pageUri);
                    if ($page) {
                        // Ensure proper JSON response
                        $kirby->response()->type('application/json');
                        return $page->render([], 'json');
                    }

                    return null; // 404
                }
            ],
            [
                'pattern'  => '(:any)',
                'language' => '*',
                'method'   => 'GET',
                'action'   => function ($path) {
                    return FlatUrlResolver::handleFlatUrlResolution($path);
                }
            ]
        ];
    }
}
