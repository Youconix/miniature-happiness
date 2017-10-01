<header>
	<div id="headerLogo"><a href="{{ $LEVEL }}"><img src="/{{ $style_dir }}/images/logo.png" alt=""/></a></div>
	
	<section id="headerLanguages">
	   <ul>
	      @foreach( $headerLanguage AS $header )
		<li class="language_icon"><a href="javascript:switchLanguage('{{ $header['code'] }}')"><img src="{{ $NIV }}{{ $shared_style_dir }}images/flags/{{ $header['code'] }}.png" alt="{{ $header['language'] }}" title="{{ $header['language'] }}" class="icon"></a></li> 
	      @endforeach
		</ul>
	</section>
	
	<p class="textLeft" style="width:auto">{!! $welcomeHeader !!}</p>
	
	<script>
    <!--
    function switchLanguage(language){
        $.get("{{ $LEVEL }}index.php?lang="+language,function(){
            location.reload();
        });
    }
    //-->
    </script>
</header>
