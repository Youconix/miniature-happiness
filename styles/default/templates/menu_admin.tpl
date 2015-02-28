<section id="admin_panel">
        <section class="tab_header">
            <block {menu_tab_header}>
                <div class="{class}" data-id="{id}">
                    {title}
                </div>
            </block>
         </section>
        
        <section class="tab_content">
            <block {menu_tab_content}>
                <article id="tab_{id}">
                    <block {tab_{id}}>
                        <div class="menu_item" id="{item_id}">
                            <h2><img src="{NIV}{icon}" alt="">{title}</h2>
                            
                            <block {link_{name}}>
                                <p id="{link_id}">{link_title}</p>
                            </block>
                        </div>
                    </block>
                </article>
            </block>
        </section>
</section>    