<section id="groups" class="window">
	<h1>{headerText}</h1>

	<form action="index.php?step=5" method="post">
	<table id="userTable">
	<tbody>
	<tr>
		<td><label>{nickText}</label></td>
		<td><input type="text" name="nick" id="nick" onblur="checkForm()" value="{nick}"></td>
	</tr>
	<tr>
		<td><label>{emailText}</label></td>
		<td><input type="text" name="email" id="email" onblur="checkForm()" value="{email}"></td>
	</tr>
	<tr>
		<td><label>{password}</label></td>
		<td><input type="password" name="password" id="password" onblur="checkForm()"></td>
	</tr>
	<tr>
		<td><label>{password2}</label></td>
		<td><input type="password" name="password2" id="password2" onblur="checkForm()"></td>
	</tr>
	<tr>
		<td colspan="3"><input type="submit" value="{buttonSubmit}"></td>
	</tr>
	</tbody>
	</table>
	</form>
</section>
