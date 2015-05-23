<section id="settings">
    <section class="item_header">
       <h1 id="settings_language_title">{languageTitle}</h1>
    </section>
    
    <section class="item_body">
        <h2 id="notice" class="notice"></h2>
        
        <fieldset><label>Bestand</label>
            <select id="current_languagefile">
            <block {available_languagesfiles}>
            <option value="{value}" {selected}>{text}</option>
            </block>
            </select>
        </fieldset>
        
        <div id="languageTree">
        {tree}
        </div>   
    </section>
</section>