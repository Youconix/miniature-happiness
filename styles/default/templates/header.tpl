<header>
	<div id="headerLogo"><a href="{NIV}home.php"><img src="{style_dir}/images/logo.png" alt="Logo place holder"/></a></div>
	
	<div id="headerLanguages">
		<block {headerLanguage}>
			<a href="{url}">{language}</a> | 
		</block>
		{login}
		{logout}
	</div>
	
	<p class="textLeft" style="width:auto">{welcomeHeader}</p>
	
	<div id="headerSlogan">{slogan}</div>
</header>
