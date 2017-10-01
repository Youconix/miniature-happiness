<section id="users">
	<section class="item_header">
	   <h1 id="users_main_title">{{ $headerText }}</h1>
	   
	   <nav>
            <ul>
                <li id="users_back">{{ $buttonBack }}</li>
            </ul>
        </nav>
    </section>
    
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
			<input type="radio" name="bot" id="bot_0" value="0" checked="checked"><label>{{ $no }}</label>
            <input type="radio" name="bot" id="bot_1" value="1"><label>{{ $yes }}</label>
		</fieldset>
        
        <fieldset>
            <label for="password" class="label">{{ $passwordHeader }}</label>
            <input type="password" name="password" id="password1" data-validation="{{ $passwordError }}" required>
        </fieldset>
        <fieldset>
            <label for="password2" class="label">{{ $passwordRepeatHeader }}</label>
            <input type="password" name="password2" id="password2" data-validation="{{ $passwordError }}" required>
        </fieldset>
        <fieldset>
                <input type="submit" value="{{ $saveButton }}" id="userSaveButton">
                <input type="hidden" id="usernameOK" value="0">
                <input type="hidden" id="emailOK" value="0">            
        </fieldset>
	</section>
</section>
