<section id="groups">
    <section class="item_header">
	   <h1 id="groups_main_title">{{ $groupTitle }}</h1>
	</section>
	
	<section class="item_body">
	    <table>
	    <thead>
	        <tr>
	            <td>{{ $headerID }}</td>
	            <td>{{ $headerName }}</td>
	            <td>{{ $headerDescription }}</td>
	            <td>{{ $headerAutomatic }}</td>
	        </tr>
	    </thead>
	    <tbody>
	        @foreach( $groups AS $group )
	            <tr data-id="{{ $group['id'] }}" class="group_editable">
	                <td>{{ $group['id'] }}</td>
	                <td>{{ $group['name'] }}</td>
	                <td>{{ $group['description'] }}</td>
	                <td>{{ $group['default'] }}</td>
	            </tr>
	        @endforeach
	    </tbody>
	    </table>
	    
	    <p><input type="button" id="groupAddButton" value="{{ $addButton }}"></p>
	</section>
</section>
