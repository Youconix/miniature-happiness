<section id="groups">
    <section class="item_header">
	   <h1 id="groups_main_title">{groupTitle}</h1>
	   
	   <nav>
        <ul>
            <li id="groups_delete" data-id="{id}" data-name="{nameDefault}" {editDisabled}>{buttonDelete}</li>
            <li id="groups_edit" data-id="{id}" {editDisabled}>{buttonEdit}</li>
            <li id="users_back">{buttonBack}</li>
        </ul>
      </nav>
	</section>
	
	<section class="item_body">
	    <fieldset>
			<label class="label">{headerName}</label>
	        {nameDefault}
	   	</fieldset>
	    <fieldset>
	    	<label class="label">{headerDescription}</label>
	        {descriptionDefault}
		</fieldset>
		<fieldset>
	        <label class="label">{headerAutomatic}</label>
	        {automatic}
		</fieldset>
		
		<h2>{memberlistTitle}</h2>
		
		<table id="group_user_list">
		<tbody>
		<block {userlist}>
		  <tr data-id="{userid}">
		      <td>{user}</td>
		      <td>{level}</td>
		  </tr>
		</block>
		</tbody>
		</table>
	</section>
</section>