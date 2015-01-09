<?php

namespace Nicolasbeauvais\Lari18n;

use Illuminate\Translation\LoaderInterface;
use Illuminate\Translation\Translator as LaravelTranslator;

/**
 * Class Translator
 * @package Nicolasbeauvais\Lari18n
 */
class Translator extends LaravelTranslator
{
    /**
     * @var Lari18n instance
     */
    private $lari18n;

    /**
     * Create a new translator instance.
     *
     * @param LoaderInterface $loader
     * @param string $locale
     * @param Lari18n $lari18n
     */
    public function __construct(LoaderInterface $loader, $locale, Lari18n $lari18n)
    {
        $this->loader = $loader;
        $this->locale = $locale;

        $this->lari18n = $lari18n;
    }

    /**
     * Get the translation for the given key.
     *
     * @param  string  $key
     * @param  array   $replace
     * @param  string  $locale
     * @param  bool    $wrap
     *
     * @return string
     */
    public function get($key, array $replace = array(), $locale = null, $wrap = true)
    {
        if (!$wrap) {
            return parent::get($key, $replace, $locale);
        }

        return $this->lari18n->wrap(parent::get($key, $replace, $locale), $key, $replace, $locale);
    }

    /**
     * Get a translation according to an integer value.
     *
     * @param  string  $key
     * @param  int     $number
     * @param  array   $replace
     * @param  string  $locale
     * @param  bool    $wrap
     *
     * @return string
     */
    public function choice($key, $number, array $replace = array(), $locale = null, $wrap = true)
    {
        $line = $this->get($key, $replace, $locale = $locale ?: $this->locale, false);

        $replace['count'] = $number;

        $choosed = $this->makeReplacements($this->getSelector()->choose($line, $number, $locale), $replace);

        if ($wrap == false) {
            return $choosed;
        } else {
            return $this->lari18n->wrap($choosed, $key, $replace, $locale, $number, $wrap);
        }
    }
}
