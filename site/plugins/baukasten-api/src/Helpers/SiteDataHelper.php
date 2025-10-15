<?php

namespace BaukastenApi\Helpers;

/**
 * SiteDataHelper class containing site data extraction functions
 */
class SiteDataHelper
{
    /**
     * Extract favicon data from the site.
     *
     * @param \Kirby\Cms\Site $site The site instance
     * @return array|null Favicon data or null if not found
     */
    public static function getFavicon($site)
    {
        $fav = $site->faviconFiles()->toObject();
        if (!$fav) {
            return null;
        }
        return [
            "svgSrc"     => $fav->faviconFileSvg()->toFile() ? (string)$fav->faviconFileSvg()->toFile()->url() : null,
            "icoSrc"     => $fav->faviconFileIco()->toFile() ? (string)$fav->faviconFileIco()->toFile()->url() : null,
            "png192Src"  => $fav->faviconFilePng1()->toFile() ? (string)$fav->faviconFilePng1()->toFile()->url() : null,
            "png512Src"  => $fav->faviconFilePng2()->toFile() ? (string)$fav->faviconFilePng2()->toFile()->url() : null,
            "pngAppleSrc" => $fav->faviconFilePng3()->toFile() ? (string)$fav->faviconFilePng3()->toFile()->url() : null,
        ];
    }

    /**
     * Returns a navigation array from the given field.
     *
     * @param \Kirby\Cms\Site $site The site instance
     * @param string $field The field name to extract navigation from
     * @return array|null Navigation array or null if empty
     */
    public static function getNavigation($site, $field)
    {
        $nav = [];
        foreach ($site->$field()->toStructure() as $item) {
            $nav[] = getLinkArray($item->linkobject());
        }
        return count($nav) > 0 ? $nav : null;
    }

    /**
     * Returns logo file data.
     *
     * @param \Kirby\Cms\Site $site The site instance
     * @return array Logo file data
     */
    public static function getLogoFile($site)
    {
        $logoObj = $site->headerLogo()->toObject();
        if ($logoObj->logoFile()->isNotEmpty()) {
            $file = $logoObj->logoFile()->toFile();
            return [
                "src"    => (string)$file->url(),
                "alt"    => (string)$file->alt()->or($site->title() . " Logo"),
                "source" => file_get_contents($file->root()),
                "width"  => $file->width(),
                "height" => $file->height(),
                "source" => file_get_contents($file->root()),
            ];
        }
        return [];
    }

    /**
     * Returns the logo CTA as a link array.
     *
     * @param \Kirby\Cms\Site $site The site instance
     * @return array Logo CTA data
     */
    public static function getLogoCta($site)
    {
        $logoObj = $site->headerLogo()->toObject();
        if ($logoObj->logoCta()->isNotEmpty()) {
            return getLinkArray($logoObj->logoCta());
        }
        return [];
    }

    /**
     * Processes font file structures.
     *
     * @param \Kirby\Cms\Site $site The site instance
     * @return array|null Font data or null if empty
     */
    public static function getFonts($site)
    {
        $fonts = [];
        foreach ($site->fontFile()->toStructure() as $fontItem) {
            $file2 = $fontItem->file2()->toFile();
            if ($file2) {
                $fonts[] = [
                    "name"        => (string)$fontItem->name(),
                    "url2"        => (string)$file2->url(),
                ];
            }
        }
        return count($fonts) > 0 ? $fonts : null;
    }

    /**
     * Processes font size structures.
     *
     * @param \Kirby\Cms\Site $site The site instance
     * @return array Font size data
     */
    public static function getFontSizes($site)
    {
        $sizes = [];
        foreach ($site->fontSize()->toStructure() as $item) {
            $sizes[] = [
                "name"                => (string)$item->name(),
                "sizeMobile"          => (string)$item->sizeMobile(),
                "lineHeightMobile"    => (string)$item->lineHeightMobile(),
                "letterSpacingMobile" => (string)$item->letterSpacingMobile(),
                "sizeDesktop"         => (string)$item->sizeDesktop(),
                "lineHeightDesktop"   => (string)$item->lineHeightDesktop(),
                "letterSpacingDesktop" => (string)$item->letterSpacingDesktop(),
                "sizeDesktopXl"       => (string)$item->sizeDesktopXl(),
                "lineHeightDesktopXl" => (string)$item->lineHeightDesktopXl(),
                "letterSpacingDesktopXl" => (string)$item->letterSpacingDesktopXl(),
                "transform"           => (string)$item->transform() ?: 'none',
                "decoration"          => (string)$item->decoration() ?: 'none',
            ];
        }
        return $sizes;
    }

    /**
     * Extract headlines from the site.
     *
     * @param \Kirby\Cms\Site $site The site instance
     * @return array Headlines data
     */
    public static function getHeadlines($site)
    {
        $headlines = [];
        for ($i = 1; $i <= 6; $i++) {
            $tag = "h$i";
            $fontKey = "{$tag}font";
            $sizeKey = "{$tag}size";
            $headlines[$tag] = [
                "font" => (string)$site->headlines()->toObject()->$fontKey(),
                "size" => (string)$site->headlines()->toObject()->$sizeKey(),
            ];
        }
        return $headlines;
    }

    /**
     * Returns analytics codes if toggled on.
     *
     * @param \Kirby\Cms\Site $site The site instance
     * @return array Analytics data
     */
    public static function getAnalytics($site)
    {
        $searchConsoleCode = $site->searchConsoleToggle()->toBool(false)
            ? (string)$site->searchConsoleCode()
            : null;
        $googleAnalyticsCode = $site->googleAnalyticsToggle()->toBool(false)
            ? (string)$site->googleAnalyticsCode()
            : null;
        $analyticsLink = $site->analyticsLink()->isNotEmpty()
            ? getLinkArray($site->analyticsLink())
            : null;
        return [
            'searchConsoleCode'  => $searchConsoleCode,
            'googleAnalyticsCode' => $googleAnalyticsCode,
            'analyticsLink'       => $analyticsLink,
        ];
    }
}
