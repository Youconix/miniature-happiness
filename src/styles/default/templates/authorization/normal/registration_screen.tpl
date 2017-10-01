<section id="registration">
	<h2>{registration}</h2>
	
	<h2 id="errorNotice" class="errorNotice">{errorNotice}</h2>

	<form action="{NIV}authorization/normal/do_registration" method="post" onsubmit="return site.checkRegistration()">
	   <fieldset>
        <label class="label" for="username">{usernameText} *</label>
		<input type="text" name="username" id="reg_username" value="{username}" required data-validation="{usernameError}" data-validation-pattern="De gebruikersnaam moet minimaal 3 tekens en maximaal 255 tekens lang zijn." pattern="^.{3,255}$">
	   </fieldset>
	   <fieldset>
				<label class="label" for="email">{emailText} *</label>
				<input type="email" name="email" id="reg_email" value="{email}" onblur="registration.checkEmail(this.value)" required data-validation="{emailError}" data-validation-pattern="Het email adres is ongeldig.">
		</fieldset>
			
			{passwordForm}			

			<h3>{captchaText}</h3>
			
		<fieldset>
		        <label class="label"></label>
				<img src="{NIV}{shared_style_dir}images/captcha.php" alt="">
        </fieldset>
        <fieldset>
                <label class="label"></label>
				<input type="text" name="captcha" id="captcha" value="" data-validation="De captcha is niet ingevuld." required>
		</fieldset>
		<fieldset>
		        <label class="label"></label>
				<input type="checkbox" name="conditions" id="reg_conditions">
				<label for="conditions"><a href="{NIV}conditions.php" target="_new">{conditionsText}</a></label>
		</fieldset>
		<fieldset>
		        <label class="label"></label>
				<input type="submit" value="{buttonRegister}">
		</fieldset>
		<fieldset>
			<br/>
		</fieldset>
		
		<fieldset>	
		<block {openID}>		
				<a href="{NIV}authorization/registration_{key}.php">
				<img class="login_icon" src="{NIV}{shared_style_dir}images/authorization/{image}.png" alt="{key}" title="{key}"></a>
		</block>
		</fieldset>
	</form>
	
	<script>
	<!--
	validation.bind(['reg_username','reg_email','captcha']);
	//-->
	</script>
</section>
