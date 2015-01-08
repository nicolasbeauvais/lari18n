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
     * @var string
     */
    private $lang_key = "%lari18n-TODO%";

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
     * Wrap translated text with lari18n tags.
     *
     * @param  mixed   $trans
     * @param  string  $key
     * @param  array   $replace
     * @param  string  $locale
     *
     * @return mixed
     */
    public function wrap($trans, $key, $replace, $locale, $number = null)
    {

        if (!is_string($trans)) {
            return $trans;
        }

        $attributes = array();

        if (starts_with($trans, $this->lang_key)) {

            $attributes['todo'] = true;
            $trans = str_replace($this->lang_key, '', $trans);
        }

        if ($trans == $key) {
            $attributes['missing'] = true;
        }

        if (!empty($replace)) {

            $attributes['replace'] = '';

            foreach ($replace as $keyR => $valueR) {
                $attributes['replace'] .= $keyR . ':' . $valueR . ',';
            }
        }

        $attributes['origin'] = addslashes(Lang::get($key, $replace, Config::get('app.fallback_locale'), false));
        $attributes['key'] = $key;

        if ($number) {
            $attributes['number'] = $number;
        }

        // Build attributes
        foreach ($attributes as $key => $attribute) {
            $attributes[$key] = 'data-' . $key . '= "' . $attribute . '"';
        }

        return '<lari ' . implode($attributes, ' ') . '>' . $trans . '</lari>';
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
                $fileName = str_replace(array('.php', '\\'), array('', '/'), $file->getRelativePathname());

                $languagesData[$key][$fileName] = Lang::get($fileName, array(), $key);
            }

            $dot_array = array_dot($languagesData[$key]);
            $count = count($dot_array);

            foreach ($dot_array as $item) {

                if (starts_with($item, $this->lang_key)) {
                    $count--;
                }
            }

            $this->languagesProgress[$key] = $count;
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

        $data['perc'] = round(($data['languagesProgress'][$data['locale']] * 100) /
            $data['languagesProgress'][$data['fallback_locale']]);

        return array('data' => $data);
    }

    private function makeI18nData()
    {
        $this->setLanguagesList();
        $this->setLanguagesData();
    }

    public function retrieveI18nData()
    {
        $this->makeI18nData();

        $data = array();

        $data['paths'] = $this->paths;
        $data['languages'] = $this->languagesList;
        $data['languagesData'] = $this->languagesData;
        $data['languagesProgress'] = $this->languagesProgress;

        return $data;
    }

    /**
     * @param $files
     * @param $locale
     */
    public function reinitialiseFiles($files, $locale)
    {
        foreach ($files as $file) {

            $fileName = str_replace(array('.php', '\\'), array('', '/'), $file->getRelativePathname());
            $translations = Lang::get($fileName, array(), $locale);

            array_walk_recursive($translations, array($this, 'reinitialiseRecursiveWalk'));
            File::put($file->getPathName(), '<?php' . "\r\n\r\n" . 'return ' . var_export($translations, true) . ';');
        }
    }

    /**
     * @param $item
     * @param $key
     */
    private function reinitialiseRecursiveWalk(&$item, $key)
    {
        $item = $this->lang_key . $item;
    }

    /**
     * @param $fallback_locale
     * @param $locale
     * @param $key
     * @param $value
     *
     * @return string
     */
    public function translate($fallback_locale, $locale, $key, $value)
    {
        $this->retrieveI18nData();

        array_set($this->languagesData[$locale], $key, $value);

        $filePath = explode('.', $key);

        array_pop($filePath);

        $file = $this->paths['lang'] . '/' . $locale . '/' . implode('/', $filePath) . '.php';

        File::put($file, '<?php' . "\r\n\r\n" . 'return ' . var_export($this->languagesData[$locale][implode('.', $filePath)], true) . ';');
    }

    /**
     * @param $fallback_locale
     * @param $locale
     * @param $key
     * @param $value
     * @param $number
     * @param $replace
     *
     * @return string
     */
    public function translateChoice($fallback_locale, $locale, $key, $value, $number, $replace = array())
    {
        $this->translate($fallback_locale, $locale, $key, $value);

        echo trans_choice($key, $number, $replace);
    }
}
