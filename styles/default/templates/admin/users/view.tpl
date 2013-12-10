<section id="users">
	<h1>{headerText}</h1>

	<section>
		<table>
		<tbody>
		<tr>
			<td><a href="javascript:adminUsers.view()"><img src="{style_dir}images/icons/back.png" alt="{buttonBack}" title="{buttonBack}"></a>
	        	<a href="javascript:adminUsers.editUser({id})"><img src="{style_dir}images/icons/edit.png" alt="{edit}" title="{edit}"></a>
	            <a href="javascript:adminUsers.deleteUser({id},{userid})"><img src="{style_dir}images/icons/delete.png" alt="{delete}" title="{delete}"></a></td>
	        <td><br></td>
			<td rowspan="10"><br></td>
		<tr>
		<tr>
			<td><label>{usernameHeader}</label></td>
			<td>{username}</td>
		</tr>
		<tr>
			<td><label>{firstnameHeader}</label></td>
			<td>{firstname}</td>
		</tr>
		<tr>
			<td><label>{nameBetweenHeader}</label></td>
			<td>{nameBetween}</td>
		</tr>
		<tr>
			<td><label>{surnameHeader}</label></td>
			<td>{surname}</td>
		</tr>
		<tr>
			<td><label>{emailHeader}</label></td>
			<td>{email}</td>
		</tr>
		<tr>
			<td><label>{botHeader}</label></td>
			<td>{bot}</td>
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
			<td>{blocked}</td>
		</tr>
		</tbody>
		</table>
	</section>
</section>