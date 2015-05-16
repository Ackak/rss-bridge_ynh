<?php
/**
* RssBridgeTbib
* Returns images from given page
* 2014-05-25
*
* @name Tbib
* @homepage http://tbib.org/
* @description Returns images from given page
* @maintainer mitsukarenai
* @use1(p="page",t="tags")
*/
class TbibBridge extends BridgeAbstract{

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
        $html = file_get_html("http://tbib.org/index.php?page=post&s=list&tags=$tags&pid=$page") or $this->returnError('Could not request Tbib.', 404);


	foreach($html->find('div[class=content] span') as $element) {
		$item = new \Item();
		$item->uri = 'http://tbib.org/'.$element->find('a', 0)->href;
		$item->postid = (int)preg_replace("/[^0-9]/",'', $element->getAttribute('id'));	
		$item->timestamp = time();
		$item->thumbnailUri = $element->find('img', 0)->src;
		$item->tags = $element->find('img', 0)->getAttribute('alt');
		$item->title = 'Tbib | '.$item->postid;
		$item->content = '<a href="' . $item->uri . '"><img src="' . $item->thumbnailUri . '" /></a><br>Tags: '.$item->tags;
		$this->items[] = $item; 
	}
    }

    public function getName(){
        return 'Tbib';
    }

    public function getURI(){
        return 'http://tbib.org/';
    }

    public function getCacheDuration(){
        return 1800; // 30 minutes
    }
}
