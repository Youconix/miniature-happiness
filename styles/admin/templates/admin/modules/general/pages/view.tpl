<section id="pages" data-url="{url}">
    <section class="item_header">
       <h1 id="pages_main_title">{pageTitle}</h1>
       
       <nav>
        <ul>
            <li id="pages_delete">{buttonDelete}</li>
            <li id="pages_back">{buttonBack}</li>
        </ul>
      </nav>
    </section>
    
    <section class="item_body">
        <h2>{name}</h2>
        
        <article id="current_rights">        
       		<h3>{generalRightsHeader}</h3>
       		
       		<fieldset>
	            <label class="label">{groupLabel}</label>
	            <select id="pages_group">	            	
	            	<option value="">{groupDefault}</option>
	            <block {groups}>
	            <option value="{value}" {selected}>{text}</option>
	            </block>
	            </select>
	        </fieldset>
	        <fieldset>
	            <label class="label">{accessLevelLabel}</label>
	            <select id="pages_accesslevel">
	            	<option value="">{viewRightsDefault}</option>
	                <block {pageRight}>
	                    <option value="{value}" {selected}>{text}</option>
	                </block>
	            </select>
	        </fieldset>
	        
	        <fieldset>
	            <input type="button" id="pages_update" value="{save}">
	            <input type="button" id="pages_reset" value="{reset}">
	        </fieldset>
        </article>
        
        <article id="currentViewRights">
        	<h2>{viewRightsTitle}</h2>
        	
        	<div>
		        <table id="right_list" data-styledir="/{shared_style_dir}">
		        <thead>
		        <tr>        
		        	<td width="50px"></td>
		        	<td>{viewLabel}</td>
		        	<td>{groupLabel}</td>
		        	<td>{accessLevelLabel}</td>
		        </tr>
		        </thead>
		        <tbody>
		        <block {template_rights}>
		        <tr data-template="{command}" data-id="{id}" id="view_{id}">        
		            <td style="width:50px"><img src="{NIV}{shared_style_dir}/images/icons/delete.png" alt="{delete}" title="{delete}"></td>
		            <td>{command}</td>
		            <td>{group}</td>
		            <td>{level}</td>
		        </tr>
		        </block>
		        </tbody>
		        </table>
		    </div>
		   	<div>
				<fieldset>
		            <label class="label">{viewLabel}</label>
		            <input type="text" id="view_name">
		        </fieldset>
		        <fieldset>
		            <label class="label">{accessLevelLabel}</label>
		               <select id="template_level">
		               	<option value="">{viewRightsDefault}</option>
		                <block {templateRight}>
		                    <option value="{value}" {selected}>{text}</option>
		                </block>
		            </select>
		        </fieldset>
		        <fieldset>
		        	<label class="label">{groupLabel}</label>
		        	<select id="viewGroups">
		        		<option value="">{groupDefault}</option>
		        	<block {groups2}>
			            <option value="{value}" {selected}>{text}</option>
			       </block>
		            </select>
		        </fieldset>
		        <fieldset>
		            <input type="button" id="pages_template_add" value="{add}">
		        </fieldset>
			</div>
		</article>
    </section>
 </section>