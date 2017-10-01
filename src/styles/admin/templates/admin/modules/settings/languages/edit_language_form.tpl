<script src="{NIV}js/ckeditor/ckeditor.js"></script>
<section id="language_editor">
    <section class="item_header">
       <h1 id="settings_language_edit_title">{languageTitle}</h1>
       
       <nav>
            <ul>
                <li id="languageEditor_back">{buttonBack}</li>
            </ul>
        </nav>
    </section>
    
    <section class="item_body">
        <h2 id="notice" class="notice"></h2>
        
        <h3>{file}</h3>
        <h3>{path}</h3>
        
        <input type="hidden" id="file" value="{file}">
        <input type="hidden" id="path" value="{path}">
        
        <section class="tab_header">
        <block {languageItemHeader}>
            <div class="{class}" data-id="{nr}">
                {languageName}
            </div>
        </block>    
        </section>
        
        <section class="tab_content" id="languageEditor">        
        <block {languageItem}>
            <article id="tab_{nr}">
                <textarea name="editor{nr}" id="editor{nr}" rows="5" cols="80" data-id="{languageName}">{text}</textarea>
            </article>
        </block>
        </section>
        
        <fieldset>
            <input type="button" id="languageEditorSave" value="{save}">
        </fieldset>
        
        <script>
        <!--
        tabs.init({
            "id":"language_editor"
        });
        //-->
        </script>
    </section>
</section>