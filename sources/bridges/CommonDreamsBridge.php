<?php
/**
* RssBridgeCommonDreams
* Returns the newest articles
* 2015-04-03
*
* @name CommonDreams Bridge
* @homepage http://www.commondreams.org/
* @description Returns the newest articles.
* @maintainer nyutag
*/
class CommonDreamsBridge extends BridgeAbstract{
   
        public function collectData(array $param){

		function CommonDreamsUrl($string) {
		 $html2 = explode(" ", $string);
		 $string = $html2[2] . "/node/" . $html2[0];
		 return $string;
		}
	
		function CommonDreamsExtractContent($url) {
		$html3 = file_get_html($url);
		$text = $html3->find('div[class=field--type-text-with-summary]', 0)->innertext;
		return $text;
		}

		$html = file_get_html('http://www.commondreams.org/rss.xml') or $this->returnError('Could not request CommonDreams.', 404);
		$limit = 0;
		foreach($html->find('item') as $element) {
		 if($limit < 2) {
		 $item = new \Item();
		 $item->title = $element->find('title', 0)->innertext;
		 $item->uri = CommonDreamsUrl($element->find('guid', 0)->innertext);
		 $item->timestamp = strtotime($element->find('pubDate', 0)->plaintext);
		 $item->content = CommonDreamsExtractContent($item->uri);
		 $this->items[] = $item;
		 $limit++;
		 }
		}
    
    }

    public function getName(){
        return 'CommonDreams Bridge';
    }

    public function getURI(){
        return 'http://www.commondreams.org/';
    }

    public function getCacheDuration(){
//        return 3600*2; // 2 hours
	return 0;
    }
}
