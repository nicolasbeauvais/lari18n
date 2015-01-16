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

/**
 * Activate Lari18n.
 */
Lari.activate = function () {
    Lari.toolbar.activate();
    Lari.parse(true);
};

/**
 * Desactivate Lari18n.
 */
Lari.desactivate = function () {
    Lari.toolbar.desactivate();
    Lari.overlay.desactivate();
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

            if ($(this).text().length == 0) {
                $(this).addClass('lari18n-empty');
                $(this).text('%empty-value%')
            }

            $(this).addClass('lari18n');
            if ($(this).data('todo')) { $(this).addClass('lari18n-todo'); }
            if ($(this).data('missing')) { $(this).addClass('lari18n-missing'); }
            $(this).bind('click.lari', Lari.overlay.translate);

        } else {

            if ($(this).hasClass('lari18n-empty')) {
                $(this).text('');
            }

            $(this).removeClass('lari18n');
            $(this).removeClass('lari18n-todo');
            $(this).removeClass('lari18n-missing');
            $(this).removeClass('lari18n-empty');


            $(this).unbind('click.lari', Lari.overlay.translate);
        }

    });
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
    $('#lari-overlay-form-translation').bind('keypress.lari', Lari.overlay.sendTranslate);
};

/**
 * Send a translation to the backend.
 *
 * @param e
 *
 * @returns {boolean}
 */
Lari.overlay.sendTranslate = function (e) {

    if (e.keyCode == 13) {

        if (!Lari.overlay.$current) { return false; }

        if ($(this).val().length == 0 && !confirm('Are you sure to translate this to an empty value ?')) {
            return false;
        }

        e.preventDefault();

        var data = Lari.data;

        data.key = Lari.overlay.$current.data('key');
        data.value = $(this).val();
        data.number = Lari.overlay.$current.data('number');

        // Choice translation case
        if (data.number) {
            var replace = Lari.overlay.$current.data('replace');

            if (replace) {
                replace = replace.split(',');
                data.replace = [];
                for (var i = 0; i < replace.length; i++) {
                    var items = replace[i];

                    if (!items) {
                        continue;
                    }

                    items = items.split(':');
                    data.replace[items[0]] = items[1];
                }
            }

            $.post('/lari18n/translate-choice', data).success(Lari.overlay.callbackChoice);

            return;
        }

        $.post('/lari18n/translate', data);

        // Update the changed tag
        var text = $(this).val();

        var replace = Lari.overlay.$current.data('replace');

        if (replace) {
            replace = replace.split(',');

            for (var i = 0; i < replace.length; i++) {
                var items = replace[i];

                if (!items) {
                    continue;
                }

                items = items.split(':');

                text = text.replace(new RegExp('\:' + items[0], 'g'), items[1]);
            }
        }

        Lari.overlay.$current.removeClass('lari18n-missing lari18n-todo').text(text);

        $('.lari18n-missing:first,.lari18n-todo:first').click();

        return false;
    }
};

Lari.overlay.callbackChoice = function (data) {
    Lari.overlay.$current.text(data);
    $('.lari18n-missing:first,.lari18n-todo:first').click();
};

/**
 * Start a translation process in the overlay.
 *
 * @param e
 */
Lari.overlay.translate = function (e) {

    e.preventDefault();
    e.stopPropagation();

    Lari.overlay.$current = $(this);
    $('.lari18n-selected').removeClass('lari18n-selected');
    Lari.overlay.$current.addClass('lari18n-selected');

    // @TODO: put that in a overlay translate init method
    $('#lari-overlay-replace').find('.lari-overlay-replace').remove();
    $('#lari-overlay-replace').addClass('hide');
    $('#lari-overlay').show();
    $('#lari-overlay-form-translation').val('');

    var top = $(this).offset().top + 65;

    if (top > $('#lari-overlay').outerHeight() + 150) {
        top = top - $('#lari-overlay').outerHeight() - 100;
    } else {
        top += $(this).outerHeight() + 30;
    }

    $('#lari-overlay').css({top: top});

    // Replace vars
    var replace = $(this).data('replace');

    var replace = Lari.overlay.$current.data('replace');

    if (replace) {
        replace = replace.split(',');
        $('#lari-overlay-replace').removeClass('hide');

        for (var i = 0; i < replace.length; i++) {
            var items = replace[i];

            if (!items) { continue; }

            items = items.split(':');

            $('#lari-overlay-replace').append('<span class="lari-overlay-replace">:' + items[0]+ ' => ' + items[1] + '</span>');
        }
    }


    $('#lari-overlay-form-origin').val($(this).data('origin'));
};

/**
 * Hide the overlay.
 */
Lari.overlay.hide = function () {
    Lari.overlay.$current = null;
    $('#lari-overlay').hide();
};

/**
 * Desactivate the overlay.
 */
Lari.overlay.desactivate = function () {

    Lari.overlay.hide();

    $('#lari-overlay-hide').unbind('click.lari', Lari.overlay.hide);
    $('#lari-overlay-form-translation').unbind('keypress.lari', Lari.overlay.sendTranslate);
};



/**
 * ====================
 * =========== LAUNCH
 * ====================
 */
$(document).ready(Lari.init);
