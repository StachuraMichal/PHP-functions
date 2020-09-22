<?php
function extract_queried($text, $query, $id, $read_more ="", $quotes_per_article = 5)
{
    anable_images($text, get_post($id));
    $dom = new DOMDocument();
    $text =str_replace('charset=windows-1250', 'HTML-ENTITIES', $text);
    @$dom->loadHTML($text);
    $cnt= 0;
    $nodesToDelete = array();
    $result_paragraphs = [];

    foreach($dom->getElementsByTagName('p') as $p)
    {
        if(strpos( strtolower($p->nodeValue), strtolower( $query)) === false  or $cnt >= $quotes_per_article )
               $nodesToDelete[] = $p;
        else{
            $result_paragraphs[] = $p;
            $cnt++;
        }
    }
    
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
  

    while ($divs[0]->hasChildNodes()) 
        $divs[0]->removeChild($divs[0]->firstChild);

    $read_element = new DOMDocument();

    foreach($result_paragraphs as $p) {
        
        $p_html = $dom->saveHTML($p);
        $p_html = add_marks(mb_convert_encoding($p_html, 'HTML-ENTITIES', 'UTF-8'), $query);
        
        @$read_element->loadHTML($p_html );
        $p_element = $dom->importNode($read_element->getElementsByTagName('p')->item(0), TRUE);
        $divs[0]->appendChild($p_element);
        $br = $dom->createElement('hr');
        $br->setAttribute('class', 'search-hr');
        $divs[0]->appendChild($br);
    }

 //   $text = $dom->saveHTML($dom->documentElement);
   // @$dom->loadHTML($text);
 //  print_r($divs[0]);
    if($dom->getElementsByTagName('p')) add_readmore($dom, $id, $read_more);
    $result = $dom->saveHTML($dom->documentElement);

   return $result;

}

function add_marks($string, $query)
{
    $pointer = 0; 
    $mark = "<mark>";
    $end_mark = "</mark>";
    $query_html = mb_convert_encoding($query, 'HTML-ENTITIES');
    for($i = 0; $i< 100; $i++)
        { 
            $pointer = mb_strpos( mb_strtolower ($string), mb_strtolower ($query_html), $pointer );
            if($pointer === false) break;  
            $string = substr_replace($string, $mark, $pointer, 0);
            $pointer += strlen($mark) + strlen($query_html);
            $string = substr_replace($string, $end_mark, $pointer , 0);
            $pointer += strlen($end_mark);
            
        }
    return $string;
}

?>