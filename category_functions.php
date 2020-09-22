<?php


function get_category_posts_query($args)
{
    if(!$args['order'])
    {
        if($args['category']->name == 'Zbroja') 
            $args['order'] = "ASC";
        else 
            $args['order'] = "DESC";
    }
    $query_args = array(
        'post_type' => 'post',
        'cat' => $args['category']->cat_ID, 
        'post_type' =>  'post', 
        'meta_key' => 'article_number',
        'orderby' => 'meta_value_num',
        'order' => $args['order'],
        'paged' => get_query_var( 'paged' )
    );
    if ($args['all_posts']) 
        $query_args['posts_per_page'] = -1;

    return new WP_Query($query_args);
}
?>