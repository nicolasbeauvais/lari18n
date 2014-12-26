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
     * @param string $key
     * @param array $replace
     * @param null $locale
     */
    public function get($key, array $replace = array(), $locale = null)
    {

    }
}
