<section id="menu_wrapper">
        <section class="tab_header">
            @foreach( $menu_tab_header AS $header)
                <div class="{{ $header->class }}" data-id="{{ $header->id }}">
                    {{ $header->title }}
                </div>
            @endforeach
         </section>
        
        <section class="tab_content">
            @foreach( $menu_tab_content AS $item )
                <article id="tab_{{ $item->id }}">
                    @foreach( $item->items AS $menuItem )
                        <div class="menu_item" id="{{ $menuItem->item_id }}">
			  <h2><a href="{!! $menuItem->path !!}">{{ $menuItem->title }}</a></h2>
                            
                          @foreach( $menuItem->links AS $link )
			    <p id="{{ $link->link_id }}"><a href="{!! $link->path !!}">{{ $link->link_title }}</a></p>
                          @endforeach
                        </div>
                    @endforeach
                </article>
            @endforeach
        </section>
</section>    
