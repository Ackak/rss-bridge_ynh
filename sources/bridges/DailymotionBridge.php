<?php
/**
* RssBridgeDailymotion 
* Returns the newest videos
*
* @name Dailymotion Bridge
* @homepage https://www.dailymotion.com/
* @description Returns the 5 newest videos by username/playlist or search
* @maintainer mitsukarenai
* @update 2014-11-18
* @use1(u="username")
* @use2(p="playlist id")
* @use3(s="search keyword",pa="page")
* 
*/
class DailymotionBridge extends BridgeAbstract{
    
	private $request;
    
	public function collectData(array $param){

		function getMetadata($id) {
			$metadata=array();
			$html2 = file_get_html('http://www.dailymotion.com/video/'.$id) or $this->returnError('Could not request Dailymotion.', 404);
			$metadata['title'] = $html2->find('meta[property=og:title]', 0)->getAttribute('content');
			$metadata['timestamp'] = strtotime($html2->find('meta[property=video:release_date]', 0)->getAttribute('content') );
			$metadata['thumbnailUri'] = $html2->find('meta[property=og:image]', 0)->getAttribute('content');
			$metadata['uri'] = $html2->find('meta[property=og:url]', 0)->getAttribute('content');

			return $metadata;
		} 


        	$html = '';
		$limit = 5;
		$count = 0;

		if (isset($param['u'])) {   // user timeline mode
			$this->request = $param['u'];
			$html = file_get_html('http://www.dailymotion.com/user/'.urlencode($this->request).'/1') or $this->returnError('Could not request Dailymotion.', 404);
		}
		else if (isset($param['p'])) {    // playlist mode
			$this->request = strtok($param['p'], '_');
			$html = file_get_html('http://www.dailymotion.com/playlist/'.urlencode($this->request).'') or $this->returnError('Could not request Dailymotion.', 404);
		}
		else if (isset($param['s'])) {   // search mode
			$this->request = $param['s']; $page = 1; if (isset($param['pa'])) $page = (int)preg_replace("/[^0-9]/",'', $param['pa']); 
			$html = file_get_html('http://www.dailymotion.com/search/'.urlencode($this->request).'/'.$page.'') or $this->returnError('Could not request Dailymotion.', 404);
		}
		else {
			$this->returnError('You must either specify a Dailymotion username (?u=...) or a playlist id (?p=...) or search (?s=...)', 400);
		}

		foreach($html->find('div.media a.preview_link') as $element) {
			if($count < $limit) {
				$item = new \Item();
				$item->id = str_replace('/video/', '', strtok($element->href, '_'));
				$metadata = getMetadata($item->id);
				$item->uri = $metadata['uri'];
				$item->thumbnailUri = $metadata['thumbnailUri'];
				$item->title = $metadata['title'];
				$item->timestamp = $metadata['timestamp'];
				$item->content = '<a href="' . $item->uri . '"><img src="' . $item->thumbnailUri . '" /></a><br><a href="' . $item->uri . '">' . $item->title . '</a>';
				$this->items[] = $item;
				$count++;
			}
		}
	}

	public function getName(){
		return (!empty($this->request) ? $this->request .' - ' : '') .'Dailymotion Bridge';
	}

	public function getURI(){
		return 'https://www.dailymotion.com/';
	}

	public function getCacheDuration(){
		return 3600*3; // 3 hours
	}
}
