<section id="groups">
    <section class="item_header">
	   <h1 id="groups_main_title">{editTitle}</h1>
	   
	   <nav>
        <ul>
            <li id="groups_delete" data-id="{id}" data-name="{nameDefault}">{delete}</li>
            <li id="users_back">{buttonBack}</li>
        </ul>
      </nav>
	</section>
	
	<section class="item_body">
	    <fieldset>
			<label for="name" class="label">{headerName}</label>
	        <input type="text" name="name" value="{nameDefault}" id="name" required>
	    </fieldset>
	    <fieldset>
	    	<label for="description" class="label">{headerDescription}</label>
	        <textarea name="description" id="description" required>{descriptionDefault}</textarea>
		</fieldset>
		<fieldset>
			<label for="automatic" class="label">{headerAutomatic}</label>
	        <input type="checkbox" name="automatic" id="default_1" {defaultChecked}>
		</fieldset>
		<fieldset>
			<input type="hidden" id="id" value="{id}">
			<input type="button" value="{buttonSubmit}" id="groupEditSave">
			<input type="button" value="{buttonCancel}" id="groupCancel" style="float:right">
		</fieldset>
	</section>
</section>