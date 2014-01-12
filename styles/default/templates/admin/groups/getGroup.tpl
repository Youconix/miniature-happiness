<section id="groups">
	<h1>{editTitle}</h1>
	
	<h2 class="errorNotice" id="notice"></h2>
	<section>
		<table>
		<tbody>
	    <tr>
			<td><label>{headerName}</label></td>
	        <td><input type="text" name="name" value="{nameDefault}" id="name" required></td>
	    </tr>
	    <tr>
	    	<td><label>{headerDescription}</label></td>
	        <td><textarea name="description" id="description" required>{descriptionDefault}</textarea></td>
		</tr>
		<tr>
			<td><label>{headerAutomatic}</label></td>
	        <td><input type="checkbox" name="automatic" id="default_1" {defaultChecked}></td>
		</tr>
		<tr>
			<td><input type="hidden" id="id" value="{id}">
			<input type="button" value="{buttonSubmit}" onclick="adminGroups.editSave()"></td>
			<td><input type="button" value="{buttonCancel}" onclick="adminGroups.view()" style="float:right"></td>
		</tr>
		</tbody>        
	   </table>
	</section>
</section>