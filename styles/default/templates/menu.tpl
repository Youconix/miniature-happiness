<nav id="menu">
	<ul>
		<li><a href="{LEVEL}index/view">{home}</a></li>
		<if {menuAdmin}>
		  <li><a href="{LEVEL}admin/index/view">{adminPanel}</a></li>
		</if>
		<if {menuLoggedIn}>
		  <li><a href="{LEVEL}logout/index">{logout}</a></li>
		</if>
		<else>
		  <li><a href="{LEVEL}authorization/normal/login_screen">{login}</a></li>
		  <li><a href="{LEVEL}authorization/normal/registration_screen">{registration}</a></li>
		</else>
	</ul>
	
</nav>
