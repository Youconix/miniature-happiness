<section class="item_header">
        <h1>{headerText}</h1>
        
        <nav>
            <ul>
                <li id="users_back">{buttonBack}</li>
                <li id="users_edit" data-id="{id}">{edit}</li>
                <li id="users_delete" data-id="{id}" data-username="{username}" data-userid="{userid}" {deleteRejected}>{delete}</li>
                <li id="user_login_as" data-id="{id}" data-username="{username}" data-userid="{userid}" {deleteRejected}>{loginAss}</li>
            </ul>
        </nav>
    </section>

    <section class="item_body">
        <table>
        <tbody>
        <tr>
            <td><label>{usernameHeader}</label></td>
            <td>{username}</td>
        </tr>
        <tr>
            <td><label>{emailHeader}</label></td>
            <td>{email}</td>
        </tr>
        <tr>
            <td><label>{botHeader}</label></td>
            <td>{bot}</td>
        </tr>
        <tr>
            <td><label>{registratedHeader}</label></td>
            <td>{registrated}</td>
        </tr>
        <tr>
          <td><label>{loggedinHeader}</label></td>
          <td>{loggedIn}</td>
        </tr>
        <tr>
            <td><label>{activeHeader}</label></td>
            <td>{active}</td>
        </tr>
        <tr>
            <td><label>{blockedHeader}</label></td>
            <td>{blocked}</td>
        </tr>
        </tbody>
        </table>
        
        <h2>Groups</h2>
        <article id="groupslist">
          <block {userGroup}>
              <fieldset>{name} - {level}</fieldset>
          </block>
        </article>
    </section>