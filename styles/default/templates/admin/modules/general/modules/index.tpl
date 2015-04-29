<section id="modules">
    <section class="item_header">
	   <h1 id="modules_main_title">{moduleTitle}</h1>
	</section>
	
	<section class="item_body">
	   <h1>{installedModulesTitle}</h1>
	
	    <table id="installed_modules">
	    <thead>
	        <tr>
	            <td>{headerName}</td>
	            <td>{headerAuthor}</td>
	            <td>{headerVersion}</td>
	            <td>{headerDescription}</td>
	        </tr>
	    </thead>
	    <tbody>
	        <block {installedModule}>
	            <tr data-id="{id}" data-name="{name}">
	                <td>{name}</td>
	                <td>{author}</td>
	                <td>{version}</td>
	                <td>{description}</td>
	            </tr>
	          </block>
	    </tbody>
	    </table>
	    
	    <if {upgradableModules}>
            <h1>{upgradableModulesTitle}</h1>
            
            <table id="upgradable_modules">
            <thead>
                <tr>
                    <td>{headerName}</td>
                    <td>{headerAuthor}</td>
                    <td>{headerVersion}</td>
                    <td>{headerVersionAvaiable}</td>
                    <td>{headerDescription}</td>
                </tr>
            </thead>
            <tbody>
                <block {upgradeModule}>
                <tr data-name="{name}" data-name="{name}">
                    <td>{name}</td>
                    <td>{author}</td>
                    <td>{version}</td>
                    <td>{versionNew}</td>
                    <td>{description}</td>
                </tr>
              </block>
            </tbody>
            </table>
         </if>
	    
	    <if {availableModules}>
    	    <h1>{newModulesTitle}</h1>
    	    
    	    <table id="new_modules">
            <thead>
                <tr>
                    <td>{headerName}</td>
                    <td>{headerAuthor}</td>
                    <td>{headerVersion}</td>
                    <td>{headerDescription}</td>
                </tr>
            </thead>
            <tbody>
                <block {newModule}>
                <tr data-name="{name}">
                    <td>{name}</td>
                    <td>{author}</td>
                    <td>{version}</td>
                    <td>{description}</td>
                </tr>
              </block>
            </tbody>
            </table>
         </if>
		</section>
	</section>