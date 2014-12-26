<?php

namespace Nicolasbeauvais\Lari18n;

use Illuminate\Translation\Translator as LaravelTranslator;

/**
 * Class Translator
 * @package Nicolasbeauvais\Lari18n
 */
class Translator extends LaravelTranslator
{
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
        return parent::get($key, $replace, $locale);
    }
}
