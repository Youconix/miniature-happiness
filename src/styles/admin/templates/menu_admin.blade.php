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
                            <h2>{{ $menuItem->title }}</h2>
                            
                            @foreach( $menuItem->links AS $link )
                                <p id="{{ $link->link_id }}">{{ $link->link_title }}</p>
                            @endforeach
                        </div>
                    @endforeach
                </article>
            @endforeach
        </section>
</section>    
