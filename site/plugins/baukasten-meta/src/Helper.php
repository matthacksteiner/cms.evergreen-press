<?php

namespace BaukastenMeta\Meta;

use Kirby\Cms\App;

class Helper
{
    public static function toggleFieldType(): string
    {
        static $type;

        if (! is_null($type)) {
            return $type;
        }

        if (version_compare(App::version(), '3.7.0-rc.1', '>=')) {
            return $type = 'toggles';
        }

        if (App::plugin('baukasten-meta/kirby-multi-toggle-field')) {
            return $type = 'multi-toggle';
        }

        return $type = 'select';
    }

    public static function themeColor(): ?string
    {
        return kirby()->apply('meta.theme.color', [
            'color' => option('baukastenMeta.meta.theme.color'),
        ], 'color');
    }
}
