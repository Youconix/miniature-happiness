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
        
        <fieldset>
            <label class="label">{groupLabel}</label>
            <select id="pages_group">
            <block {groups}>
            <option value="{value}" {selected}>{text}</option>
            </block>
            </select>
        </fieldset>
        <fieldset>
            <label class="label">{accessLevelLabel}</label>
            <select id="pages_accesslevel">
                <block {pageRight}>
                    <option value="{value}" {selected}>{text}</option>
                </block>
            </select>
        </fieldset>
        
        <fieldset>
            <input type="button" id="pages_update" value="{save}">
        </fieldset>
        
        <h2>{viewRightsTitle}</h2>
        
        <table id="right_list" data-styledir="{NIV}{style_dir}">
        <tbody>
        <block {template_rights}>
        <tr data-template="{command}">
            <td>{command}</td>
            <td>{level}</td>
            <td><img src="{NIV}{style_dir}/images/icons/delete.png" alt="{delete}" title="{delete}"></td>
        </tr>
        </block>
        </tbody>
        </table>
        
        <fieldset>
            <label class="label">View naam</label>
            <input type="text" id="view_name">
        </fieldset>
        <fieldset>
            <label class="label">{accessLevelLabel}</label>
               <select id="template_level">
                <block {templateRight}>
                    <option value="{value}" {selected}>{text}</option>
                </block>
            </select>
        </fieldset>
        <fieldset>
            <input type="button" id="pages_template_add" value="{add}">
        </fieldset>
    </section>
 </section>