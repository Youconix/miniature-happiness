<div id="users">
	<h1>{headerText}</h1>

	<div class="adminPanel">
		<table cellspacing="0" cellpadding="0">
		<tr>
			<td><a href="javascript:adminUsers.view()"><img src="{style_dir}images/icons/back.png" alt="{buttonBack}" title="{buttonBack}"/></a>
	        	<a href="javascript:adminUsers.editUser({id})"><img src="{style_dir}images/icons/edit.png" alt="{edit}" title="{edit}"/></a>
	            <a href="javascript:adminUsers.deleteUser({id},{userid})"><img src="{style_dir}images/icons/delete.png" alt="{delete}" title="{delete}"/></a></td>
	        <td><br/></td>
			<td rowspan="10"><br/></td>
		<tr>
		<tr>
			<td class="bold">{usernameHeader}</td>
			<td>{username}</td>
		</tr>
		<tr>
			<td class="bold">{firstnameHeader}</td>
			<td>{firstname}</td>
		</tr>
		<tr>
			<td class="bold">{nameBetweenHeader}</td>
			<td>{nameBetween}</td>
		</tr>
		<tr>
			<td class="bold">{surnameHeader}</td>
			<td>{surname}</td>
		</tr>
		<tr>
			<td class="bold">{emailHeader}</td>
			<td>{email}</td>
		</tr>
		<tr>
			<td class="bold">{botHeader}</td>
			<td>{bot}</td>
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
			<td>{blocked}</td>
		</tr>
		</table>
	</div>
</div>