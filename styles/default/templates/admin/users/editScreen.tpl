<div id="users">
	<h1>{headerText}</h1>

	<div class="adminPanel">
		<table cellspacing="0" cellpadding="0">
		<tr>
			<td><a href="javascript:adminUsers.view()"><img src="{style_dir}images/icons/back.png" alt="{buttonBack}" title="{buttonBack}"/></a>
	        	<a href="javascript:adminUsers.deleteUser({id},{userid})"><img src="{style_dir}images/icons/delete.png" alt="{delete}" title="{delete}"/></a></td>
	        <td><br/></td>
		<tr>
		<tr>
			<td class="bold">{usernameHeader}</td>
			<td>{username}</td>
		</tr>
		<tr>
			<td class="bold">{firstnameHeader}</td>
			<td><input type="text" id="firstname" value="{firstname}" class="formField"/></td>
		</tr>
		<tr>
			<td class="bold">{nameBetweenHeader}</td>
			<td><input type="text" id="nameBetween" value="{nameBetween}" class="formField"/></td>
		</tr>
		<tr>
			<td class="bold">{surnameHeader}</td>
			<td><input type="text" id="surname" value="{surname}" class="formField"/></td>
		</tr>
		<tr>
			<td class="bold">{emailHeader}</td>
			<td><input type="text" id="email" value="{email}" class="formField"/></td>
		</tr>
		<tr>
        	<td class="bold">{nationalityHeader}</td>
        	<td><input type="text" id="nationality" value="" class="formField"/></td>
        </tr>
        <tr>
        	<td class="bold">{telephoneHeader}</td>
        	<td><input type="text" id="telephone" value="" class="formField"/></td>
        </tr>  
		<tr>
			<td class="bold">{botHeader}</td>
			<td><input type="radio" id="bot_0" value="0" {bot0}> <label>{no}</label>
                <input type="radio" id="bot_1" value="1" {bot1}> <label>{yes}</label></td>
		</tr>
		<tr>
			<td class="bold">{registratedHeader}</td>
			<td>{registrated}</td>
		</tr>
		<tr>
			<td class="bold">{activeHeader}</td>
			<td>{active}</td>
		</tr>
		<tr>
			<td class="bold">{blockedHeader}</td>
			<td><input type="radio" id="blocked_0" value="0" {blocked0}> <label>{no}</label>
                <input type="radio" id="blocked_1" value="1" {blocked1}> <label>{yes}</label></td>
		</tr>		
		<tr>
			<td colspan="2"><input type="hidden" id="id" value="{id}"/>
				<a href="javascript:adminUsers.editUserSave({userid})" class="button">Opslaan</a></td>
		</tr>
		</table>
		
		<h2>{groupHeader}</h2>
		<table cellspacing="0" cellpadding="0">
        <block {groupDefault}>
        <tr>
        	<td><input type="checkbox" disabled="disabled" {checked}/>
        		<label>{groupName}</label></td>
        	<td><select id="groep_level_{groupID}" onchange="adminUsers.changeGroup({groupID},this.value)">{levels}</select></td>
        </tr>
        </block>
        <block {group}>
        <tr>
        	<td><input type="checkbox" id="group_{groupID}"  value="{groupID}" onclick="adminUsers.selectGroup({groupID},{userid})" {checked}/>
        		<label>{groupName}</label></td>
        	<td><select id="groep_level_{groupID}" onchange="adminUsers.changeGroup({groupID},this.value)">{levels}</select></td>
        </tr>
        </block>
        </table>
		
		{password_notice}
		{[password_form]}
	</div>
</div>