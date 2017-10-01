 @extends layouts/admin.blade.php

@section('body_content')
    <section id="admin_panel">
 	@include('menu_admin.blade.php')
 	
 	<section id="adminContent">
 	</section>
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
 @endsection
