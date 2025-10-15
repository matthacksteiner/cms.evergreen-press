<?php

/*
|--------------------------------------------------------------------------
| Kirby Configuration Array
|--------------------------------------------------------------------------
*/

return [
	// 'debug' => true,
	'panel.dev' => true, // Enable panel development mode for plugin changes
	'auth' => [
		'methods' => ['password', 'password-reset']
	],
	'panel.install'   => true,
	'date.handler'    => 'strftime',
	'languages'       => true,
	'prefixDefaultLocale' => false,
	'error'           => 'z-error',
	'panel' => [
		'css'     => 'assets/css/baukasten-panel.css',
		'favicon' => 'assets/img/baukasten-favicon.ico',
		'viewButtons' => [
			'site' => [
				'frontend-open' => function () {
					$frontendUrl = rtrim(site()->frontendUrl()->toString(), '/') . '/';

					return [
						'icon' => 'window',
						'text' => 'Öffnen',
						'link' => $frontendUrl,
						'target' => '_blank'
					];
				},
				'frontend-preview' => function () {
					$frontendUrl = rtrim(site()->frontendUrl()->toString(), '/');
					$previewUrl = $frontendUrl . '/preview/';

					return [
						'icon' => 'open',
						'text' => 'Vorschau',
						'link' => $previewUrl,
						'target' => '_blank'
					];
				}
			],
			'page' => [
				'frontend-open' => function (Kirby\Cms\Page $page) {
					$frontendUrl = rtrim(site()->frontendUrl()->toString(), '/');

					// Special case for home page - link to root
					if ($page->isHomePage() || $page->template()->name() === 'home' || $page->uri() === 'home') {
						$pageUrl = $frontendUrl . '/';
					} else {
						$pageUri = $page->uri();
						$pageUrl = $frontendUrl;
						if (!empty($pageUri)) {
							$pageUrl .= '/' . $pageUri . '/';
						} else {
							$pageUrl .= '/';
						}
					}

					return [
						'icon' => 'window',
						'text' => 'Öffnen',
						'link' => $pageUrl,
						'target' => '_blank'
					];
				},
				'frontend-preview' => function (Kirby\Cms\Page $page) {
					$frontendUrl = rtrim(site()->frontendUrl()->toString(), '/');

					// Special case for home page - link to preview root
					if ($page->isHomePage() || $page->template()->name() === 'home' || $page->uri() === 'home') {
						$previewUrl = $frontendUrl . '/preview/';
					} else {
						$pageUri = $page->uri();
						$previewUrl = $frontendUrl . '/preview';
						if (!empty($pageUri)) {
							$previewUrl .= '/' . $pageUri . '/';
						} else {
							$previewUrl .= '/';
						}
					}

					return [
						'icon' => 'open',
						'text' => 'Vorschau',
						'link' => $previewUrl,
						'target' => '_blank'
					];
				}
			]
		]
	],
	'thumbs' => [
		'quality' => 99,
		'format'  => 'webp',
	],
	'cache' => [
		'pages' => [
			'active' => true,
			'type'   => 'file'
		]
	],
	'routes' => [
		[
			'pattern' => 'sitemap.xml',
			'action' => function () {
				$sitemap = BaukastenMeta\Meta\Sitemap::factory();
				$xml = $sitemap->generate();

				return new Kirby\Cms\Response($xml, 'application/xml');
			}
		]
	],
	'ready' => function () {
		return [
			'johannschopplich.deploy-trigger' => [
				'deployUrl' => env('DEPLOY_URL', 'https://api.netlify.com/build_hooks/65142ee2a2de9b24080dcc95'),
			],
		];
	},
];
