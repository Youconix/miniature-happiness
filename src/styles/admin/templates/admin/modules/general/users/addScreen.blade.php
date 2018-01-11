<section id="users">
	<section class="item_header">
	   <h1 id="users_main_title">{{ $headerText }}</h1>
	   
	   <nav>
            <ul>
                <li id="users_back">{{ $buttonBack }}</li>
            </ul>
        </nav>
    </section>
    
  <form id="addForm" method="post" action="path('admin_users_add')">
	<section class="item_body">
	    <fieldset>
            <label for="username" class="label">{{ $usernameHeader }}</label>
            <input type="text" id="username" name="username" data-validation="{{ $usernameError }}" required>
        </fieldset>
		<fieldset>
			<label for="email" class="label">{{ $emailHeader }}</label>
			<input type="email" id="email" name="email" data-validation="{{ $emailError }}" required>
		</fieldset>
		<fieldset>
			<label for="bot" class="label">{{ $botHeader }}</label>
			{!! $bot->generate() !!}
		</fieldset>
        
        {!! $passwordForm->generate() !!}
        <fieldset>
                <input type="submit" value="{{ $saveButton }}" id="userSaveButton">
                <input type="hidden" id="usernameOK" value="0">
                <input type="hidden" id="emailOK" value="0">            
        </fieldset>
	</section>
  </form>
</section>

{!! $head !!}
<script>
  onOff.init();
</script>
