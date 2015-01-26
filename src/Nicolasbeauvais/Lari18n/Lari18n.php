<?php

namespace Nicolasbeauvais\Lari18n;

use Config;
use File;
use Illuminate\Http\Response;
use Lang;

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
     * @var Translator
     */
    private $translator;

    /**
     * @var array
     */
    public $paths= [];

    /**
     * @var Lari18n
     */
    private static $instance = null;

    /**
     * @var string
     */
    public $todo_translation_key;

    /**
     * @var array
     */
    private $languagesList = [];

    /**
     * @var array
     */
    private $languagesData = [];

    /**
     * @var array
     */
    private $languagesProgress = [];

    /**
     * @var bool
     */
    private static $activated = false;

    /**
     * @var array
     */
    private $i18nData = [];

    /**
     * Construct lari18n.
     */
    private function __construct()
    {
        $this->app = app();
        $this->paths = [
            'js' => asset('packages/nicolasbeauvais/lari18n/js') . '/',
            'lang' => app_path('lang'),
            'backup' => storage_path('packages/nicolasbeauvais/lari18n/')
        ];
        $this->todo_translation_key = Config::get('lari18n::todo_translation_key');
    }

    /**
     * Return the singleton instance of Lari18n.
     *
     * @return Lari18n
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Lari18n();
        }

        return self::$instance;
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Wrap translated text with lari18n tags and attributes.
     *
     * @param  mixed   $trans
     * @param  string  $key
     * @param  array   $replace
     * @param  string  $locale
     * @param  int     $number
     *
     * @return mixed
     */
    public function wrap($trans, $key, $replace, $locale, $number = null)
    {
        // If an array is returned by the translation, we can't wrap it
        if (!is_string($trans)) {
            return $trans;
        }

        // Store the tag attributes
        $attributes = [];

        // If the translation start with the Lari18n key, it's a new one
        $attributes['todo'] = starts_with($trans, $this->todo_translation_key);

        // Remove the special Lari18n key of the translation
        $trans = str_replace($this->todo_translation_key, '', $trans);

        // If the translation is the key, the translation is missing
        $attributes['missing'] = $trans == $key;

        // Set Locale
        $attributes['locale'] = $locale;

        // For choice type translation we also store the given number
        $attributes['number'] = $number ?: null;

        // Add replace key/value to the tag's attributes
        $attributes['replace'] = '';
        foreach ($replace as $keyR => $valueR) {
            $attributes['replace'] = $attributes['replace'] . $keyR . ':' . $valueR . ',';
        }

        // Store fallback translation and key
        $attributes['origin'] = addslashes(Lang::get($key, $replace, Config::get('app.fallback_locale'), false));
        $attributes['key'] = $key;

        // Build attributes
        foreach ($attributes as $key => $attribute) {

            // Empty/Null/False attribute aren't used
            if (!$attributes) { continue; }

            $attributes[$key] = 'data-' . $key . '= "' . $attribute . '"';
        }

        return '<lari ' . implode($attributes, ' ') . '>' . $trans . '</lari>';
    }

    /**
     * Modify the laravel response, if needed, to add the toolbar.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Http\Response $response
     *
     * @return \Illuminate\Http\Response
     * @return mixed
     */
    public function modifyResponse($request, $response)
    {
        $app = $this->app;

        if ($app->runningInConsole() || $request->isXmlHttpRequest() || $request->wantsJson()) {
            return $response;
        } elseif (($response->headers->has('Content-Type') &&
                strpos($response->headers->get('Content-Type'), 'html') === false) || 'html' !== $request->format()) {
            return $response;
        } elseif (!($response instanceof Response)) {
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

        if ($pos !== false) {
            $content = substr($content, 0, $pos) . $renderedContent . substr($content, $pos);
        } else {
            $content = $content . $renderedContent;
        }

        $response->setContent($content);
    }

    /**
     * Get the localisation languages list and set it to a class parameter.
     *
     */
    private function setLanguagesList()
    {
        $languagesList = File::directories($this->paths['lang']);

        foreach ($languagesList as $key => $language) {
            $this->languagesList[$key] = substr(str_replace($this->paths['lang'], '', $language), 1);
        }
    }

    /**
     * Get all needed data of each localisation languages and set it to a class parameter.
     *
     * @return array
     */
    private function setLanguagesData()
    {
        $languagesData = array_flip($this->languagesList);


        // Open each file in the localisation directory and retrieve all the data of a localisation.
        foreach ($languagesData as $key => $value) {
            $files = File::allFiles($this->paths['lang'] . '/' . $key);

            $this->languagesData[$key] = [];

            foreach ($files as $fKey => $file) {
                $fileName = str_replace(['.php', '\\'], ['', '/'], $file->getRelativePathname());

                $this->languagesData[$key][$fileName] = Lang::get($fileName, [], $key);
            }

            $dot_array = array_dot($this->languagesData[$key]);
            $count = count($dot_array);

            // count the number of translated element in each file
            foreach ($dot_array as $item) {
                if (starts_with($item, $this->todo_translation_key)) {
                    $count--;
                }
            }

            $this->languagesProgress[$key] = $count;
        }
    }

    /**
     * Get all data needed for toolbar.
     *
     * @return array
     */
    public function getToolbarData()
    {
        // Initialise lari18n data.
        $this->makeI18nData();

        $data = [];

        // Get locales
        $data['locale'] = Config::get('app.locale');
        $data['fallback_locale'] = Config::get('app.fallback_locale');

        // Get language information
        $data['languages'] = $this->languagesList;
        $data['languagesData'] = $this->languagesData;
        $data['languagesProgress'] = $this->languagesProgress;

        // Get stats
        $progress = $data['languagesProgress'];
        $perc = round(($progress[$data['locale']] * 100) / $progress[$data['fallback_locale']]);
        $data['perc'] = $perc > 100 ? 100 : $perc;

        return ['data' => $data];
    }

    /**
     * Get all the data needed for an AJAX call.
     *
     * @return array
     */
    public function retrieveI18nData()
    {
        if (!empty($this->i18nData)) {
            return $this->i18nData;
        }

        // Initialise lari18n data.
        $this->makeI18nData();

        $data = [];

        $data['paths'] = $this->paths;
        $data['languages'] = $this->languagesList;
        $data['languagesData'] = $this->languagesData;
        $data['languagesProgress'] = $this->languagesProgress;

        $this->i18nData = $data;

        return $data;
    }

    /**
     * Get all the needed data for lari18n.
     */
    private function makeI18nData()
    {
        $this->setLanguagesList();
        $this->setLanguagesData();
    }

    /**
     * Reinitialise a translation file.
     *
     * @param $files
     * @param $locale
     */
    public function reinitialiseFiles($files, $locale)
    {
        foreach ($files as $file) {

            $fileName = str_replace(['.php', '\\'], ['', '/'], $file->getRelativePathname());
            $translations = Lang::get($fileName, [], $locale);

            array_walk_recursive($translations, [$this, 'reinitialiseRecursiveWalk']);
            File::put($file->getPathName(), '<?php' . "\r\n\r\n" . 'return ' . var_export($translations, true) . ';');
        }
    }

    /**
     * Recursive localisation item reinitialisation.
     *
     * @param $item
     * @param $key
     */
    private function reinitialiseRecursiveWalk(&$item, $key)
    {
        $item = $this->todo_translation_key . $item;
    }

    /**
     * Put the new translation value in the corresponding localisation file.
     *
     * @param $fallback_locale
     * @param $locale
     * @param $key
     * @param $value
     *
     * @return string
     */
    public function translate($fallback_locale, $locale, $key, $value)
    {
        // Initialise lari18n AJAX data.
        $this->retrieveI18nData();

        array_set($this->languagesData[$locale], $key, $value);

        $filePath = explode('.', $key);

        array_pop($filePath);

        $file = $this->paths['lang'] . '/' . $locale . '/' . implode('/', $filePath) . '.php';

        File::put($file, '<?php' . "\r\n\r\n" . 'return ' . var_export($this->languagesData[$locale][implode('.', $filePath)], true) . ';');
    }

    /**
     * Remove a translation value from the corresponding localisation file.
     *
     * @param $locale
     * @param $key
     *
     * @return string
     */
    public function remove($locale, $key)
    {
        // Initialise lari18n AJAX data.
        $this->retrieveI18nData();
        array_forget($this->languagesData[$locale], $key);

        $filePath = explode('.', $key);

        array_pop($filePath);

        $file = $this->paths['lang'] . '/' . $locale . '/' . implode('/', $filePath) . '.php';

        File::put($file, '<?php' . "\r\n\r\n" . 'return ' . var_export($this->languagesData[$locale][implode('.', $filePath)], true) . ';');
    }

    /**
     * Put the new choice type translation value in the corresponding localisation file.
     *
     * @param $fallback_locale
     * @param $locale
     * @param $key
     * @param $value
     * @param $number
     * @param $replace
     *
     * @return string
     */
    public function translateChoice($fallback_locale, $locale, $key, $value, $number, $replace)
    {
        $this->translate($fallback_locale, $locale, $key, $value);

        $replace = $replace ?: [];

        echo $this->translator->choice($key, $number, $replace, $locale, false);
    }

    /**
     * Return the lari18n activation state.
     *
     * @return boolean
     */
    public static function isActivated()
    {
        return self::$activated;
    }

    /**
     * Activate lari18n.
     */
    public static function activate()
    {
        self::$activated = true;
    }

    /**
     * Recursive localisation item reinitialisation.
     *
     * @param $item
     * @param $key
     */
    private function updateRecursiveWalk($item, $key)
    {
        dd ($item, $key);
    }

}
