<section class="login">
	<form action="{NIV}authorization/normal/update" method="post">
		<h1>{expired_title}</h1>
		
		<h2 class="errorNotice">{errorNotice}</h2>
	
		<table style="margin: auto; border: 0;">
		<tbody>
			<tr>
				<td><label>{password} :</label></td>
				<td><input type="password" name="password_old" required></td>
			</tr>
			<tr>
				<td><label>{newPassword} :</label></td>
				<td><input type="password" name="password" required></td>
			</tr>
			<tr>
				<td><label>{newPassword2} :</label></td>
				<td><input type="password" name="password2" required></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="{loginButton}" class="button"></td>
			</tr>
			</tbody>
		</table>
	</form>
</section>
