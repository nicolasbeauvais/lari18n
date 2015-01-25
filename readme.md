# Lari18n (Beta)
> Translation made easy

## Demo

###### Lari18n Toolbar
![toolbar-demo](https://cloud.githubusercontent.com/assets/2951704/5892985/9524f12c-a4d4-11e4-89ba-d909b1cb0bc1.png)

###### Lari18n translation overlay
![overlay-demo](https://cloud.githubusercontent.com/assets/2951704/5892986/9527a8a4-a4d4-11e4-9bcc-8b7bbdab4088.png)

## Installation

#### Composer

To install Lari18n as a Composer package to be used with Laravel 4, simply add this to your composer.json:

```
  "nicolasbeauvais/lari18n": "dev-master"
```

### Publish

To let your app use the front end ressource of Lari18n you need to publish them to your app using this artisan command for the assets

```
  php artisan asset:publish nicolasbeauvais/lari18n
```

use this command for the views

```
  php artisan view:publish nicolasbeauvais/lari18n
```

and this command for the configuration file

```
  php artisan config:publish nicolasbeauvais/lari18n
```



#### Setup

You should comment (or remove) the laravel translation service provider and use lari18n instead

```php
  'providers' => array(
      // ...
      //'Illuminate\Translation\TranslationServiceProvider',
      // ...
      'Nicolasbeauvais\Lari18n\Lari18nServiceProvider'
  ),
```
Now, every time you use `trans()` or `Lang::get()` lari18n will be able to do some magic work.

## Using Lari18n

Lari18n help you to archieve a new translation process, for that you must use the native [laravel localization system](http://laravel.com/docs/4.2/localization), and your app base language (falback_locale) translation files must be filed.

To activate Lari18n you can use the activate method. For example in a filter for a specific role of your application.
```php
\Nicolasbeauvais\Lari18n\Lari18n::activate();
```

#### New Translation

Lari18n is packed with a artisan command to help you in the translation process. This command create a new locale directory with all the translation files ready to be translated with Lari18n.

```
  php artisan lari18n:new [from_locale] [to_locale]
```

### Translation changes

You can watch your translations directory using gulp or grunt and apply the update command on file change

```
  php artisan lari18n:update
```

This command will update all other locales files with the additions made on the fallback locale files.

If you also want to remove the entry that doesn't exist in your fallback locale you can use the `remove` option

```
  php artisan lari18n:update --remove
```

## License
This Laravel package is open-sourced licensed under the [MIT license](http://opensource.org/licenses/MIT)
