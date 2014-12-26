# Lari18n (In development)
> A live internationalisation edition tool for laravel

## Development process

- [X] Create a basic Package structure
- [ ] Extend the Lang::get Laravel method from the package
  - [ ] Add a span with a specific class around the translated text
  - [ ] Create a JavaScript script to highlight wanted translation and modify them from the browser
- [ ] Create a toolbar with translation advancement and language selection 
- [ ] Create a configuration file to use permission for translation editing

## Instalation

#### Composer

To install Lari18n as a Composer package to be used with Laravel 4, simply add this to your composer.json:

```
  "nicolasbeauvais/lari18n": "dev-master"
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



## License
This Laravel package is open-sourced licensed under the [MIT license](http://opensource.org/licenses/MIT)
