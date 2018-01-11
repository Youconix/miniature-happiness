<section id="users">
  <section class="item_header">
    <h1 id="users_main_title">{{ $headerText }}</h1>

    <nav>
      <ul>
	<li id="users_delete" data-id="{{ $user->getId() }}" data-username="{{ $user->getUsername() }}" data-userid="{{ $userid }}" {!! $deleteRejected !!}>{{ $delete }}</li>
	<li id="users_back">{{ $buttonBack }}</li>
      </ul>
    </nav>
  </section>

  <section class="item_body">
    <fieldset>
      <label class="label">{{ $usernameHeader }}</label>
      {{ $user->getUsername() }}
    </fieldset>
    <fieldset>
      <label for="email" class="label">{{ $emailHeader }}</label>
      <input type="email" id="email" name="email" value="{{ $user->getEmail() }}" data-validation="{{ $emailError }}" required>
    </fieldset>
    <fieldset>
      <label for="bot" class="label">{{ $botHeader }}</label>
      <input type="radio" name="bot" id="bot_0" value="0" @if( !$user->isBot() ) <?php echo('checked="checked"'); ?> @endif > <label>{{ $no }}</label>
      <input type="radio" name="bot" id="bot_1" value="1" @if( $user->isBot() ) <?php echo('checked="checked"'); ?> @endif> <label>{{ $yes }}</label>
    </fieldset>
    <fieldset>
      <label class="label">{{ $registratedHeader }}</label>
      @if( $user->getRegistrated()->getTimestamp() == 0 ) <?php echo '-'; ?> @else {!! $localisation->dateOrTime($user->getRegistrated()) !!} @endif
    </fieldset>
    <fieldset>
      <label class="label">{{ $loggedinHeader }}</label>
      {!! $localisation->dateOrTime($user->getLastLogin()) !!}
    </fieldset>
    <fieldset>
      <label class="label">{{ $activeHeader }}</label>
      @if( $user->isEnabled() ) {{ $yes }} @else {{ $no }} @endif
      {!! $localisation->dateOrTime($user->getRegistrated()) !!}
    </fieldset>
    <fieldset>
      <label for="blocked" class="label">{{ $blockedHeader }}</label>
      <input type="radio" name="blocked" id="blocked_0" value="0" @if( !$user->isBlocked() ) <?php echo('checked="checked"'); ?> @endif > <label>{{ $no }}</label>
      <input type="radio" name="blocked" id="blocked_1" value="1" @if( $user->isBlocked() ) <?php echo('checked="checked"'); ?> @endif > <label>{{ $yes }}</label>
    </fieldset>
    @if( $user->getLoginType() == 'normal' )
    <h2>{{ $passwordChangeHeader }}</h2>
    <h3>{{ $passwordChangeText }}</h3>

    <fieldset>
      <label for="password1" class="label">{{ $passwordHeader }}</label>
      <input type="password" name="password1" id="password1" data-validation="{{ $passwordError }}">
    </fieldset>
    <fieldset>
      <label for="password2" class="label">{{ $passwordRepeatHeader }}</label>
      <input type="password" name="password2" id="password2" data-validation="{{ $passwordError }}">
    </fieldset>
    @endif
    
    <fieldset>
      <input type="hidden" id="userid" value="{id}">
      <input type="submit" value="{{ $updateButton }}" id="userUpdateButton">
    </fieldset>
    
    <h2>Groups</h2>

    <article id="groupslist">
      @foreach( $groups AS $id => $group )
      @if( $group['blocked'] )
      <fieldset>{{ $group['name'] }} - {{ $group['level'] }}</fieldset>                    
      @else 
      <fieldset>{{ $group['name'] }} - {{ $group['level'] }} <img src="/{{ $shared_style_dir }}images/icons/delete.png" alt="{{ $delete }}" title="{{ $delete }}" class="delete" data-id="{{ $user->getId() }}" data-group="{{ $id }}" data-level="{{ $group['levelNr'] }}"></fieldset>
      @endif
      @endforeach
    </article>
    
    <h2>Add group</h2>

    <fieldset>
      <select id="newGroup" data-id="{{ $user->getID() }}">
	<option value="">Choose a group</option>
        @foreach( $newGroups AS $group )
	<option value="{{ $group['value'] }}">{{ $group['text'] }}</option>
        @endforeach
      </select>

      <select id="newLevel">
	<option value="">Select an access level</option>

	@foreach( $levels AS $level )
	<option value="{{ $level['value'] }}">{{ $level['text'] }}</option>
	@endforeach
      </select>        
    </fieldset>

    <script>
      <!--
    var styleDir = '{{ $NIV }}{{ $style_dir }}';
-->
    </script>
  </section>
</section>
