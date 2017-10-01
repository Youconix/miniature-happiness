<nav id="menu">
	<ul>
		<li><a href="{{ $LEVEL }}index/view">{{ $home }}</a></li>
		@if( $menuAdmin )
		  <li><a href="{{ $LEVEL }}admin/index/view">{{ $adminPanel }}</a></li>
		@endif
		@if( $menuLoggedIn )
		  <li><a href="{{ $LEVEL }}login/logout">{{ $logout }}</a></li>
		@else
		  <li><a href="{{ $LEVEL }}login/index">{{ $login }}</a></li>
		  <li><a href="{{ $LEVEL }}registration/index">{{ $registration }}</a></li>
		@endif
	</ul>
	
</nav>
