<?php
function my_get_post_numbers($title)
{
    $output_array = array('post_num' => '', 'cat_num' => '');
    $str_array = str_split($title);
    $last_letter = '';
    $first_number = "";
    $second_number = "";
    foreach ($str_array as $letter) {
        if (is_numeric($letter)) {
            if ($last_letter == '-' or $second_number != '') {
                $second_number = $second_number . $letter;
            } else {
                $first_number = $first_number . $letter;
            }

        } elseif ($letter != '.' and $letter != '-') {
            break;
        }

        $last_letter = $letter;
    }
    if ($second_number == '') {
        $output_array['post_num'] = intval($first_number);
    } else {
        $output_array['cat_num'] = intval($first_number);
        $output_array['post_num'] = intval($second_number);
    }

    return $output_array;
}

function automatic_numbering()
{
    $query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => -1,
    ));
    foreach ($query->posts as $post) {
        set_cat_and_post_munbers($post);
    }
}

function set_cat_and_post_munbers(&$post)
{
    $post_title = get_field('content', $post->ID)['title'];
    $post_numbers = my_get_post_numbers($post_title);
    get_field('article_number', $post->ID);

    update_field('article_number', $post_numbers['post_num'], $post->ID);
    update_field('category_number', $post_numbers['cat_num'], $post->ID);
}
?>
