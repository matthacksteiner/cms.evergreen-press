<?php

use Kirby\Toolkit\Config;

if (!function_exists('getMeta')) {
    function getMeta($site, $page, $kirby)
    {
        $pageMeta = $page->meta();
        $owner = $site->meta_website_owner()->toString();
        $ownerId = url('/#owner');

        $json = [
            "title" => (string)$pageMeta->title(),
            "description" => $pageMeta->description()->isNotEmpty() ? (string)$pageMeta->description() : null,
            "robots" => $pageMeta->robots(),
            "canonical" => $pageMeta->canonicalUrl(),
            "separators" => (string)$site->meta_title_separator(),
            "social" => [],
        ];

        foreach ($pageMeta->social() as $tag) {
            $key = $tag['property'] ?? ($tag['name'] ?? null);
            if ($key) {
                $json['social'][$key] = $tag['content'];
            }
        }

        if ($owner === 'org') {
            $org = [
                '@type' => 'Organization',
                '@id'   => $ownerId,
                'name'  => $site->meta_org_name()->toString(),
                'url'   => $site->url(),
            ];

            if ($logo = $site->meta_org_logo()->toFile()) {
                $org['logo'] = $logo->url();
            }

            $json['org'] = $org;
        } elseif ($owner === 'person' && ($user = $site->meta_person()->toUser())) {
            $person = [
                '@type' => 'Person',
                '@id'   => $ownerId,
                'name' => $user->name()->toString(),
                'email' => $user->email(),
            ];

            if ($avatar = $user->avatar()) {
                $person['image'] = $avatar->url();
            }

            $json['person'] = $person;
        }

        return $json;
    }
}

return function ($site, $page, $kirby) {
    return [
        'json' => [
            "meta" => getMeta($site, $page, $kirby),
            'intendedTemplate' => $page->intendedTemplate()->name(),
            'uid' => (string)$page->uid(),
            'title' => (string)$page->title(),
            'lang' => (string)kirby()->languageCode(),
        ]
    ];
};
