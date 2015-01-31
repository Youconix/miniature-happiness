<section id="users">
	<h1>{userTitle}</h1>

	<section>
		<h2 class="errorNotice" id="notice"></h2>
    	<table>
    	<tbody>
        <tr>
            <td><label>{usernameHeader}</label></td>
            <td><input type="text" id="username" value="" onblur="adminUsers.checkUsername(this.value)" required></td>
        </tr>
        <tr>
            <td><label>{firstnameHeader}</label></td>
            <td><input type="text" id="firstname" value=""></td>
        </tr>
        <tr>
            <td><label>{nameBetweenHeader}</label></td>
            <td><input type="text" id="nameBetween" value=""></td>
        </tr>
        <tr>
            <td><label>{surnameHeader}</label></td>
            <td><input type="text" id="surname" value=""></td>
        </tr>
        <tr>
            <td><label>{emailHeader}</label></td>
            <td><input type="email" id="email" value="" onblur="adminUsers.checkEmail(this.value)" required></td>
        </tr>
        <tr>
            <td><label>{passwordHeader}</label></td>
            <td><input type="password" id="password" value="" required></td>
        </tr>        
        <tr>
            <td><label>{passwordRepeatHeader}</label></td>
            <td><input type="password" id="password2" value="" required></td>
        </tr>
        <tr>
            <td><label>{botHeader}</label></td>
            <td><input type="radio" id="bot_0" value="0" checked="checked"> <label>{no}</label>
                <input type="radio" id="bot_1" value="1"> <label>{yes}</label></td>
        </tr>
        <tr>
            <td colspan="2"><br></td>
        </tr>
        <tr>
        	<td><a href="javascript:adminUsers.add()" class="button">{buttonSave}</a></td>
        	<td><a href="javascript:adminUsers.view()" class="button">{buttonBack}</a></td>
        </tr>
        </tbody>
        </table>
    </section>
</section>
