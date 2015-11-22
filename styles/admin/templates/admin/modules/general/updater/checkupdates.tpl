<section id="maintenance">
    <section class="item_header">
       <h1 id="maintenance_main_title">{moduleTitle}</h1>
    </section>
    
    <section class="item_body">
    	<if {ok}>
    		<if {major_upgrade}>
    			<h2>Nieuwe versie beschikbaar</h2>
    			
    			<p>Versie {major} is beschikbaar voor download.</p>
    		</if>
    		<if {minor_upgrade}>
    			<h2>Software bijwerken</h2>
    			
    			<p>Bijwerken van versie {currentVersion} naar {maxVersion}</p>
    			
    			<input type="button" id="upgrade_button" value="Bijwerken naar {maxVersion}">
    			
    			<h3>Details</h3>
    			<block {versions}>
    				<h4>{version}</h4>
    				<p>{description}</p>
    			</block>
    		</if>
    		<else>
    			<h2 class="notice">Software up to date.</h2>
    		</else>
    	</if>
    	<else>
    		<h2 class="errorNotice">{status}</h2>
    	</else>
    </section>
</section>