<?php

/**
 * Baukasten Kirby Routes Plugin
 *
 * This plugin provides a way to view all routes in your Kirby CMS setup.
 * It adds a new route to access the routes list in the browser.
 */

use Kirby\Cms\App as Kirby;
use Kirby\Http\Response;

Kirby::plugin('baukasten/kirby-routes', [
    'options' => [
        'route' => 'routes', // The URL path to access the routes list
    ],
    'routes' => [
        [
            'pattern' => 'routes',
            'method'  => 'GET',
            'action'  => function () {
                return baukastenKirbyRoutes();
            }
        ],
    ],
]);

/**
 * Generate and display the routes list
 */
function baukastenKirbyRoutes()
{
    $kirby = kirby();
    $defaultLanguage = $kirby->defaultLanguage();

    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kirby CMS Routes</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                line-height: 1.6;
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
                color: #333;
            }
            h1, h2 {
                color: #2c3e50;
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin-bottom: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f5f5f5;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            code {
                background-color: #f5f5f5;
                padding: 2px 4px;
                border-radius: 3px;
                font-family: monospace;
            }
            .redirect {
                color: #666;
                font-style: italic;
            }
        </style>
    </head>
    <body>
        <h1>Kirby CMS Routes</h1>
        <p>This document lists all routes available in the Baukasten Kirby CMS setup.</p>';

    // Get all configured routes from Kirby
    $routes = $kirby->routes();

    // Add configured routes
    $html .= '<h2>Configured Routes</h2>
        <p>These routes are explicitly defined in the configuration.</p>
        <table>
            <tr>
                <th>Pattern</th>
                <th>Method</th>
                <th>Language</th>
                <th>Description</th>
            </tr>';

    foreach ($routes as $route) {
        $pattern = $route['pattern'];
        $method = $route['method'] ?? 'GET';
        $language = $route['language'] ?? '*';

        // Get the function name from the action if possible
        $description = '';
        if (isset($route['action']) && is_callable($route['action'])) {
            $reflection = new ReflectionFunction($route['action']);
            $description = 'Function: ' . $reflection->getName();
        }

        $html .= '<tr><td><code>/' . $pattern . '</code></td><td>' . $method . '</td><td>' . $language . '</td><td>' . $description . '</td></tr>';
    }
    $html .= '</table>';

    // Add implicit routes for pages
    $html .= '<h2>Implicit Page Routes</h2>
        <p>These routes are automatically created by Kirby for each page.</p>
        <table>
            <tr>
                <th>Pattern</th>
                <th>Method</th>
                <th>Language</th>
                <th>Description</th>
            </tr>';

    // Get all pages
    $pages = $kirby->site()->index();
    foreach ($pages as $page) {
        $uri = $page->uri();
        $template = $page->intendedTemplate()->name();

        // Default language routes
        $html .= '<tr><td><code>/' . $uri . '</code></td><td>GET</td><td>' . $defaultLanguage->code() . '</td><td>Page: ' . $template . ' (Default language)</td></tr>';
        $html .= '<tr><td><code>/' . $uri . '.json</code></td><td>GET</td><td>' . $defaultLanguage->code() . '</td><td>JSON: ' . $template . ' (Default language)</td></tr>';

        // Non-default language routes
        foreach ($kirby->languages() as $language) {
            if ($language->code() !== $defaultLanguage->code()) {
                $langUri = $page->uri($language->code());
                if ($langUri !== $uri) {
                    $html .= '<tr><td><code>/' . $language->code() . '/' . $langUri . '</code></td><td>GET</td><td>' . $language->code() . '</td><td>Page: ' . $template . '</td></tr>';
                    $html .= '<tr><td><code>/' . $language->code() . '/' . $langUri . '.json</code></td><td>GET</td><td>' . $language->code() . '</td><td>JSON: ' . $template . '</td></tr>';
                }
            }
        }
    }
    $html .= '</table>';

    // Add footer
    $html .= '<h2>Notes</h2>
        <ul>
            <li>All routes support the GET method by default unless specified otherwise.</li>
            <li>JSON routes return data in JSON format.</li>
            <li>Default language (' . $defaultLanguage->code() . ') routes are served without language prefix.</li>
            <li>Non-default language routes require their language code prefix.</li>
            <li>Requests to default language with prefix (/' . $defaultLanguage->code() . '/*) redirect to the unprefixed URL.</li>
            <li>Requests to non-default languages without prefix redirect to their prefixed URL.</li>
        </ul>
    </body>
    </html>';

    return new Response($html, 'text/html');
}
