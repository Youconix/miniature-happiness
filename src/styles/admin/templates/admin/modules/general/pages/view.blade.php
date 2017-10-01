<section id="pages" data-url="{{ $url }}">
    <section class="item_header">
       <h1 id="pages_main_title">{{ $pageTitle }}</h1>
       
       <nav>
        <ul>
            <li id="pages_delete">{{ $buttonDelete }}</li>
            <li id="pages_back">{{ $buttonBack }}</li>
        </ul>
      </nav>
    </section>
    
    <section class="item_body">
        <h2>{{ $name }}</h2>
        
        <article id="current_rights">        
       		<h3>{{ $generalRightsHeader }}</h3>
       		
       		<fieldset>
	            <label class="label">{{ $groupLabel }}</label>
	            <select id="pages_group">	            	
	            	<option value="">{{ $groupDefault }}</option>
	            @foreach( $groups AS $group )
                        <option value="{{ $group['value'] }}" {!! $group['selected'] !!}>{{ $group['text'] }}</option>
	            @endforeach
	            </select>
	        </fieldset>
	        <fieldset>
	            <label class="label">{{ $accessLevelLabel }}</label>
	            <select id="pages_accesslevel">
	            	<option value="">{{ $viewRightsDefault }}</option>
	                @foreach( $pageRight AS $right )
	                    <option value="{{ $right['value'] }}" {!! $right['selected'] !!}>{{ $right['text'] }}</option>
	                @endforeach
	            </select>
	        </fieldset>
	        
	        <fieldset>
	            <input type="button" id="pages_update" value="{{ $save }}">
	            <input type="button" id="pages_reset" value="{{ $reset }}">
	        </fieldset>
        </article>
        
        <article id="currentViewRights">
        	<h2>{{ $viewRightsTitle }}</h2>
        	
        	<div>
		        <table id="right_list" data-styledir="/{{ $shared_style_dir }}">
		        <thead>
		        <tr>        
		        	<td width="50px"></td>
		        	<td>{{ $viewLabel }}</td>
		        	<td>{{ $groupLabel }}</td>
		        	<td>{{ $accessLevelLabel }}</td>
		        </tr>
		        </thead>
		        <tbody>
		        @foreach( $template_rights AS $right )
		        <tr data-template="{{ $right['command'] }}" data-id="{{ $right['id'] }}" id="view_{{ $right['id'] }}">        
		            <td style="width:50px"><img src="{{ $NIV }}{{ $shared_style_dir }}/images/icons/delete.png" alt="{{ $delete }}" title="{{ $delete }}"></td>
		            <td>{{ $right['command'] }}</td>
		            <td>{{ $right['group'] }</td>
		            <td>{{ $right['level'] }}</td>
		        </tr>
		        @endforeach
		        </tbody>
		        </table>
		    </div>
		   	<div>
				<fieldset>
		            <label class="label">{{ $viewLabel }}</label>
		            <input type="text" id="view_name">
		        </fieldset>
		        <fieldset>
		            <label class="label">{{ $accessLevelLabel }}</label>
		               <select id="template_level">
		               	<option value="">{{ $viewRightsDefault }}</option>
		                @foreach( $templateRights AS $right )
		                    <option value="{{ $right['value'] }}" {!! $right['selected'] !!}>{{ $right['text'] }}</option>
		                @endforeach
		            </select>
		        </fieldset>
		        <fieldset>
		        	<label class="label">{{ $groupLabel }}</label>
		        	<select id="viewGroups">
                                    <option value="">{{ $groupDefault }}</option>
		        	@foreach( $groups2 AS $group )
			            <option value="{{ $group['value'] }}" {!! $group['selected'] !!}>{{ $group['text'] }}</option>
			       @endforeach
		            </select>
		        </fieldset>
		        <fieldset>
		            <input type="button" id="pages_template_add" value="{{ $add }}">
		        </fieldset>
			</div>
		</article>
    </section>
 </section>
