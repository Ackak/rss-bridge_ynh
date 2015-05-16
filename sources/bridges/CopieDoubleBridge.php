<?php
/**
 *
 * @name CopieDouble
* @homepage http://www.copie-double.com/
 * @description CopieDouble
 * @update 12/12/2013
* initial maintainer: superbaillot.net
 */
class CopieDoubleBridge extends BridgeAbstract{

    public function collectData(array $param){
        $html = file_get_html('http://www.copie-double.com/') or $this->returnError('Could not request CopieDouble.', 404);
        $table = $html->find('table table', 2);
        
        foreach($table->find('tr') as $element)
        {
            $td = $element->find('td', 0);
             $cpt++;
            if($td->class == "couleur_1")
            {
                $item = new Item();
                
                $title = $td->innertext;
                $pos = strpos($title, "<a");
                $title = substr($title, 0, $pos);
                $item->title = $title;
            }
            elseif(strpos($element->innertext, "/images/suivant.gif") === false)
            {
                $a=$element->find("a", 0);
                $item->uri = "http://www.copie-double.com" . $a->href;
                
                $content = str_replace('src="/', 'src="http://www.copie-double.com/',$element->find("td", 0)->innertext);
                $content = str_replace('href="/', 'href="http://www.copie-double.com/',$content);
                $item->content = $content;
                $this->items[] = $item;
            }
        }
    }

    public function getName(){
        return 'CopieDouble';
    }

    public function getURI(){
        return 'http://www.copie-double.com';
    }

    public function getDescription(){
        return 'CopieDouble via rss-bridge';
    }

    public function getCacheDuration(){
        return 14400; // 4 hours
    }
}

?>
