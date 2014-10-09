<section class="login">
	<form action="forgot_password.php" method="post">
		<h1>{fogotTitle}</h1>
		
		<h2 class="errorNotice">{errorNotice}</h2>
		
		<table style="margin: auto; border: 0;">
		<tbody>
			<tr>
				<td><label>{email} :</label></td>
				<td><input type="email" name="email" required></td>
			</tr>
			<tr>
				<td colspan="2"><input type="hidden" name="command" value="reset">
				<input type="submit" value="{loginButton}" class="button"></td>
			</tr>
			</tbody>
		</table>
	</form>
</section>