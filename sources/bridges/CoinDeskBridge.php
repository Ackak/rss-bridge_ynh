<?php
/**
* RssBridgeCoinDesk 
* Returns the 5 newest posts from coindesk.com (full text)
*
* @name CoinDesk
* @homepage http://www.coindesk.com/
* @description Returns the 5 newest posts from CoinDesk (full text)
* @maintainer mitsukarenai
* @update 2014-05-30
*/
class CoinDeskBridge extends BridgeAbstract{

    public function collectData(array $param){

    function CoinDeskStripCDATA($string) {
    	$string = str_replace('<![CDATA[', '', $string);
    	$string = str_replace(']]>', '', $string);
    	return $string;
    }
    function CoinDeskExtractContent($url) {
	$html2 = file_get_html($url);
	$text = $html2->find('div.single-content', 0)->innertext;
	$text = strip_tags($text, '<p><a><img>');
	return $text;
    }
        $html = file_get_html('http://www.coindesk.com/feed/atom/') or $this->returnError('Could not request CoinDesk.', 404);
	$limit = 0;

	foreach($html->find('entry') as $element) {
	 if($limit < 5) {
	 $item = new \Item();
	 $item->title = CoinDeskStripCDATA($element->find('title', 0)->innertext);
	 $item->author = $element->find('author', 0)->plaintext;
	 $item->uri = $element->find('link', 0)->href;
	 $item->timestamp = strtotime($element->find('published', 0)->plaintext);
	 $item->content = CoinDeskExtractContent($item->uri);
	 $this->items[] = $item;
	 $limit++;
	 }
	}
    
    }

    public function getName(){
        return 'CoinDesk';
    }

    public function getURI(){
        return 'http://www.coindesk.com/';
    }

    public function getCacheDuration(){
        return 1800; // 30min
    }
}
