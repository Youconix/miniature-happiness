<section id="users">
    <section class="item_header">
        <h1 id="users_main_title">{{ $headerText }}</h1>
        
        <nav>
            <ul>
                <li id="user_login_as" data-id="{{ $user->getId() }}" data-username="{username}" data-userid="{{ $userid }}" {!! $deleteRejected !!}>{{ $loginAss }}</li>
                <li id="users_delete" data-id="{{ $user->getId() }}" data-username="{username}" data-userid="{{ $userid }}" {!! $deleteRejected !!}>{{ $delete }}</li>
                <li id="users_edit" data-id="{{ $user->getId() }}">{{ $edit }}</li>
                <li id="users_back">{{ $buttonBack }}</li>
            </ul>
        </nav>
    </section>

    <section class="item_body">
        <table>
        <tbody>
        <tr>
            <td><label>{{ $usernameHeader }}</label></td>
            <td>{{ $user->getUsername() }}</td>
        </tr>
        <tr>
            <td><label>{{ $emailHeader }}</label></td>
            <td>{{ $user->getEmail() }}</td>
        </tr>
        <tr>
            <td><label>{{ $botHeader }}</label></td>
            <td>@if( $user->isBot() )  
                    {{ $yes }} 
                @else 
                    {{ $no }} 
                @endif 
                </td>
        </tr>
        <tr>
            <td><label>{{ $registratedHeader }}</label></td>
            <td>@if( !empty($user->getRegistrated()) )
                {{ $localisation->dateOrTime($user->getRegistrated()) }}
                @else 
                    -
                @endif
                </td>
        </tr>
        <tr>
          <td><label>{{ $loggedinHeader }}</label></td>
          <td>@if( $user->getLastLogin()->getTimestamp() != 0 )
            {{ $localisation->dateOrTime($user->getLastLogin()) }}
            @else 
                -
            @endif
            </td>
        </tr>
        <tr>
            <td><label>{{ $activeHeader }}</label></td>
            <td>@if( $user->isEnabled() )
                    {{ $yes }}
                @else 
                    {{ $no }}
                @endif
                </td>
        </tr>
        <tr>
            <td><label>{{ $blockedHeader }}</label></td>
            <td>@if( $user->isBlocked() )
                    {{ $yes }}
                @else
                    {{ $no }}
                @endif
                </td>
        </tr>
        </tbody>
        </table>
        
        <h2>Groups</h2>
        <article id="groupslist">
          @foreach( $groups AS $group )
              <fieldset>{{ $group['name'] }} - {{ $group['level'] }}</fieldset>
          @endforeach
        </article>
    </section>
</section>
