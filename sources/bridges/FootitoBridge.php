<?php
/**
*
* @name Footito
* @homepage http://www.footito.fr/
* @description Footito
* @update 21/11/2013
* initial maintainer: superbaillot.net
*/
class FootitoBridge extends BridgeAbstract{

    public function collectData(array $param){
        $html = file_get_html('http://www.footito.fr/') or $this->returnError('Could not request Footito.', 404);
    
        foreach($html->find('div.post') as $element) {
            $item = new Item();
            
            $content = trim($element->innertext);
            $content = str_replace("<img", "<img style='float : left;'", $content );
            $content = str_replace("class=\"logo\"", "style='float : left;'", $content );
            $content = str_replace("class=\"contenu\"", "style='margin-left : 60px;'", $content );
            $content = str_replace("class=\"responsive-comment\"", "style='border-top : 1px #DDD solid; background-color : white; padding : 10px;'", $content );
            $content = str_replace("class=\"jaime\"", "style='display : none;'", $content );
            $content = str_replace("class=\"auteur-event responsive\"", "style='display : none;'", $content );
            $content = str_replace("class=\"report-abuse-button\"", "style='display : none;'", $content );
            $content = str_replace("class=\"reaction clearfix\"", "style='margin : 10px 0px; padding : 5px; border-bottom : 1px #DDD solid;'", $content );
            $content = str_replace("class=\"infos\"", "style='font-size : 0.7em;'", $content );
            
            $item->content = $content;
            
            $title = $element->find('.contenu .texte ', 0)->plaintext;
            $item->title = $title;
            
            $info = $element->find('div.infos', 0);
            
            $item->timestamp = strtotime($info->find('time', 0)->datetime);
            $item->name = $info->find('a.auteur', 0)->plaintext;
            
            $this->items[] = $item;
        }
    }

    public function getName(){
        return 'footito';
    }

    public function getURI(){
        return 'http://www.footito.fr/';
    }

    public function getCacheDuration(){
        return 3600; // 1h hours
    }
    public function getDescription(){
        return "Footito via rss-bridge";
    }
}
?>
