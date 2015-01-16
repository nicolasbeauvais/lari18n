<style>
    .lari18n {
        border-color: {{ Config::get('lari18n::color-done.border') }} !important;
        background-color: {{ Config::get('lari18n::color-done.background') }} !important;
        color: {{ Config::get('lari18n::color-done.color') }} !important;
    }

    .lari18n.lari18n-missing {
        border-color: {{ Config::get('lari18n::color-missing.border') }} !important;
        background-color: {{ Config::get('lari18n::color-missing.background') }} !important;
        color: {{ Config::get('lari18n::color-missing.color') }} !important;
    }

    .lari18n.lari18n-todo {
        border-color: {{ Config::get('lari18n::color-todo.border') }} !important;
        background-color: {{ Config::get('lari18n::color-todo.background') }} !important;
        color: {{ Config::get('lari18n::color-todo.color') }} !important;
    }

    .lari18n.lari18n-selected {
        border-color: {{ Config::get('lari18n::color-selected.border') }} !important;
        background-color: {{ Config::get('lari18n::color-selected.background') }} !important;
        color: {{ Config::get('lari18n::color-selected.color') }} !important;
    }
</style>

<div id="lari-toolbar-info-toggle">L</div>

<div id="lari-toolbar-info" data-fallback_locale="{{ $data['fallback_locale'] }}" data-locale="{{ $data['locale'] }}">

    <span id="lari-toolbar-info-desactivated">
        Lari18n is ready to translate this page.
        <button id="lari-toolbar-info-activate" class="lari-button">Activate</button>
    </span>
    <span id="lari-toolbar-info-activated">

        You are translating from the fallback locale
        <span class="lari-toolbar-info-lang">[{{ ucfirst($data['fallback_locale']) }}]</span>
        to the
        <span class="lari-toolbar-info-lang">[{{ ucfirst($data['locale']) }}]</span>
        languages, and completed
        <span class="lari-toolbar-info-perc">{{ $data['perc'] }}%</span>
        of this page translations

        <button id="lari-toolbar-info-desactivate" class="lari-button">Desactivate</button>
    </span>

    <span id="lari-toolbar-info-hide">X</span>
    <a id="lari-toolbar-info-documentation" href="" target="_blank">Documentation</a>
</div>

<div id="lari-overlay">

    <div id="lari-overlay-container">

        <div id="lari-overlay-hide">
            x
        </div>

        <div id="lari-overlay-replace" class="hide">Keys:</div>

        <form id="lari-overlay-form" action="/lari18n">
            <label for="lari-overlay-form-origin">Original (Fallback locale)</label>
            <label for="lari-overlay-form-translation">Translation ({{ $data['locale'] }})</label>
            <textarea name="lari-overlay-form-origin" id="lari-overlay-form-origin"></textarea>
            <textarea name="lari-overlay-form-translation" id="lari-overlay-form-translation" ></textarea>
            <p class="lari-overlay-form-info">
                (Press enter to automatically send the translation.)
            </p>
        </form>
    </div>
</div>
