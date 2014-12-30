<?php

namespace Nicolasbeauvais\Lari18n;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;

/**
 * Class Lari18n
 * @package Nicolasbeauvais\Lari18n
 */
class Lari18n
{

    /**
     * @var mixed
     */
    private $app;

    /**
     * @var array
     */
    private $paths= array();

    /**
     * @var Lari18n
     */
    private $instance;

    /**
     * @var array
     */
    private $languagesList = array();

    /**
     * @var array
     */
    private $languagesData = array();

    /**
     * @var array
     */
    private $languagesProgress = array();

    /**
     * Construct.
     */
    private function __construct()
    {
        $this->app = app();
        $this->paths['js'] = asset('packages/nicolasbeauvais/lari18n/js') . '/';
        $this->paths['lang'] = app_path() . '/lang';
    }

    /**
     * Return the singleton instance of Lari18n.
     *
     * @return Lari18n
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {

            $instance = new Lari18n();
        }

        return $instance;
    }

    /**
     * Wrap translated text with lai18n tags.
     *
     * @param mixed $trans
     * @return mixed
     */
    public function wrap($trans)
    {
        if (!is_string($trans)) {
            return $trans;
        }

        return '<lari>' . $trans . '</lari>';
    }

    /**
     * Modify the laravel response if needed to add th toolbar.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Symfony\Component\HttpFoundation\Response

     * @return mixed
     */
    public function modifyResponse($request, $response)
    {
        $app = $this->app;

        if ($app->runningInConsole()) {
            return $response;
        }

        if ($request->isXmlHttpRequest() || $request->wantsJson()) {

            return $response;

        } elseif (($response->headers->has('Content-Type') &&
                strpos($response->headers->get('Content-Type'), 'html') === false) || 'html' !== $request->format()) {

            return $response;

        }

        $this->injectToolbar($response);

        return $response;
    }

    /**
     * Attach the toolbar dom to the laravel answer.
     *
     * @param Response $response
     */
    public function injectToolbar(Response $response)
    {
        $content = $response->getContent();

        $renderedContent = '<script type="text/javascript" src="' . $this->paths['js'] . 'lari18n.js'  . '"></script>';

        $pos = strripos($content, '</body>');
        if (false !== $pos) {
            $content = substr($content, 0, $pos) . $renderedContent . substr($content, $pos);
        } else {
            $content = $content . $renderedContent;
        }
        $response->setContent($content);
    }

    private function setLanguagesList()
    {
        $languagesList = File::directories($this->paths['lang']);

        foreach ($languagesList as $key => $language) {
            $languagesList[$key] = substr(str_replace($this->paths['lang'], '', $language), 1);
        }

        $this->languagesList = $languagesList;
    }

    /**
     * @return array
     */
    private function setLanguagesData()
    {
        $languagesData = array_flip($this->languagesList);

        foreach ($languagesData as $key => $value) {
            $files = File::allFiles($this->paths['lang'] . '/' . $key);

            $languagesData[$key] = array();

            foreach ($files as $fKey => $file) {
                $fileName = str_replace(array('.php', '/', '\\'), array('', '.', '.'), $file->getRelativePathname());

                $languagesData[$key][$fileName] = Lang::get($fileName);
            }

            $this->languagesProgress[$key] = count($languagesData[$key], COUNT_RECURSIVE);
        }

        $this->languagesData = $languagesData;
    }

    /**
     * Get all data needed for toolbar.
     */
    public function getToolbarData()
    {
        $this->makeI18nData();

        $data = array();

        // Get locales
        $data['locale'] = Config::get('app.locale');
        $data['fallback_locale'] = Config::get('app.fallback_locale');


        $data['languages'] = $this->languagesList;
        $data['languagesData'] = $this->languagesData;
        $data['languagesProgress'] = $this->languagesProgress;

        $data['perc'] = ($data['languagesProgress'][$data['locale']] * 100) /
            $data['languagesProgress'][$data['fallback_locale']];

        return array('data' => $data);
    }

    private function makeI18nData()
    {
        $this->setLanguagesList();
        $this->setLanguagesData();
    }
}
