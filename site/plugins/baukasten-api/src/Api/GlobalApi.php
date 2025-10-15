<?php

namespace BaukastenApi\Api;

use Kirby\Http\Response;

/**
 * GlobalApi class handles the global.json endpoint
 *
 * This class extracts the globalJsonData and globalJson functions from config.php
 * and preserves all current global data fields.
 */
class GlobalApi
{
    /**
     * Handle the global.json endpoint request
     *
     * @return \Kirby\Http\Response JSON response with global data
     */
    public static function handle()
    {
        return Response::json(self::getData());
    }

    /**
     * Generate the global data
     *
     * @return array Array of global site data
     */
    public static function getData()
    {
        $site   = site();
        $kirby  = kirby();
        $analytics = getAnalytics($site);

        return [
            "kirbyUrl"              => (string)$kirby->url('index'),
            "siteUrl"               => (string)$site->url(),
            "siteTitle"             => (string)$site->title(),
            "defaultLang"           => getDefaultLanguage($kirby),
            "translations"          => getTranslations($kirby),
            "prefixDefaultLocale"   => option('prefixDefaultLocale'),
            "maintenanceToggle"     => ($maintenancePage = $site->find('maintenance')) ? $maintenancePage->maintenanceToggle()->toBool() : false,
            "allLang"               => getAllLanguages($kirby),
            "favicon"               => getFavicon($site),
            "frontendUrl"           => (string)$site->frontendUrl(),
            "navHeader"             => getNavigation($site, 'navHeader'),
            "navHamburger"          => getNavigation($site, 'navHambuger'),
            "colorPrimary"          => (string)$site->colorPrimary(),
            "colorSecondary"        => (string)$site->colorSecondary(),
            "colorTertiary"         => (string)$site->colorTertiary(),
            "colorBlack"            => (string)$site->colorBlack(),
            "colorWhite"            => (string)$site->colorWhite(),
            "colorTransparent"      => (string)$site->colorTransparent(),
            "colorBackground"       => (string)$site->colorBackground(),
            "font"                  => getFonts($site),
            "fontSize"              => getFontSizes($site),
            "headlines"             => getHeadlines($site),
            "headerActive"          => $site->headerActive()->toBool(),
            "headerFont"            => (string)$site->headerMenu()->toObject()->headerFont(),
            "headerFontSize"        => (string)$site->headerMenu()->toObject()->headerFontSize(),
            "headerColor"           => (string)$site->headerMenu()->toObject()->headerColor(),
            "headerColorActive"     => (string)$site->headerMenu()->toObject()->headerColorActive(),
            "headerBackground"      => (string)$site->headerMenu()->toObject()->headerBackground(),
            "headerBackgroundActive" => (string)$site->headerMenu()->toObject()->headerBackgroundActive(),
            "headerColorCurrent"    => (string)$site->headerMenu()->toObject()->headerColorCurrent(),
            "headerColorCurrentActive" => (string)$site->headerMenu()->toObject()->headerColorCurrentActive(),
            "hamburgerFont"         => (string)$site->headerHamburger()->toObject()->hamburgerFont(),
            "hamburgerFontSize"     => (string)$site->headerHamburger()->toObject()->hamburgerFontSize(),
            "hamburgerFontColor"    => (string)$site->headerHamburger()->toObject()->hamburgerFontColor(),
            "hamburgerMenuColor"    => (string)$site->headerHamburger()->toObject()->hamburgerMenuColor(),
            "hamburgerMenuColorActive" => (string)$site->headerHamburger()->toObject()->hamburgerMenuColorActive(),
            "hamburgerOverlay"      => (string)$site->headerHamburger()->toObject()->hamburgerOverlay(),
            "hamburgerFontColorCurrent" => (string)$site->headerHamburger()->toObject()->hamburgerFontColorCurrent(),
            "hamburgerFontColorCurrentActive" => (string)$site->headerHamburger()->toObject()->hamburgerFontColorCurrentActive(),
            "logoFile"              => getLogoFile($site),
            "logoAlign"             => (string)$site->headerLogo()->toObject()->logoAlign(),
            "logoCta"               => getLogoCta($site),
            "logoDesktop"           => (string)$site->headerLogo()->toObject()->logoDesktop(),
            "logoMobile"            => (string)$site->headerLogo()->toObject()->logoMobile(),
            "logoDesktopActive"     => (string)$site->headerLogo()->toObject()->logoDesktopActive(),
            "logoMobileActive"      => (string)$site->headerLogo()->toObject()->logoMobileActive(),
            "gridGapMobile"         => (string)$site->gridGapMobile(),
            "gridMarginMobile"      => (string)$site->gridMarginMobile(),
            "gridGapDesktop"        => (string)$site->gridGapDesktop(),
            "gridMarginDesktop"     => (string)$site->gridMarginDesktop(),
            "gridBlockMobile"       => (string)$site->gridBlockMobile(),
            "gridBlockDesktop"      => (string)$site->gridBlockDesktop(),
            "buttonFont"            => (string)$site->buttonSettings()->toObject()->buttonFont(),
            "buttonFontSize"        => (string)$site->buttonSettings()->toObject()->buttonFontSize(),
            "buttonBorderRadius"    => (string)$site->buttonSettings()->toObject()->buttonBorderRadius(),
            "buttonBorderWidth"     => (string)$site->buttonSettings()->toObject()->buttonBorderWidth(),
            "buttonPadding"         => (string)$site->buttonSettings()->toObject()->buttonPadding(),
            "buttonBackgroundColor" => (string)$site->buttonColors()->toObject()->buttonBackgroundColor(),
            "buttonBackgroundColorActive" => (string)$site->buttonColors()->toObject()->buttonBackgroundColorActive(),
            "buttonTextColor"       => (string)$site->buttonColors()->toObject()->buttonTextColor(),
            "buttonTextColorActive" => (string)$site->buttonColors()->toObject()->buttonTextColorActive(),
            "buttonBorderColor"     => (string)$site->buttonColors()->toObject()->buttonBorderColor(),
            "buttonBorderColorActive" => (string)$site->buttonColors()->toObject()->buttonBorderColorActive(),
            "paginationFont"        => (string)$site->paginationSettings()->toObject()->paginationFont(),
            "paginationFontSize"    => (string)$site->paginationSettings()->toObject()->paginationFontSize(),
            "paginationBorderRadius" => (string)$site->paginationSettings()->toObject()->paginationBorderRadius(),
            "paginationBorderWidth" => (string)$site->paginationSettings()->toObject()->paginationBorderWidth(),
            "paginationPadding"     => (string)$site->paginationSettings()->toObject()->paginationPadding() ?: '10',
            "paginationMargin"      => (string)$site->paginationSettings()->toObject()->paginationMargin() ?: '10',
            "paginationElements"    => (string)$site->paginationSettings()->toObject()->paginationElements(),
            "paginationTop"         => (string)$site->paginationSettings()->toObject()->paginationTop() ?: '16',
            "paginationBottom"      => (string)$site->paginationSettings()->toObject()->paginationBottom() ?: '16',
            "paginationBackgroundColor" => (string)$site->paginationColors()->toObject()->paginationBackgroundColor(),
            "paginationBackgroundColorHover" => (string)$site->paginationColors()->toObject()->paginationBackgroundColorHover(),
            "paginationBackgroundColorActive" => (string)$site->paginationColors()->toObject()->paginationBackgroundColorActive(),
            "paginationTextColor"   => (string)$site->paginationColors()->toObject()->paginationTextColor(),
            "paginationTextColorHover" => (string)$site->paginationColors()->toObject()->paginationTextColorHover(),
            "paginationTextColorActive" => (string)$site->paginationColors()->toObject()->paginationTextColorActive(),
            "paginationBorderColor" => (string)$site->paginationColors()->toObject()->paginationBorderColor(),
            "paginationBorderColorHover" => (string)$site->paginationColors()->toObject()->paginationBorderColorHover(),
            "paginationBorderColorActive" => (string)$site->paginationColors()->toObject()->paginationBorderColorActive(),
            "searchConsoleToggle"   => $site->searchConsoleToggle()->toBool(false),
            "searchConsoleCode"     => $analytics['searchConsoleCode'],
            "googleAnalyticsToggle" => $site->googleAnalyticsToggle()->toBool(false),
            "googleAnalyticsCode"   => $analytics['googleAnalyticsCode'],
            "analyticsLink"         => $analytics['analyticsLink'],
            "claimText" => (string) $site->headerClaim()->toObject()->claimText(),
            "claimFont" => (string) $site->headerClaim()->toObject()->claimFont(),
            "claimFontSize" => (string) $site->headerClaim()->toObject()->claimFontSize(),
        ];
    }
}
