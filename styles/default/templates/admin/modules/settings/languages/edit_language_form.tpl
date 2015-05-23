<script src="{NIV}js/ckeditor/ckeditor.js"></script>
<section id="settings">
    <section class="item_header">
       <h1 id="settings_language_edit_title">{languageTitle}</h1>
    </section>
    
    <section class="item_body">
        <h2 id="notice" class="notice"></h2>
        
        <h3>{file}</h3>
        <h3>{path}</h3>
        
        <section id="languageEditor">
        
        <block {languageItem}>
            <article>
	            <h4>{languageName}</h4>
	            
	            <fieldset>
    	            <textarea name="editor{nr}" id="editor{nr}" rows="5" cols="80">{text}</textarea>
	            </fieldset>
            </article>
        </block>
        </section>   
    </section>
</section>