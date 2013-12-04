<div id="groups">
	<h1>{groupTitle}</h1>
	
	<div class="adminPanel">
	    <table id="groupview">
	        <tr>
	            <th>{headerID}</th>
	            <th>{headerName}</th>
	            <th>{headerDescription}</th>
	            <th>{headerAutomatic}</th>
	            <th colspan="3"></th>
	        </tr>
	        <block {groupBlocked}>
	            <tr>
	                <td>{id}</td>
	                <td>{name}</td>
	                <td>{description}</td>
	                <td>{default}</td>
	                <td><img src="{style_dir}images/icons/edit_grey.png" alt="{buttonEdit}" title="{buttonEdit}"/></td>
	                <td><img src="{style_dir}images/icons/delete_grey.png" alt="{buttonDelete}" title="{buttonDelete}"/></td>
	                <td><a href="javascript:adminGroups.viewUsers({id})" class="button">{viewUsers}</a></td>
	            </tr>
	        </block>
	        <block {group}>
	            <tr>
	                <td>{id}</td>
	                <td>{name}</td>
	                <td>{description}</td>
	                <td>{default}</td>
	                <td><a href="javascript:adminGroups.edit({id})"><img src="{style_dir}images/icons/edit.png" alt="{buttonEdit}" title="{buttonEdit}"/></a></td>
	                <td><a href="javascript:adminGroups.deleteGroup({id})"><img src="{style_dir}images/icons/delete.png" alt="{buttonDelete}" title="{buttonDelete}"/></a></td>
	                <td><a href="javascript:adminGroups.viewUsers({id})" class="button">{viewUsers}</a></td>
	            </tr>
	        </block>
	    </table>
	    
	    <a href="javascript:adminGroups.addScreen()" class="button">{addButton}</a>
	</div>
</div>