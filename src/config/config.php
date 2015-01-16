<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Lari18n not translated yet
    |--------------------------------------------------------------------------
    |
    | That key is used to indicate lari18n that a localisation element hasn't
    | been translated yet, even if it already has content.
    | When using the lari18n:new command, lari18n copy the localisation files and
    | put that key before every copied fallback_local translation to know they
    | have to be done.
    |
    */

    'todo_translation_key' => '%lari18n-TODO%',

    /*
    |--------------------------------------------------------------------------
    | Lari18n translated element colors
    |--------------------------------------------------------------------------
    |
    | CSS color of a translated element. (default green #2ccc35)
    |
    */

    'color-done' => array(
        'border' => '#2ccc35',
        'color' => '#2ccc35',
        'background' => '#ffffff'
    ),

    /*
    |--------------------------------------------------------------------------
    | Lari18n missing element colors
    |--------------------------------------------------------------------------
    |
    | CSS color of a missing element. (default orange #f66e27)
    |
    */

    'color-missing' => array(
        'border' => '#f66e27',
        'color' => '#f66e27',
        'background' => '#ffffff'
    ),

    /*
    |--------------------------------------------------------------------------
    | Lari18n not translated element colors
    |--------------------------------------------------------------------------
    |
    | CSS color of a not translated element. (default red #cc3837)
    |
    */

    'color-todo' => array(
        'border' => '#cc3837',
        'color' => '#cc3837',
        'background' => '#ffffff'
    ),

    /*
    |--------------------------------------------------------------------------
    | Lari18n selected element colors
    |--------------------------------------------------------------------------
    |
    | CSS color of a selected element. (default black #000000)
    |
    */

    'color-selected' => array(
        'border' => '#000000',
        'color' => '#000000',
        'background' => '#ffffff'
    ),
);
