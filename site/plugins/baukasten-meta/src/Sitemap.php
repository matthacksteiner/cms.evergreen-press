<?php

namespace BaukastenMeta\Meta;

use DOMDocument;
use DOMElement;
use Kirby\Cms\App;
use Kirby\Cms\Languages;
use Kirby\Cms\Page;

class Sitemap
{
    protected App $kirby;
    protected bool $isMultilang;
    protected Languages $languages;

    public function __construct()
    {
        $this->kirby = kirby();
        $this->isMultilang = $this->kirby->multilang();
        $this->languages   = $this->kirby->languages();
    }

    public static function factory(...$args): static
    {
        return new static(...$args);
    }

    public function generate(): string
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $root = $doc->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xhtml', 'http://www.w3.org/1999/xhtml');

        // Allow hook to change $doc and $root, e.g. adding namespaces or other attributes.
        $this->kirby->trigger('meta.sitemap:before', [
            'kirby' => $this->kirby,
            'doc' => $doc,
            'root' => $root
        ]);

        foreach ($this->kirby->site()->index() as $page) {
            $this->urlsForPage($page, $doc, $root);
        }

        $root = $doc->appendChild($root);

        // Allow hook to alter the DOM
        $this->kirby->trigger('meta.sitemap:after', [
            'kirby' => $this->kirby,
            'doc' => $doc,
            'root' => $root
        ]);

        return $doc->saveXML();
    }

    protected function urlsForPage(
        Page $page,
        DOMDocument $doc,
        DOMElement $root
    ): void {
        $meta = $page->meta();

        if (static::isPageIndexible($page) === false) {
            // Exclude page, if explicitly excluded in page settings
            // for global settings
            return;
        }

        // Emit one <url> entry per language (including alternates)
        if ($this->isMultilang && $this->languages->count() > 0) {
            foreach ($this->languages as $language) {
                $code = $language->code();

                $url = $doc->createElement('url');
                $url->appendChild($doc->createElement('loc', $page->url($code)));

                if ($this->kirby->option('baukastenMeta.meta.sitemap.detailSettings') !== false) {
                    $priority = $meta->priority();
                    if ($priority !== null) {
                        $url->appendChild($doc->createElement('priority', number_format($priority, 1, '.', '')));
                    }
                    if ($changefreq = $meta->changefreq()) {
                        $url->appendChild($doc->createElement('changefreq', $changefreq));
                    }
                }

                // Add xhtml:link alternates for all languages + x-default
                if ($this->languages->count() > 1) {
                    foreach ($this->languages as $altLanguage) {
                        $altLink = $doc->createElement('xhtml:link');
                        $altLink->setAttribute('rel', 'alternate');
                        $altLink->setAttribute('hreflang', $altLanguage->code());
                        $altLink->setAttribute('href', $page->url($altLanguage->code()));
                        $url->appendChild($altLink);
                    }

                    // x-default should point to the default language URL (hook will normalize)
                    $defaultLanguage = $this->kirby->defaultLanguage();
                    $xDefault = $doc->createElement('xhtml:link');
                    $xDefault->setAttribute('rel', 'alternate');
                    $xDefault->setAttribute('hreflang', 'x-default');
                    $xDefault->setAttribute('href', $page->url($defaultLanguage->code()));
                    $url->appendChild($xDefault);
                }

                // Add lastmod
                $url->appendChild($doc->createElement('lastmod', date('Y-m-d', $meta->lastmod())));

                // Hook to allow filtering
                if ($this->kirby->apply('meta.sitemap.url', [
                    'kirby' => $this->kirby,
                    'page' => $page,
                    'meta' => $meta,
                    'doc' => $doc,
                    'url' => $url,
                    'include' => true,
                ], 'include') !== false) {
                    $root->appendChild($url);
                }
            }
        } else {
            // Single-language site: keep previous behavior
            $url = $doc->createElement('url');
            $url->appendChild($doc->createElement('loc', $page->url()));

            if ($this->kirby->option('baukastenMeta.meta.sitemap.detailSettings') !== false) {
                $priority = $meta->priority();
                if ($priority !== null) {
                    $url->appendChild($doc->createElement('priority', number_format($priority, 1, '.', '')));
                }
                if ($changefreq = $meta->changefreq()) {
                    $url->appendChild($doc->createElement('changefreq', $changefreq));
                }
            }

            $url->appendChild($doc->createElement('lastmod', date('Y-m-d', $meta->lastmod())));

            if ($this->kirby->apply('meta.sitemap.url', [
                'kirby' => $this->kirby,
                'page' => $page,
                'meta' => $meta,
                'doc' => $doc,
                'url' => $url,
                'include' => true,
            ], 'include') !== false) {
                $root->appendChild($url);
            }
        }
    }

    public static function isPageIndexible(Page $page): bool
    {
        // pages have to pass a set of for being indexible. If any test
        // fails, the page is excluded from index

        $templatesExclude = option('baukastenMeta.meta.sitemap.templates.exclude', []);
        $templatesExcludeRegex = '!^(?:' . implode('|', $templatesExclude) . ')$!i';

        $templatesIncludeUnlisted = option('baukastenMeta.meta.sitemap.templates.includeUnlisted', []);
        $templatesIncludeUnlistedRegex = '!^(?:' . implode('|', $templatesIncludeUnlisted) . ')$!i';

        $pagesExclude = option('baukastenMeta.meta.sitemap.pages.exclude', []);
        $pagesExcludeRegex = '!^(?:' . implode('|', $pagesExclude) . ')$!i';

        $pagesIncludeUnlisted = option('baukastenMeta.meta.sitemap.pages.includeUnlisted', []);
        $pagesIncludeUnlistedRegex = '!^(?:' . implode('|', $pagesIncludeUnlisted) . ')$!i';

        if ($page->isErrorPage()) {
            // error page is always excluded from sitemap
            return false;
        }


        if (! $page->isHomePage() && $page->status() === 'unlisted') {
            if (
                preg_match($templatesIncludeUnlistedRegex, $page->intendedTemplate()->name()) !== 1
                && preg_match($pagesIncludeUnlistedRegex, $page->id()) !== 1
            ) {
                // unlisted pages are only indexible, if exceptions are
                // defined for them based on page id or template
                return false;
            }
        }

        if (preg_match($templatesExcludeRegex, $page->intendedTemplate()->name()) === 1) {
            // page is in exclude-list of templates
            return false;
        }

        if (preg_match($pagesExcludeRegex, $page->id()) === 1) {
            // page is in exclude-list of page IDs
            return false;
        }

        if (! is_null($page->parent())) {
            // test indexability of parent pages as well
            return static::isPageIndexible($page->parent());
        }

        return true;
    }
}
