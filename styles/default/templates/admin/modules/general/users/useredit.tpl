    <section class="item_header">
	   <h1>{headerText}</h1>
	   
	   <nav>
            <ul>
                <li id="users_back">{buttonBack}</li>
                <li id="users_delete" data-id="{id}" data-username="{username}" data-userid="{userid}" {deleteRejected}>{delete}</li>
            </ul>
        </nav>
    </section>
    
	<section class="item_body">
		<table>
		<tbody>
		<tr>
			<td><label>{usernameHeader}</label></td>
			<td>{username}</td>
		</tr>
		<tr>
			<td><label>{emailHeader}</label></td>
			<td><input type="email" id="email" value="{email}" required></td>
		</tr>
		<tr>
			<td><label>{botHeader}</label></td>
			<td><input type="radio" id="bot_0" value="0" {bot0}> <label>{no}</label>
                <input type="radio" id="bot_1" value="1" {bot1}> <label>{yes}</label></td>
		</tr>
		<tr>
			<td><label>{registratedHeader}</label></td>
			<td>{registrated}</td>
		</tr>
		<tr>
          <td><label>{loggedinHeader}</label></td>
          <td>{loggedIn}</td>
        </tr>
        <tr>
			<td><label>{activeHeader}</label></td>
			<td>{active}</td>
		</tr>
		<tr>
			<td><label>{blockedHeader}</label></td>
			<td><input type="radio" id="blocked_0" value="0" {blocked0}> <label>{no}</label>
                <input type="radio" id="blocked_1" value="1" {blocked1}> <label>{yes}</label></td>
		</tr>
		</tbody>
		</table>
		
		<h2>Wachtwoord veranderen</h2>
        <h3>Laat leeg om de wachtwoorden hetzelfde te laten</h3>
        
        <table>
        <tbody>
        <tr>
            <td><label>Nieuwe wachtwoord</label></td>
            <td><input type="password" id="password1" required></td>
        </tr>
        <tr>
            <td><label>Wachtwoord herhalen</label></td>
            <td><input type="password" id="password2" required></td>
        </tr>
        <tr>
            <td colspan="2">
            <input type="hidden" id="userid" value="{id}">
            <input type="submit" value="Aanpassen" id="userUpdateButton"></td>
        </tr>
        </tbody>
        </table>
		
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