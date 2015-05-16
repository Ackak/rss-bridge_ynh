<?php
/**
* RssBridgeRule34
* Returns images from given page
* 2014-05-25
*
* @name Rule34
* @homepage http://rule34.xxx/
* @description Returns images from given page
* @maintainer mitsukarenai
* @use1(p="page",t="tags")
*/
class Rule34Bridge extends BridgeAbstract{

    public function collectData(array $param){
	$page = 0;$tags='';
        if (isset($param['p'])) { 
		$page = (int)preg_replace("/[^0-9]/",'', $param['p']); 
		$page = $page - 1;
		$page = $page * 50;
        }
        if (isset($param['t'])) { 
            $tags = urlencode($param['t']); 
        }
        $html = file_get_html("http://rule34.xxx/index.php?page=post&s=list&tags=$tags&pid=$page") or $this->returnError('Could not request Rule34.', 404);


	foreach($html->find('div[class=content] span') as $element) {
		$item = new \Item();
		$item->uri = 'http://rule34.xxx/'.$element->find('a', 0)->href;
		$item->postid = (int)preg_replace("/[^0-9]/",'', $element->getAttribute('id'));	
		$item->timestamp = time();
		$item->thumbnailUri = $element->find('img', 0)->src;
		$item->tags = $element->find('img', 0)->getAttribute('alt');
		$item->title = 'Rule34 | '.$item->postid;
		$item->content = '<a href="' . $item->uri . '"><img src="' . $item->thumbnailUri . '" /></a><br>Tags: '.$item->tags;
		$this->items[] = $item; 
	}
    }

    public function getName(){
        return 'Rule34';
    }

    public function getURI(){
        return 'http://rule34.xxx/';
    }

    public function getCacheDuration(){
        return 1800; // 30 minutes
    }
}
