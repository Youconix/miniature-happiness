 <!-- admin view -->
 <div id="admin">
 	{[menuAdmin]}
 	
 	<div id="adminContent">
 		<div class="indexItem">
			<h2>{titleUpdates}</h2>

		    <div class="adminPanel" id="updateScreen">
		        {loadingUpdates}
		    </div>
		</div>
 	
 		{[securityView]}
		
		{[errorView]}
 	</div>
 </div>
