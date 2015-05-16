<?php
/**
*
* @name Blagues De Merde
* @homepage http://www.blaguesdemerde.fr/
* @description Blagues De Merde
* @update 16/10/2013
* initial maintainer: superbaillot.net
*/
class BlaguesDeMerdeBridge extends BridgeAbstract{

    public function collectData(array $param){
        $html = file_get_html('http://www.blaguesdemerde.fr/') or $this->returnError('Could not request BDM.', 404);
    
        foreach($html->find('article.joke_contener') as $element) {
            $item = new Item();
            $temp = $element->find('a');
            if(isset($temp[2]))
            {
                $item->content = trim($element->find('div.joke_text_contener', 0)->innertext);
                $uri = $temp[2]->href;
                $item->uri = $uri;
                $item->title = substr($uri, (strrpos($uri, "/") + 1));
                $date = $element->find("li.bdm_date",0)->innertext;
                $time = mktime(0, 0, 0, substr($date, 3, 2), substr($date, 0, 2), substr($date, 6, 4));
                $item->timestamp = $time;
                $item->name = $element->find("li.bdm_pseudo",0)->innertext;;
                $this->items[] = $item;
            }
        }
    }

    public function getName(){
        return 'blaguesdemerde';
    }

    public function getURI(){
        return 'http://www.blaguesdemerde.fr/';
    }

    public function getCacheDuration(){
        return 7200; // 2h hours
    }
    public function getDescription(){
        return "Blagues De Merde via rss-bridge";
    }
}
?>
