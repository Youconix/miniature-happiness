<section id="cache">
    <section class="item_header">
       <h1 id="cache_main_title">{{ $moduleTitle }}</h1>
    </section>
    
    <section class="item_body">
        <article>
            <fieldset>
                <label class="label">Enabled</label>
                {!! $enabled->generate() !!}
            </fieldset>
        </article>
	</section>
</section>
