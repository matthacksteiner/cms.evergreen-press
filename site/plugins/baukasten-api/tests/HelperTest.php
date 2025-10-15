<?php

use PHPUnit\Framework\TestCase;
use BaukastenApi\Helpers\NavigationHelper;
use BaukastenApi\Helpers\UrlHelper;
use BaukastenApi\Helpers\LanguageHelper;
use BaukastenApi\Helpers\SiteDataHelper;

/**
 * Helper function tests
 *
 * These tests focus on the core logic of helper functions with simplified mocking
 */
class HelperTest extends TestCase
{
    private $mockPage;
    private $mockSite;
    private $mockKirby;
    private $mockParent;

    protected function setUp(): void
    {
        // Create mock objects for testing
        $this->mockPage = $this->createMock(\Kirby\Cms\Page::class);
        $this->mockSite = $this->createMock(\Kirby\Cms\Site::class);
        $this->mockKirby = $this->createMock(\Kirby\Cms\App::class);
        $this->mockParent = $this->createMock(\Kirby\Cms\Page::class);
    }

    // NavigationHelper Tests

    public function testGetEffectiveParentWithNoParent()
    {
        $this->mockPage->method('parent')->willReturn(null);

        $result = NavigationHelper::getEffectiveParent($this->mockPage);

        $this->assertNull($result);
    }

    public function testGetPageNavigationWithHomePage()
    {
        $this->mockPage->method('isHomePage')->willReturn(true);
        $this->mockPage->method('parent')->willReturn(null);
        $this->mockPage->method('nextListed')->willReturn(null);
        $this->mockPage->method('prevListed')->willReturn(null);

        $result = NavigationHelper::getPageNavigation($this->mockPage);

        $this->assertIsArray($result);
        $this->assertNull($result['nextPage']);
        $this->assertNull($result['prevPage']);
    }

    // UrlHelper Tests

    public function testGeneratePageUriForHomePage()
    {
        $this->mockPage->method('isHomePage')->willReturn(true);

        $result = UrlHelper::generatePageUri($this->mockPage);

        $this->assertEquals('home', $result);
    }

    public function testGeneratePageUriWithoutRespectingToggle()
    {
        $this->mockPage->method('isHomePage')->willReturn(false);
        $this->mockPage->method('uri')->willReturn('section/page');

        // When not respecting section toggle, should use standard URI
        $result = UrlHelper::generatePageUri($this->mockPage, false);

        $this->assertEquals('section/page', $result);
    }

    // LanguageHelper Tests

    public function testGetTranslationsExcludesDefault()
    {
        // Mock default language
        $mockDefaultLang = $this->createMock(\Kirby\Cms\Language::class);
        $mockDefaultLang->method('code')->willReturn('en');

        // Mock translation language
        $mockTransLang = $this->createMock(\Kirby\Cms\Language::class);
        $mockTransLang->method('code')->willReturn('de');
        $mockTransLang->method('name')->willReturn('German');
        $mockTransLang->method('url')->willReturn('/de');
        $mockTransLang->method('locale')->willReturn('de_DE');

        // Mock current language
        $mockCurrentLang = $this->createMock(\Kirby\Cms\Language::class);
        $mockCurrentLang->method('code')->willReturn('en');

        // Mock languages collection
        $mockLanguages = $this->createMock(\Kirby\Cms\Languages::class);
        $mockLanguages->method('getIterator')->willReturn(new ArrayIterator([$mockDefaultLang, $mockTransLang]));

        $this->mockKirby->method('defaultLanguage')->willReturn($mockDefaultLang);
        $this->mockKirby->method('languages')->willReturn($mockLanguages);
        $this->mockKirby->method('language')->willReturn($mockCurrentLang);

        $result = LanguageHelper::getTranslations($this->mockKirby);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('de', $result[0]['code']);
        $this->assertEquals('German', $result[0]['name']);
        $this->assertFalse($result[0]['active']); // Current is 'en', so 'de' is not active
    }

    public function testGetAllLanguagesIncludesAll()
    {
        // Mock languages
        $mockLang1 = $this->createMock(\Kirby\Cms\Language::class);
        $mockLang1->method('code')->willReturn('en');
        $mockLang1->method('name')->willReturn('English');
        $mockLang1->method('url')->willReturn('/en');
        $mockLang1->method('locale')->willReturn('en_US');

        $mockLang2 = $this->createMock(\Kirby\Cms\Language::class);
        $mockLang2->method('code')->willReturn('de');
        $mockLang2->method('name')->willReturn('German');
        $mockLang2->method('url')->willReturn('/de');
        $mockLang2->method('locale')->willReturn('de_DE');

        // Mock current language
        $mockCurrentLang = $this->createMock(\Kirby\Cms\Language::class);
        $mockCurrentLang->method('code')->willReturn('en');

        // Mock languages collection
        $mockLanguages = $this->createMock(\Kirby\Cms\Languages::class);
        $mockLanguages->method('getIterator')->willReturn(new ArrayIterator([$mockLang1, $mockLang2]));

        $this->mockKirby->method('languages')->willReturn($mockLanguages);
        $this->mockKirby->method('language')->willReturn($mockCurrentLang);

        $result = LanguageHelper::getAllLanguages($this->mockKirby);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('en', $result[0]['code']);
        $this->assertEquals('de', $result[1]['code']);
    }

