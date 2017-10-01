<section class="login">
	<form action="{NIV}authorization/normal/do_login" method="post">
	    <h2>{loginTitle}</h2>
	
		<fieldset>
				<label class="label">{usernameText}</label>
				<input type="text" name="username" id="username" value="{username}" data-validation="The username is not filled in" required>
		</fieldset>
		<fieldset>
				<label class="label">{passwordText}</label>
				<input type="password" id="password" name="password" data-validation="The password is not filled in" required>
		</fieldset>
		<fieldset>
				<label class="label" style="width:250px">{autologin}</label>
				<input type="checkbox" value="1" name="autologin">
		</fieldset>
		<fieldset>
		        <label class="label"><a href="{NIV}authorization/normal/registration_screen">{registration}</a></label>
		        <label class="label"><a href="{NIV}forgot_password/index">{forgotPassword}</a></label>
				<input type="submit" value="{loginButton}" class="button">
		</fieldset>
			
		<fieldset>   
		  <block {openID}>        
		      <a href="{NIV}authorization/registration_{key}/login_screen">
		      <img class="login_icon" src="{NIV}{shared_style_dir}images/authorization/{image}.png" alt="{key}" title="{key}"></a>
		  </block>
	    </fieldset>
	</form>
</section>
