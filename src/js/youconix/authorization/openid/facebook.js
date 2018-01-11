function Facebook(){}
Facebook.prototype.prepare  = function(){
  code = '/* Load namespace */ '+
	'	var head	= document.getElementsByTagName("html")[0];  '+
	'	head.setAttribute("xmlns:fb","http://ogp.me/ns/fb#");		 '+		
				
	'	window.fbAsyncInit = function() { '+
	'		/* Load main tag */ '+
	'		var container	= document.getElementsByTagName("body")[0]; '+
			
	'		var fb_root	= document.createElement("div"); '+
	'		fb_root.id	= "fb-root"; '+
	'	  	container.insertBefore(fb_root,container.firstChild); '+
				
	'	    // init the FB JS SDK '+
  '  		FB.init({ '+
	'	      appId      : "'+facebook_settings['appID']+'", // App ID from the App Dashboard '+
	'	      channelUrl : "'+facebook_settings['channelUrl']+'", // Channel File for x-domain communication '+
	'	      status     : true, // check the login status upon init? '+
	'	      cookie     : true, // set sessions cookies to allow your server to access the session?		        '+
  '    		  oauth: true, '+
	'	      xfbml      : true  // parse XFBML tags on this page? '+
	'	  }); '+
		  
	'		FB.getLoginStatus(function(response) { '+
	'  			if (response.status === "connected" ){ '+
	'  				// logged in '+
	'			    '+facebook_settings['loggedin']+
	'  			}  '+
	'  			else if (response.status === "not_authorized"){ '+
	'			    // 	not_authorized '+
	'			    ' +facebook_settings['unauthorized']+
	'  			} '+
	'  			else { '+
	'			    // not_logged_in '+
	'			    ' + facebook_settings['loggedout']+
	'  			}	 '+
 	'		}); '+
  '		}; '+
  		
  '		function login() { '+
	'	    FB.login(function(response) { '+
	'	        if( response.authResponse ){ '+
	'	            ' +facebook_settings['loggedin']+
	'	        } else { '+
	'	            window.location = "index.php"; '+
	'	        } '+
	'	    }); '+
	'	} '+

	'	  // Load the SDK\'s source Asynchronously '+
	'	  // Note that the debug version is being actively developed and might  '+
	'	  // contain some type checks that are overly strict.  '+
	'	  // Please report such bugs using the bugs tool. '+
	'	  (function(d, debug){ '+
	'	     var js, id = "facebook-jssdk", ref = d.getElementsByTagName("script")[0]; '+
	'     if (d.getElementById(id)) {return;} '+
	'     js = d.createElement("script"); js.id = id; js.async = true; '+
	'     js.src = "//connect.facebook.net/'+facebook_settings['locale']+ '/all" + (debug ? "/debug" : "") + ".js"; '+
	'     ref.parentNode.insertBefore(js, ref); '+
  '     }(document, /*debug*/ false)); ';
      
  $('head').append('<script><!-- '+code+'//--></script>');
}
Facebook.prototype.setLogin = function(token,userid){
  
}

facebook = new Facebook;
facebook.prepare();