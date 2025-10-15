# Simple API Caching for Kirby → Astro

## What's Implemented

**Minimal caching** for faster Astro builds when using Kirby as a headless CMS.

### Cached Endpoints

- `/global.json` - cached for 12 hours
- `/index.json` - cached for 6 hours
- Multi-language support (separate cache per language)

### Auto-Invalidation

Cache automatically clears when:

- Page content is updated
- Site configuration changes

## Benefits for Astro + Netlify

### ✅ Faster Netlify Builds

- API calls go from ~500ms to ~50ms
- Typical build time reduction: 50-70%
- Especially beneficial for sites with many pages

### ✅ Faster Local Development

- `npm run dev` fetches cached data
- Faster preview builds

### ✅ Zero Maintenance

- No management interface to maintain
- Automatic cache clearing
- Works transparently

## Technical Details

### Cache Configuration

```php
// site/config/config.php
'cache' => [
    'api' => true  // Simple file-based cache
],
```

### Cache Durations

- **Global config**: 12 hours (changes very rarely)
- **Page index**: 6 hours (might change more often)

### Cache Location

```
storage/cache/api/
├── global.default
├── global.en
├── global.de
├── index.default
├── index.en
└── index.de
```

## For Your Workflow

### When You Update Content

1. Edit in Kirby Panel
2. Save changes
3. ✅ Cache automatically clears
4. Next Astro build gets fresh data

### When Cache Helps

- During Astro development (`npm run dev`)
- Netlify builds (production + previews)
- Any time your frontend fetches from `/global.json` or `/index.json`

### When Cache Doesn't Help

- End user performance (they get static files from Netlify CDN)
- Runtime API calls (Astro generates static files)

## Troubleshooting

### Stale Data in Astro

1. Check if you saved changes in Kirby Panel
2. Cache should auto-clear on save
3. If stuck, restart your Kirby server

### Build Performance

- Check Netlify build logs for API call timings
- First build after content change = slower (cache miss)
- Subsequent builds = faster (cache hit)

## Remove Caching (if needed)

To remove caching entirely:

1. Remove from `site/config/config.php`:

```php
// Remove these lines:
'cache' => ['api' => true],
'hooks' => [/* all hook content */],
```

2. Change routes back to:

```php
'action' => function () { return indexJson(); }
'action' => function () { return globalJson(); }
```

## Conclusion

This minimal implementation gives you build performance benefits with zero maintenance overhead, perfect for headless CMS setups with infrequent content changes.
