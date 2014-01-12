<section id="users">
    <h1>{headerText}</h1>

	<section>
		<h2>{searchTitle}</h2>
	    <fieldset>
	    	<input type="text" id="searchUsername"/><a href="javascript:adminUsers.search()" class="button">{searchText}</a>
	   	</fieldset>
	
		<p><a href="javascript:adminUsers.newUser()" class="button">{textAdd}</a></p>
		
		<h2>{headerNick}</h2>
		
	    <table>
	    <tbody>
	        <block {users}>
	            <tr id="row_{id}">
	            	<td>{id}</td>
	            	<td>{nick}</td>
	            	<td>{email}</td>
	                <td><a href="javascript:adminUsers.viewUser({id})"><img src="{style_dir}images/icons/view.png" alt="{view}" title="{view}"/></a>
	                	<a href="javascript:adminUsers.editUser({id})"><img src="{style_dir}images/icons/edit.png" alt="{edit}" title="{edit}"/></a>
	                	<a href="javascript:adminUsers.deleteUser({id},{userid})"><img src="{style_dir}images/icons/delete.png" alt="{delete}" title="{delete}"/></a></td>
	            </tr>
	        </block>
	    <tr>
	    	<td colspan="4"><br/></td>
	    </tr>
	    <tr>
	    	<td colspan="4"><a href="javascript:adminUsers.newUser()" class="button">{textAdd}</a></td>
	    </tr>
	    </tbody>
	    </table>
	</section>
</section>