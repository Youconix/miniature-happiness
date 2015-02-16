<section class="login">
	<form action="{NIV}authorization/login/do_login" method="post">
		<table style="margin: auto; border: 0;">
			<tbody>
				<tr>
					<td><label>{usernameText}</label></td>
					<td><input type="text" name="username" value="{username}"
						required></td>
				</tr>
				<tr>
					<td><label>{passwordText}</label></td>
					<td><input type="password" name="password" required></td>
				</tr>
				<tr>
					<td><input type="checkbox" value="1" name="autologin"
						style="float: right"></td>
					<td>{autologin}</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="{loginButton}"
						class="button" style="float: right;"></td>
				</tr>
				<tr>
					<td><a href="{NIV}authorization/registration/index">{registration}</a></td>
					<td><a href="{NIV}forgot_password/index">{forgotPassword}</a>
					</td>
				</tr>
				<block{login}>
				<tr>
					<td colspan="2"><a href="{NIV}authorization/login_{key}/index"><img
							src="{NIV}{style_dir}images/icons/{image}.png" alt="{key}"
							title="{key}">{text}</a></td>
				</tr>
				</block>
			</tbody>
		</table>
	</form>
</section>
