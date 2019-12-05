# FastCGI Cache Purge for Bludit
This plugin uses `ngx_cache_purge` to purge cached pages by GETting the modified/deleted post using the given format.
You can use `@siteurl` for the site URL and `@posturl` for the post URL.
Default is `@siteurl/purge/@posturl`.