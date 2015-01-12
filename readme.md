# Lari18n (In development)
> A live internationalisation edition tool for laravel

## Missing key features

- [X] Make it work with vars in translation string
- [X] Handle the Lang::choice method
- [ ] More productivity with the Lari18n overlay
  - [X] Automatically go to the next item after a successful translation
  - [X] Show the current element differently
- [ ] Use a filter to activate the plugin for admin / translator only
- [ ] Handle new translation key added to a locale (translation upgrade)
- [ ] Create a configuration file


## Instalation

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

and this command for the views

```
  php artisan view:publish nicolasbeauvais/lari18n
```


#### Setup

You should comment (or remove) the laravel translation service provider and use lari18n instead

```
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

#### New Translation

Lari18n is packed with a artisan command to help you in the translation process. This command create a new locale directory with all the translation files ready to be translated with Lari18n.

```
  php artisan lari18n:new [from_locale] [to_locale]
```


## License
This Laravel package is open-sourced licensed under the [MIT license](http://opensource.org/licenses/MIT)
