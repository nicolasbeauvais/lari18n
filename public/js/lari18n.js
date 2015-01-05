var Lari = {};

// Css styles dependencies to lazy load
Lari.styles = [
    'lari18n'
];

// Data for the package
Lari.data = {};

/**
 * Init the lari18n Package
 */
Lari.init = function () {

    Lari.dom.init();

    // Insert stylesheets to DOM
    for (var i = 0; i < Lari.styles.length; i++) {
        Lari.loadStyle(Lari.styles[i]);
    }
};

Lari.activate = function () {
    Lari.toolbar.activate();
    Lari.parse(true);
};

Lari.desactivate = function () {
    Lari.toolbar.desactivate();
    Lari.parse(false);
};

Lari.loadStyle = function (name) {

    var style = document.createElement('link');
    style.rel = 'stylesheet';
    style.href = 'packages/nicolasbeauvais/lari18n/css/' +  name + '.css';

    $('head:first').append(style);
};

Lari.parse = function (isActivate) {

    $('lari').each(function () {

        if (isActivate) {

            $(this).addClass('lari18n');
            if ($(this).data('todo')) { $(this).addClass('lari18n-todo'); }
            if ($(this).data('missing')) { $(this).addClass('lari18n-missing'); }
            $(this).bind('click.lari', Lari.translate);

        } else {

            $(this).removeClass('lari18n');
            $(this).removeClass('lari18n-todo');
            $(this).removeClass('lari18n-missing');

            $(this).unbind('click.lari', Lari.translate);
        }

    });
};

Lari.translate = function (e) {

    e.preventDefault();
    e.stopPropagation();

    Lari.$current = $(this);

    $('#lari-overlay').show();

    var top = $(this).offset().top;
    if (top > $('#lari-overlay').outerHeight() + 100) {
        top = top - $('#lari-overlay').outerHeight() - 30;
    } else {
        top += $(this).outerHeight() + 30;
    }

    $('#lari-overlay').css({top: top});

    $('#lari-overlay-form-origin').val($(this).data('origin'));
};

Lari.sendTranslate = function (e) {

    if (e.keyCode == 13) {

        if (!Lari.$current) { return false; }

        e.preventDefault();

        var data = Lari.data;

        data.key = Lari.$current.data('key');
        data.value = $(this).val();

        $.post('/lari18n/translate', data);

        // Update the changed tag
        Lari.$current.removeClass('lari18n-missing lari18n-todo').text($(this).val());

        return false;
    }
};

/**
 * ====================
 * ================ DOM
 * ====================
 */
Lari.dom = {};

/**
 * Init the dom object.
 */
Lari.dom.init = function () {

    Lari.dom.get();
};

/**
 * Ajax query to get the package DOM.
 */
Lari.dom.get = function () {

    // Insert toolbar
    $.get('/lari18n/dom').success(Lari.dom.loaded);
};

/**
 * Ajax callback of the get dom request.
 *
 * @param data
 */
Lari.dom.loaded = function (data) {

    // Attach the toolbar to the DOM
    $('body:first').prepend(data);

    // Add toolbar specific listeners
    Lari.toolbar.init();

    Lari.overlay.init();

    // Init data
    Lari.data = $('#lari-toolbar-info').data();
};


/**
 * ====================
 * =========== TOOLBAR
 * ====================
 */
Lari.toolbar = {};

/**
 * Init the toolbar
 */
Lari.toolbar.init = function () {
    $('#lari-toolbar-info-activate').bind('click.lari', Lari.activate);
    $('#lari-toolbar-info-desactivate').bind('click.lari', Lari.desactivate);
    $('#lari-toolbar-info-hide, #lari-toolbar-info-toggle').bind('click.lari', Lari.toolbar.toggle);
};

/**
 * Put the toolbar to the activated state
 */
Lari.toolbar.activate = function () {
    $('#lari-toolbar-info-desactivated').hide();
    $('#lari-toolbar-info-activated').show();
};

/**
 * Put the toolbar to the desactivated state
 */
Lari.toolbar.desactivate = function () {
    $('#lari-toolbar-info-desactivated').show();
    $('#lari-toolbar-info-activated').hide();
};

/**
 * Toggle the toolbar visibility
 */
Lari.toolbar.toggle = function () {
    $('#lari-toolbar-info').toggle();
    $('#lari-toolbar-info-toggle').toggle();
};



/**
 * ====================
 * =========== OVERLAY
 * ====================
 */
Lari.overlay = {};

// Contain the DOM element currently edited by the overlay
Lari.overlay.$current = null;

/**
 * Init the overlay
 */
Lari.overlay.init = function () {
    $('#lari-overlay-hide').bind('click.lari', Lari.overlay.hide);
    $('#lari-overlay-form-translation').on("keypress", Lari.sendTranslate);
};


Lari.overlay.hide = function () {
    Lari.$current = null;
    $('#lari-overlay').hide();
};

/**
 * ====================
 * =========== LAUNCH
 * ====================
 */
$(document).ready(Lari.init);

