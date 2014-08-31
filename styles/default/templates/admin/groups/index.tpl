<section id="groups">
	<h1>{groupTitle}</h1>
	
	<section>
	    <table>
	    <thead>
	        <tr>
	            <td>{headerID}</td>
	            <td>{headerName}</td>
	            <td>{headerDescription}</td>
	            <td>{headerAutomatic}</td>
	            <td colspan="3"></td>
	        </tr>
	    </thead>
	    <tbody>
	        <block {groupBlocked}>
	            <tr>
	                <td>{id}</td>
	                <td>{name}</td>
	                <td>{description}</td>
	                <td>{default}</td>
	                <td><img src="{style_dir}images/icons/edit_grey.png" alt="{buttonEdit}" title="{buttonEdit}"></td>
	                <td><img src="{style_dir}images/icons/delete_grey.png" alt="{buttonDelete}" title="{buttonDelete}"></td>
	                <td><a href="javascript:adminGroups.viewUsers({id})" class="button">{viewUsers}</a></td>
	            </tr>
	        </block>
	        <block {group}>
	            <tr>
	                <td>{id}</td>
	                <td>{name}</td>
	                <td>{description}</td>
	                <td>{default}</td>
	                <td><a href="javascript:adminGroups.edit({id})"><img src="{style_dir}images/icons/edit.png" alt="{buttonEdit}" title="{buttonEdit}"></a></td>
	                <td><a href="javascript:adminGroups.deleteGroup({id})"><img src="{style_dir}images/icons/delete.png" alt="{buttonDelete}" title="{buttonDelete}"></a></td>
	                <td><a href="javascript:adminGroups.viewUsers({id})" class="button">{viewUsers}</a></td>
	            </tr>
	        </block>
	    </tbody>
	    </table>
	    
	    <a href="javascript:adminGroups.addScreen()" class="button">{addButton}</a>
	</section>
</section>