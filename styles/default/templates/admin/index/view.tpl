 <!-- admin view -->
 <section id="admin">
 	{[menuAdmin]}
 	
 	<section id="adminContent">
 		<section class="indexItem">
			<h2>{titleUpdates}</h2>

		    <section id="updateScreen">
		        {loadingUpdates}
		    </section>
		</section>
 	
 		{[securityView]}
		
		{[errorView]}
 	</section>
 </section>

 <script>
 <!--
 var tabs = new Tabs();
 $(document).ready(function(){
	tabs.init({'id':'admin_panel'});
 });
 //-->
 </script>