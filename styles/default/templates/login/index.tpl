<section class="login">
	<form action="login.php" method="post">
		<table style="margin: auto; border: 0;">
		<tbody>
			<tr>
				<td><label>{username}</label></td>
				<td><input type="text" name="username" required></td>
			</tr>
			<tr>
				<td><label>{password}</label></td>
				<td><input type="password" name="password" required></td>
			</tr>
			<tr>
				<td><input type="checkbox" value="1" name="autologin" style="float:right"></td>
				<td>{autologin}</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="{loginButton}" class="button" style="float: right;"></td>
			</tr>
			<tr>
				<td><a href="{NIV}registration.php">{registration}</a></td>
				<td><a href="{NIV}forgot_password.php">{forgotPassword}</a>
				</td>
			</tr>
			<block {openID}>
			<tr>
				<td colspan="2"><a href="login.php?command=openID&type={key}">{text}</a></td>
			</tr>
			</block>
			</tbody>
		</table>
	</form>
</section>
