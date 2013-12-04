<div id="groups">
	<h1>{groupTitle}</h1>
	
	<h2 class="errorNotice" id="notice"></h2>
	<div class="adminPanel">
		<table cellspacing="0" cellpadding="0">
	        <tr>
			<td class="bold">{headerName}</td>
	            	<td><input type="text" name="name" value="{nameDefault}" class="textAdd" id="name"/></td>
	        </tr>
	        <tr>
	            	<td class="bold">{headerDescription}</td>
	            	<td><textarea cols="0" rows="0" name="description" id="description">{descriptionDefault}</textarea></td>
		</tr>
		<tr>
	            	<td class="bold">{headerAutomatic}</td>
	            	<td><input type="checkbox" name="automatic" id="default_1"/></td>
		</tr>
		<tr>
			<td><input type="button" value="{buttonSubmit}" class="button" onclick="adminGroups.add()"/></td>
			<td><input type="button" value="{buttonCancel}" class="button" onclick="adminGroups.view()" style="float:right"/></td>
		</tr>        
	        </table>
	</div>
</div>