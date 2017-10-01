<section id="groups">
    <section class="item_header">
	   <h1 id="groups_main_title">{{ $groupTitle }}</h1>
	   
	   <nav>
        <ul>
            <li id="groups_delete" data-id="{{ $id }}" data-name="{{ $nameDefault }}" {{ $editDisabled }}>{{ $buttonDelete }}</li>
            <li id="users_back">{{ $buttonBack }}</li>
        </ul>
      </nav>
	</section>
	
	<section class="item_body">
	    <fieldset>
                <label class="label">{{ $headerName }}</label>
	        <input type="text" name="name" id="name" required value="{{ $nameDefault }}">
            </fieldset>
	    <fieldset>
	    	<label class="label">{{ $headerDescription }}</label>
	        <textarea name="description" id="description" required>{{ $descriptionDefault }}</textarea>
            </fieldset>
            <fieldset>
	        <label class="label">{{ $headerAutomatic }}</label>
	        <input type="checkbox" name="automatic" id="default_1" {{ $automatic }}>
            </fieldset>
            <fieldset>
                <input type="button" id="groupEdit" value="{{ $buttonSubmit }}" class="button">
                <input type="button" id="groupCancel" value="{{ $buttonCancel }}" style="float:right">
            </fieldset>
	</section>
</section>
