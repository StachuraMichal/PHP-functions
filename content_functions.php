<?php

function chop_content($string, $post, $read_more = "", $mun_par = 12){
   
    $dom = new DOMDocument();
    $string =str_replace('charset=windows-1250', 'charset=utf-8', $string);
    @$dom->loadHTML($string);
    $i=0;
    $nodesToDelete = array();
    $word_count = 0;
    $paragraphs =$dom->getElementsByTagName('p') ;
    while(true)
    {
        $p = $paragraphs->item($i);
        $word_count += strlen($p->nodeValue);
 
        if ($i == $mun_par OR $word_count > 100*$mun_par )
        {
            $mun_par= $i;
            $last_par = preg_replace("/\s+/u", "", $p->nodeValue);
            if (strlen($last_par)<7 AND $i >2)
            {
                $i-=1;
                $mun_par--;
            }
            else
            { 
                $style = $p->getAttribute('style');
                $p->setAttribute('style', $style.'; background: -webkit-linear-gradient(#000 ,#0000) !important;-webkit-background-clip: text !important;-webkit-text-fill-color: transparent;');
                $i++;
                break;
            }
        }
        else  $i++; 

        if($i>=count($paragraphs))  break;
    }

    $cnt = count($paragraphs);
    $p = $paragraphs->item($i-1);
    while($p->nextSibling)    
        $p->parentNode->removeChild($p->nextSibling);

    $divs =$dom->getElementsByTagName('div') ;
    $firstSectionIncluded = false;
    foreach($divs as $div)
    {
        if(strpos($div->getAttribute('class'), 'WordSection') ===false or $firstSectionIncluded == true)  
            $nodesToDelete[] = $div; 
        else
            $firstSectionIncluded = true;
    }
    $spans = $dom->getElementsByTagName('span');
    
    foreach($spans as $span)
        if(has_div_parent($span) == false) $nodesToDelete[] = $span;

    foreach($nodesToDelete as $node)
        $node->parentNode->removeChild($node);
    
    add_readmore($dom, $post->ID);
    $result = $dom->saveHTML($dom->documentElement);
    return $result;  
}



function add_readmore(&$dom, $id,  $read_more = ""){
    if ($read_more == "") 
        $read_more = "<a class='readmore' href=". get_the_permalink( $id). '>Czytaj dalej <i class="fas fa-arrow-right"></i></a>';
    else
        $read_more = "<a class='readmore' href=". get_the_permalink( $id). '>'.$read_more.' <i class="fas fa-arrow-right"></i></a>';
    $read_element = new DOMDocument();
    @$read_element->loadHTML($read_more);

    $dom->getElementsByTagName('p')->item(0)->parentNode->appendChild(
        $dom->importNode($read_element->documentElement, TRUE)
      );

}

function anable_images(&$string, $post){
    $dom = new DOMDocument();
    $string =str_replace('charset=windows-1250', 'charset=utf-8', $string);
    @$dom->loadHTML($string);
    $images = $dom->getElementsByTagName('img');   

    $read_element = new DOMDocument();
    @$read_element->loadHTML($post->post_content);
    $content_images = $read_element->getElementsByTagName('img'); 

    $i = 0;
    foreach($images as $image)
    {
        $image->setAttribute('style', "margin-left: auto;margin-right: auto;");
        $alt = $image->getAttribute('alt');        

        if($content_images[$i])
        {
            $image->setAttribute('src', $content_images[$i]->getAttribute('src'));
            $i++;
        } 
        elseif(strpos($alt, 'http') !== false)
            $image->setAttribute('src', $alt);
    
    }
    $string = $dom->saveHTML($dom->documentElement);

}


function has_div_parent($obj)
{
    if ($obj->parentNode->tagName == 'div') return true;
    if ($obj->parentNode->tagName == 'body') return false;

    return has_div_parent($obj->parentNode);
}



?>