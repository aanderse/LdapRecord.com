<?php

namespace App\Http\Controllers;

use App\Documentation;
use Symfony\Component\DomCrawler\Crawler;

class DocsController extends Controller
{
    /**
     * The documentation repository.
     *
     * @var Documentation
     */
    protected $docs;

    /**
     * Constructor.
     *
     * @param Documentation $docs
     *
     * @return void
     */
    public function __construct(Documentation $docs)
    {
        $this->docs = $docs;
    }

    /**
     * Show the root documentation page (/docs).
     *
     * @return \Illuminate\Http\Response
     */
    public function showRootPage()
    {
        return redirect($this->docs::getRootUrl());
    }

    /**
     * Show a documentation page.
     *
     * @param string      $version
     * @param string|null $page
     *
     * @return \Illuminate\Http\Response
     */
    public function show($version, $page = null)
    {
        $default = $this->getDefaultVersion();

        if (! $this->isVersion($version)) {
            return redirect('docs/'.$default, 301);
        }

        if (! defined('CURRENT_VERSION')) {
            define('CURRENT_VERSION', $version);
        }

        $sectionPage = $page ?: 'readme';
        $content = $this->docs->get($version, $sectionPage);

        if (is_null($content)) {
            return response()->view('docs', [
                'title' => 'Page not found',
                'index' => $this->docs->getIndex($version),
                'content' => view('partials.doc-missing'),
                'currentVersion' => $version,
                'currentSection' => '',
                'canonical' => null,
            ], 404);
        }

        $title = (new Crawler($content))->filterXPath('//h1');

        $section = '';

        if ($this->docs->sectionExists($version, $page)) {
            $section .= '/'.$page;
        } elseif (! is_null($page)) {
            return redirect('/docs/'.$version);
        }

        $canonical = null;

        if ($this->docs->sectionExists($default, $sectionPage)) {
            $canonical = 'docs/'.$default.'/'.$sectionPage;
        }

        return view('docs', [
            'title' => count($title) ? $title->text() : null,
            'index' => $this->docs->getIndex($version),
            'content' => $content,
            'currentVersion' => $version,
            'currentSection' => $section,
            'canonical' => $canonical,
        ]);
    }

    /**
     * Returns the documentations default version.
     *
     * @return string
     */
    protected function getDefaultVersion()
    {
        return DEFAULT_VERSION;
    }

    /**
     * Returns the available documentation versions.
     *
     * @return array
     */
    protected function getDocVersions()
    {
        return Documentation::getDocVersions();
    }

    /**
     * Determine if the given URL segment is a valid version.
     *
     * @param string $version
     *
     * @return bool
     */
    protected function isVersion($version)
    {
        return array_key_exists($version, $this->getDocVersions());
    }
}
