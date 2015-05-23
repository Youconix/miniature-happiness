<header>
	<div id="headerLogo"><a href="{LEVEL}"><img src="/{style_dir}/images/logo.png" alt=""/></a></div>
	
	<section id="headerLanguages">
	   <ul>
		<block {headerLanguage}>
			<li class="language_icon"><a href="javascript:switchLanguage('{code}')"><img src="{NIV}{style_dir}images/flags/{code}.png" alt="{language}" title="{language}" class="icon"></a></li> 
		</block>
		</ul>
	</section>
	
	<p class="textLeft" style="width:auto">{welcomeHeader}</p>
	
	<script>
    <!--
    function switchLanguage(language){
        $.get("{LEVEL}index.php?lang="+language,function(){
            location.reload();
        });
    }
    //-->
    </script>
</header>
