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
     *
     * @return string
     */
    public function get($key, array $replace = array(), $locale = null)
    {
        return $this->lari18n->wrap(parent::get($key, $replace, $locale));
    }
}
