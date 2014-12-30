
var Lari = {};

Lari.styles = [
    'lari18n'
];

Lari.init = function () {

    Lari.attach();
    $('lari').each(Lari.parse);
};

Lari.attach = function () {

    // Insert toolbar
    Lari.makeToolbar();

    // Insert stylesheets to DOM
    for (var i = 0; i < Lari.styles.length; i++) {
        Lari.loadStyle(Lari.styles[i]);
    }
};

Lari.loadStyle = function (name) {

    var style = document.createElement('link');
    style.rel = 'stylesheet';
    style.href = 'packages/nicolasbeauvais/lari18n/css/' +  name + '.css';

    $('head:first').append(style);
};

Lari.parse = function (isRemove) {
    $(this).addClass('lari');
    $(this).on('click.lari', Lari.translate);
};

Lari.translate = function () {
    console.log($(this));
};

Lari.makeToolbar = function () {

};

/**
 * Launch
 */
$(document).ready(Lari.init);