    public function testGetDefaultLanguage()
    {
        // Mock default language
        $mockDefaultLang = $this->createMock(\Kirby\Cms\Language::class);
        $mockDefaultLang->method('code')->willReturn('en');
        $mockDefaultLang->method('name')->willReturn('English');
        $mockDefaultLang->method('url')->willReturn('/en');
        $mockDefaultLang->method('locale')->willReturn('en_US');

        // Mock current language
        $mockCurrentLang = $this->createMock(\Kirby\Cms\Language::class);
        $mockCurrentLang->method('code')->willReturn('en');

        $this->mockKirby->method('defaultLanguage')->willReturn($mockDefaultLang);
        $this->mockKirby->method('language')->willReturn($mockCurrentLang);

        $result = LanguageHelper::getDefaultLanguage($this->mockKirby);

        $this->assertIsArray($result);
        $this->assertEquals('en', $result['code']);
        $this->assertEquals('English', $result['name']);
        $this->assertTrue($result['active']);
    }

    public function testGetDefaultLanguageWithPrefixOption()
    {
        // Mock default language
        $mockDefaultLang = $this->createMock(\Kirby\Cms\Language::class);
        $mockDefaultLang->method('code')->willReturn('en');
        $mockDefaultLang->method('name')->willReturn('English');
        $mockDefaultLang->method('url')->willReturn('/en/home');
        $mockDefaultLang->method('locale')->willReturn('en_US');

        // Mock current language
        $mockCurrentLang = $this->createMock(\Kirby\Cms\Language::class);
        $mockCurrentLang->method('code')->willReturn('en');

        $this->mockKirby->method('defaultLanguage')->willReturn($mockDefaultLang);
        $this->mockKirby->method('language')->willReturn($mockCurrentLang);

        $result = LanguageHelper::getDefaultLanguage($this->mockKirby);

        $this->assertIsArray($result);
        $this->assertEquals('en', $result['code']);
        $this->assertEquals('English', $result['name']);
        // URL should be processed to remove language prefix when prefixDefaultLocale is false
        $this->assertEquals('/home', $result['url']);
    }

    // Basic structure tests for SiteDataHelper

    public function testSiteDataHelperMethodsExist()
    {
        // Test that all expected methods exist on the SiteDataHelper class
        $this->assertTrue(method_exists(SiteDataHelper::class, 'getFavicon'));
        $this->assertTrue(method_exists(SiteDataHelper::class, 'getNavigation'));
        $this->assertTrue(method_exists(SiteDataHelper::class, 'getLogoFile'));
        $this->assertTrue(method_exists(SiteDataHelper::class, 'getLogoCta'));
        $this->assertTrue(method_exists(SiteDataHelper::class, 'getFonts'));
        $this->assertTrue(method_exists(SiteDataHelper::class, 'getFontSizes'));
        $this->assertTrue(method_exists(SiteDataHelper::class, 'getHeadlines'));
        $this->assertTrue(method_exists(SiteDataHelper::class, 'getAnalytics'));
    }

    public function testNavigationHelperMethodsExist()
    {
        // Test that all expected methods exist on the NavigationHelper class
        $this->assertTrue(method_exists(NavigationHelper::class, 'getPageNavigation'));
        $this->assertTrue(method_exists(NavigationHelper::class, 'getNavigationSiblings'));
        $this->assertTrue(method_exists(NavigationHelper::class, 'getEffectiveParent'));
    }

    public function testUrlHelperMethodsExist()
    {
        // Test that all expected methods exist on the UrlHelper class
        $this->assertTrue(method_exists(UrlHelper::class, 'getSectionToggleState'));
        $this->assertTrue(method_exists(UrlHelper::class, 'generatePageUri'));
    }

    public function testLanguageHelperMethodsExist()
    {
        // Test that all expected methods exist on the LanguageHelper class
        $this->assertTrue(method_exists(LanguageHelper::class, 'getTranslations'));
        $this->assertTrue(method_exists(LanguageHelper::class, 'getAllLanguages'));
        $this->assertTrue(method_exists(LanguageHelper::class, 'getDefaultLanguage'));
    }
}
