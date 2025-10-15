<?php

namespace BaukastenApi\Helpers;

/**
 * LanguageHelper class containing language and translation functions
 */
class LanguageHelper
{
    /**
     * Returns an array of languages excluding the default language.
     *
     * @param \Kirby\Cms\App $kirby The Kirby instance
     * @return array Array of translation languages
     */
    public static function getTranslations($kirby)
    {
        $default = $kirby->defaultLanguage();
        $translations = [];
        foreach ($kirby->languages() as $language) {
            if ($language->code() !== $default->code()) {
                $translations[] = [
                    "code"   => $language->code(),
                    "name"   => $language->name(),
                    "url"    => $language->url(),
                    "locale" => $language->locale(LC_ALL),
                    "active" => $language->code() === $kirby->language()->code(),
                ];
            }
        }
        return $translations;
    }

    /**
     * Returns an array of all languages.
     *
     * @param \Kirby\Cms\App $kirby The Kirby instance
     * @return array Array of all languages
     */
    public static function getAllLanguages($kirby)
    {
        $all = [];
        foreach ($kirby->languages() as $language) {
            $all[] = [
                "code"   => $language->code(),
                "name"   => $language->name(),
                "url"    => $language->url(),
                "locale" => $language->locale(LC_ALL),
                "active" => $language->code() === $kirby->language()->code(),
            ];
        }
        return $all;
    }

    /**
     * Returns the default language information.
     *
     * @param \Kirby\Cms\App $kirby The Kirby instance
     * @return array Default language information
     */
    public static function getDefaultLanguage($kirby)
    {
        $default = $kirby->defaultLanguage();
        return [
            "code"   => $default->code(),
            "name"   => $default->name(),
            "url"    => option('prefixDefaultLocale')
                ? $default->url()
                : str_replace('/' . $default->code(), '', $default->url()),
            "locale" => $default->locale(LC_ALL),
            "active" => $default->code() === $kirby->language()->code(),
        ];
    }
}
