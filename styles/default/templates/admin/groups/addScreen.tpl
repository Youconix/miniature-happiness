<section id="groups">
	<h1>{groupTitle}</h1>
	
	<h2 class="errorNotice" id="notice"></h2>
	<section>
		<table>
		<tbody>
	    <tr>
			<td><label>{headerName}</label></td>
	        <td><input type="text" name="name" value="{nameDefault}" class="textAdd" id="name"></td>
	   	</tr>
	    <tr>
	    	<td><label>{headerDescription}</label></td>
	        <td><textarea name="description" id="description">{descriptionDefault}</textarea></td>
		</tr>
		<tr>
	        <td><label>{headerAutomatic}</label></td>
	        <td><input type="checkbox" name="automatic" id="default_1"></td>
		</tr>
		<tr>
			<td><input type="button" value="{buttonSubmit}" class="button" onclick="adminGroups.add()"></td>
			<td><input type="button" value="{buttonCancel}" class="button" onclick="adminGroups.view()" style="float:right"></td>
		</tr>
		</tbody>      
	    </table>
	</section>
</section>