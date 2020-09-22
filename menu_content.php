<?php

function set_menu_items()
{
    $categories = get_categories();
    foreach ($categories as $category) {
        if ($category->name != 'Uncategorized') {
            printf('<li><a href="%1$s">%2$s</a></li>',
                esc_url(get_category_link($category->term_id)),
                esc_html($category->name)
            );
        }
    }

    $kontakt = get_page_by_title('kontakt');
    printf('<li><a href="%1$s">%2$s</a></li>',
        esc_url(get_permalink($kontakt)),
        esc_html(get_the_title($kontakt))
    );
}
?>
