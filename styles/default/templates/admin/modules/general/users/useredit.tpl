    <section class="item_header">
	   <h1 id="users_main_title">{headerText}</h1>
	   
	   <nav>
            <ul>
                <li id="users_delete" data-id="{id}" data-username="{username}" data-userid="{userid}" {deleteRejected}>{delete}</li>
                <li id="users_back">{buttonBack}</li>
            </ul>
        </nav>
    </section>
    
	<section class="item_body">
		<fieldset>
			<label class="label">{usernameHeader}</label>
			{username}
		</fieldset>
		<fieldset>
			<label for="email" class="label">{emailHeader}</label>
			<input type="email" id="email" name="email" value="{email}" required>
		</fieldset>
		<fieldset>
			<label for="bot" class="label">{botHeader}</label>
			<input type="radio" name="bot" id="bot_0" value="0" {bot0}> <label>{no}</label>
                <input type="radio" name="bot" id="bot_1" value="1" {bot1}> <label>{yes}</label>
		</fieldset>
		<fieldset>
			<label class="label">{registratedHeader}</label>
			{registrated}
		</fieldset>
		<fieldset>
          <label class="label">{loggedinHeader}</label>
          {loggedIn}
        </fieldset>
        <fieldset>
			<label class="label">{activeHeader}</label>
			{active}
		</fieldset>
		<fieldset>
			<label for="blocked" class="label">{blockedHeader}</label>
			<input type="radio" name="blocked" id="blocked_0" value="0" {blocked0}> <label>{no}</label>
                <input type="radio" name="blocked" id="blocked_1" value="1" {blocked1}> <label>{yes}</label>
		</fieldset>
		
		<h2>Wachtwoord veranderen</h2>
        <h3>Laat leeg om de wachtwoorden hetzelfde te laten</h3>
        
        <fieldset>
            <label for="password1" class="label">Nieuwe wachtwoord</label>
            <input type="password" name="password1" id="password1" required>
        </fieldset>
        <fieldset>
            <label for="password2" class="label">Wachtwoord herhalen</label>
            <input type="password" name="password2" id="password2" required>
        </fieldset>
        <fieldset>
            <td colspan="2">
            <input type="hidden" id="userid" value="{id}">
            <input type="submit" value="Aanpassen" id="userUpdateButton">
        </fieldset>
        
		<h2>Groups</h2>
		
        <article id="groupslist">
          <block {userGroupBlocked}>
              <fieldset>{name} - {level}</fieldset>
          </block>
          <block {userGroup}>
              <fieldset>{name} - {level} <img src="{NIV}{style_dir}images/icons/delete.png" alt="{delete}" title="{delete}" class="delete" data-id="{userid}" data-group="{id}" data-level="{levelNr}"></fieldset>
          </block>
        </article>
        
        <h2>Add group</h2>
        
        <fielset>
        <select id="newGroup" data-id="{id}">
            <option value="">Choose a group</option>
        <block {newGroup}>
            <option value="{value}">{text}</option>
        </block>
        </select>
        
        <select id="newLevel">
            <option value="">Select an access level</option>
            
            <block {newLevel}>
                <option value="{value}">{text}</option>
            </block>
        </select>        
        </fieldset>
        
        <script>
        <!--
        var styleDir = '{NIV}{style_dir}';
        -->
        </script>
	</section>