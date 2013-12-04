<div class="login">
	<form action="login.php" method="post">
		<table cellpadding="0" cellspacing="0" style="margin: auto; border: 0;">
			<tr>
				<td class="textLeft">{username}</td>
				<td><input type="text" name="username" />
				</td>
			</tr>
			<tr>
				<td class="textLeft">{password} </td>
				<td><input type="password" name="password" />
				</td>
			</tr>
			<tr>
				<td><input type="checkbox" value="1" name="autologin" style="float:right"/></td>
				<td class="textLeft">{autologin}</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="{loginButton}" class="button" style="float: right;" /></td>
			</tr>
			<tr>
				<td><a href="{NIV}registration.php">{registration}</a>
				</td>
				<td><a href="{NIV}forgot_password.php">{forgotPassword}</a>
				</td>
			</tr>
			<block {openID}>
			<tr>
				<td colspan="2"><a href="login.php?command=openID&type={key}">{text}</a></td>
			</tr>
			</block>
		</table>
	</form>
</div>
