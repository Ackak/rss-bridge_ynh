<?php
/**
* RssBridgeTwitterClean
* Based on https://github.com/mitsukarenai/twitterbridge-noapi
*
* @name Twitter Bridge Clean
* @homepage http://twitter.com/
* @description Returns user timelines without username in title or search results for keywords/hashtags (without using their API).
* @maintainer vinzv
* @update 2015-03-07
* @use1(q="keyword or #hashtag")
* @use2(u="username")
*/
class TwitterBridgeClean extends BridgeAbstract{
    
    private $request;

    public function collectData(array $param){
        $html = '';
        if (isset($param['q'])) {   /* keyword search mode */
            $this->request = $param['q'];
            $html = file_get_html('http://twitter.com/search/realtime?q='.urlencode($this->request).'+include:retweets&src=typd') or $this->returnError('No results for this query.', 404);
        }
        elseif (isset($param['u'])) {   /* user timeline mode */
            $this->request = $param['u'];
            $html = file_get_html('http://twitter.com/'.urlencode($this->request)) or $this->returnError('Requested username can\'t be found.', 404);
        }
        else {
            $this->returnError('You must specify a keyword (?q=...) or a Twitter username (?u=...).', 400);
        }

        foreach($html->find('div.js-stream-tweet') as $tweet) {
            $item = new \Item();
            $item->username = $tweet->getAttribute('data-screen-name');	// extract username and sanitize
            $item->fullname = $tweet->getAttribute('data-name'); // extract fullname (pseudonym)
            $item->avatar = $tweet->find('img', 0)->src;	// get avatar link
            $item->id = $tweet->getAttribute('data-tweet-id');	// get TweetID
            $item->uri = 'https://twitter.com'.$tweet->find('a.js-permalink', 0)->getAttribute('href');	// get tweet link
            $item->timestamp = $tweet->find('span.js-short-timestamp', 0)->getAttribute('data-time');	// extract tweet timestamp
		// processing content links
		foreach($tweet->find('a') as $link) {
			if($link->hasAttribute('data-expanded-url') ) {
				$link->href = $link->getAttribute('data-expanded-url');
			}
			$link->removeAttribute('data-expanded-url');
			$link->removeAttribute('data-query-source');
			$link->removeAttribute('rel');
			$link->removeAttribute('class');
			$link->removeAttribute('target');
			$link->removeAttribute('title');
			$link->removeAttribute('dir');
		}
            $item->content = str_replace('pic.twitter.com', 'https://pic.twitter.com', strip_tags($tweet->find('p.js-tweet-text', 0)->innertext));	// extract tweet text
            $item->title = $item->content;
            $this->items[] = $item;
        }
    }

    public function getName(){
        return (!empty($this->request) ? $this->request .' - ' : '') .'Twitter Bridge';
    }

    public function getURI(){
        return 'http://twitter.com';
    }

    public function getCacheDuration(){
        return 300; // 5 minutes
    }
}
