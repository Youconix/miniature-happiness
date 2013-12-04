<div id="groups">
	<h1>{groupTitle}</h1>
	
	<div class="adminPanel">
	    <table id="groupview">
	        <tr>
	            <th>{headerUser}</th>
	            <th>{headerRights}</th>
	        </tr>
	        <block {user}>
	            <tr>
	                <td><a href="javascript:adminUsers.viewUser({id})">{username}</a></td>
	                <td>{rights}</td>
	            </tr>
	        </block>
	    </table>
	    
	    <a href="javascript:adminGroups.view()" class="button">{backButton}</a>
	</div>
</div>