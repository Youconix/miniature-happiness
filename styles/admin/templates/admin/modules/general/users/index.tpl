<section id="users">
    <section class="item_header">
        <h1 id="users_main_title">{headerText}</h1>
    </section>

  <section class="item_body">
    <fieldset>
      <input type="text" id="searchUsername" name="username" placeholder="{searchText}" autocomplete="off">
    </fieldset>
	
		<p><input type="button" id="newUserButton2" value="{textAdd}"></p>

	    <table id="usertable">
	    <thead>
	      <tr>
		<td>{header_ID}</td>
		<td>{header_username}</td>
		<td>{header_email}</td>
		<td>{header_loggedin}</td>
		<td>{header_registration}</td>
	      </tr>
	    </thead>
	    <tbody>
	        <block {users}>
	            <tr data-id="{id}">
	            	<td>{id}</td>
	            	<td>{nick}</td>
	            	<td>{email}</td>
	            	<td>{logged_in}</td>
	            	<td>{registration}</td>
	            </tr>
	        </block>
	    <tr>
	    	<td colspan="4"><br/></td>
	    </tr>
	    </tbody>
	    </table>
	    
	    <p><input type="button" id="newUserButton2" value="{textAdd}"></p>
	</section>
</section>