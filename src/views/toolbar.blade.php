<div id="lari-toolbar-toggle">
    L
</div>
<div id="lari-toolbar">

    <div id="lari-toolbar-info">
        <div id="lari-toolbar-info-languages">
            <span class="lari-toolbar-info-lang">{{ ucfirst($data['fallback_locale']) }}</span>
            to
            <span class="lari-toolbar-info-lang">{{ ucfirst($data['locale']) }}</span>
        </div>
        <div id="lari-toolbar-info-perc">{{ $data['perc'] }}%</div>
    </div>

    <div id="lari-toolbar-close">
        x
    </div>

    <form id="lari-toolbar-form" action="/lari18n">
        <input type="text"/>
        to
        <input type="text"/>
        <input type="submit" value="Send"/>
    </form>
</div>
