<div id="users">
	<h1>{userTitle}</h1>

	<div class="adminPanel">
		<h2 class="errorNotice" id="formNotice"></h2>
    <table>
        <tr>
            <td colspan="2" class="errorNotice" id="notice"></td>
        </tr>
        <tr>
            <td class="bold">{usernameHeader}</td>
            <td><input type="text" id="username" value="" class="formField" onblur="adminUsers.checkUsername(this.value)"/></td>
        </tr>
        <tr>
            <td class="bold">{firstnameHeader}</td>
            <td><input type="text" id="firstname" value="" class="formField"/></td>
        </tr>
        <tr>
            <td class="bold">{nameBetweenHeader}</td>
            <td><input type="text" id="nameBetween" value="" class="formField"/></td>
        </tr>
        <tr>
            <td class="bold">{surnameHeader}</td>
            <td><input type="text" id="surname" value="" class="formField"/></td>
        </tr>
        <tr>
            <td class="bold">{emailHeader}</td>
            <td><input type="text" id="email" value="" class="formField" onblur="adminUsers.checkEmail(this.value)"/></td>
        </tr>      
        <tr>
        	<td class="bold">{nationalityHeader}</td>
        	<td><input type="text" id="nationality" value="" class="formField"/></td>
        </tr>
        <tr>
        	<td class="bold">{telephoneHeader}</td>
        	<td><input type="text" id="telephone" value="" class="formField"/></td>
        </tr>  
        <tr>
            <td class="bold">{passwordHeader}</td>
            <td><input type="password" id="password" value="" class="formField"/></td>
        </tr>        
        <tr>
            <td class="bold">{passwordRepeatHeader}</td>
            <td><input type="password" id="password2" value="" class="formField"/></td>
        </tr>
        <tr>
            <td class="bold">{botHeader}</td>
            <td><input type="radio" id="bot_0" value="0" checked="checked"> <label>{no}</label>
                <input type="radio" id="bot_1" value="1"> <label>{yes}</label></td>
        </tr>
        <tr>
            <td colspan="2"><br/></td>
        </tr>
        <tr>
        	<td><a href="javascript:adminUsers.add()" class="button">{buttonSave}</a></td>
        	<td><a href="javascript:adminUsers.view()" class="button">{buttonBack}</a></td>
        </table>
    </div>
</div>
