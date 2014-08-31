<section id="users">
	<h1>{headerText}</h1>

	<section>
		<table>
		<tbody>
		<tr>
			<td><a href="javascript:adminUsers.view()"><img src="{style_dir}images/icons/back.png" alt="{buttonBack}" title="{buttonBack}"></a>
	        	<a href="javascript:adminUsers.deleteUser({id},{userid})"><img src="{style_dir}images/icons/delete.png" alt="{delete}" title="{delete}"></a></td>
	        <td><br></td>
		<tr>
		<tr>
			<td><label>{usernameHeader}</label></td>
			<td>{username}</td>
		</tr>
		<tr>
			<td><label>{firstnameHeader}</label></td>
			<td><input type="text" id="firstname" value="{firstname}"></td>
		</tr>
		<tr>
			<td><label>{nameBetweenHeader}</label></td>
			<td><input type="text" id="nameBetween" value="{nameBetween}"></td>
		</tr>
		<tr>
			<td><label>{surnameHeader}</label></td>
			<td><input type="text" id="surname" value="{surname}"></td>
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
			<td><label>{activeHeader}</label></td>
			<td>{active}</td>
		</tr>
		<tr>
			<td><label>{blockedHeader}</label></td>
			<td><input type="radio" id="blocked_0" value="0" {blocked0}> <label>{no}</label>
                <input type="radio" id="blocked_1" value="1" {blocked1}> <label>{yes}</label></td>
		</tr>		
		<tr>
			<td colspan="2"><input type="hidden" id="id" value="{id}">
				<a href="javascript:adminUsers.editUserSave({userid})" class="button">Opslaan</a></td>
		</tr>
		</tbody>
		</table>
		
		<h2>{groupHeader}</h2>
		
		<table>
		<tbody>
        <block {groupDefault}>
        <tr>
        	<td><input type="checkbox" disabled="disabled" {checked}>
        		<label>{groupName}</label></td>
        	<td><select id="groep_level_{groupID}" onchange="adminUsers.changeGroup({groupID},this.value)">{levels}</select></td>
        </tr>
        </block>
        <block {group}>
        <tr>
        	<td><input type="checkbox" id="group_{groupID}"  value="{groupID}" onclick="adminUsers.selectGroup({groupID},{userid})" {checked}>
        		<label>{groupName}</label></td>
        	<td><select id="groep_level_{groupID}" onchange="adminUsers.changeGroup({groupID},this.value)">{levels}</select></td>
        </tr>
        </block>
        </tbody>
        </table>
		
		{password_notice}
		{[password_form]}
	</section>
</section>