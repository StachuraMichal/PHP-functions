# PHP functions for Wordpress with ACF

- callback_functions.php
  - ```extract_images(&$post) ```   
    unpack images from zip file attached to `$post`  
    Add them to post as a content
    unattach zip file
    
- content_functions.php
  - ```chop_content($string, $post, $read_more = "", $mun_par = 12) ```   
    return chopped version of html `$string` with `$read_more` text and link to corresponding post
    
- search_functions.php
  - ```extract_queried($text, $query, $id, $read_more ="", $quotes_per_article = 5) ```   
    find first ` $quotes_per_article` paragraph with `$query` in html `$text` and return them with marked `$query` 
    
