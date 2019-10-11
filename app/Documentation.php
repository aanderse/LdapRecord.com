<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Cache\Repository as Cache;

class Documentation
{
    /**
     * The filesystem implementation.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The cache implementation.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Create a new documentation instance.
     *
     * @param Filesystem  $files
     * @param Cache       $cache
     *
     * @return void
     */
    public function __construct(Filesystem $files, Cache $cache)
    {
        $this->files = $files;
        $this->cache = $cache;
    }

    /**
     * Get the documentation index page.
     *
     * @param string $version
     *
     * @return string
     */
    public function getIndex($version)
    {
        return $this->cache->remember($this->getIndexCacheKey($version), 5, function () use ($version) {
            $path = $this->getIndexBasePath($version);

            if ($this->files->exists($path)) {
                return $this->replaceLinks($version, markdown($this->files->get($path)));
            }

            return null;
        });
    }

    /**
     * Get the given documentation page.
     *
     * @param string $version
     * @param string $page
     *
     * @return string
     */
    public function get($version, $page)
    {
        return $this->cache->remember($this->getPageCacheKey($version, $page), 5, function () use ($version, $page) {
            $path = $this->getPageBasePath($version, $page);

            if ($this->files->exists($path)) {
                return $this->replaceLinks($version, markdown($this->files->get($path)));
            }

            return null;
        });
    }

    /**
     * Replace the version place-holder in links.
     *
     * @param string $version
     * @param string $content
     *
     * @return string
     */
    public static function replaceLinks($version, $content)
    {
        return str_replace('{{version}}', $version, $content);
    }

    /**
     * Check if the given section exists.
     *
     * @param string $version
     * @param string $page
     *
     * @return boolean
     */
    public function sectionExists($version, $page)
    {
        return $this->files->exists(
            $this->getPageBasePath($version, $page)
        );
    }

    /**
     * Returns the documentations root URL.
     *
     * @return string
     */
    public static function getRootUrl()
    {
        return '/docs/'.DEFAULT_VERSION;
    }

    /**
     * Returns the repositories page URL.
     *
     * @param string      $version
     * @param string|null $page
     *
     * @return string
     */
    public static function getPageUrl($version, $page = null)
    {
        return route('page', $version, $page);
    }

    /**
     * Get the publicly available versions of the documentation
     *
     * @return array
     */
    public static function getDocVersions()
    {
        return [
            'master' => 'Master',
        ];
    }

    /**
     * Returns the full path of the documentation index markdown file.
     *
     * @param string $version
     *
     * @return string
     */
    protected function getIndexBasePath($version)
    {
        return $this->getDocsPath($version.'/documentation.md');
    }

    /**
     * Returns the full path of the given documentation markdown file.
     *
     * @param string $version
     * @param string $page
     *
     * @return string
     */
    protected function getPageBasePath($version, $page)
    {
        return $this->getDocsPath($version.'/'.$page.'.md');
    }

    /**
     * Returns the page cache key.
     *
     * @param string $version
     * @param string $page
     *
     * @return string
     */
    protected function getPageCacheKey($version, $page)
    {
        return 'docs.'.$version.'.'.Str::slug($page);
    }

    /**
     * Returns the documentation index cache key.
     *
     * @param string $version
     *
     * @return string
     */
    protected function getIndexCacheKey($version)
    {
        return 'docs.'.$version.'.index';
    }

    /**
     * Returns the documentation full path and appends the given path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function getDocsPath($path)
    {
        return base_path('resources/docs/'.$path);
    }
}
