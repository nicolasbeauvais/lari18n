<?php namespace Nicolasbeauvais\Lari18n;

use Illuminate\Support\Facades\Facade;

class Lari18nFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'lari18n'; }

}
