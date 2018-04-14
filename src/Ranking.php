<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/11/29
 * Time: 19:14
 */

namespace NiconicoPHP;

use PicoFeed\Reader\Reader;
use PicoFeed\Config\Config;


class Ranking {
	
	const ranking_api_base = 'http://www.nicovideo.jp/ranking/:type/:period/:category?rss=2.0';
	
	private $type;
	
	private $period;
	
	private $category;
	
	public function __construct ($a_category = 'all', $a_period = 'total', $a_type = 'fav') {
		$this->set_category($a_category);
		$this->set_period($a_period);
		$this->set_type($a_type);
	}
	
	public function set_category ($a_category) {
		if (!RankingCategory::has($a_category))
			throw new \InvalidArgumentException();
		
		$this->category = $a_category;
	}
	
	public function set_period ($a_period) {
		if (!RankingPeriod::has($a_period))
			throw new \InvalidArgumentException();
		
		$this->period = $a_period;
	}
	
	public function set_type ($a_type) {
		if (!RankingType::has($a_type))
			throw new \InvalidArgumentException();
		
		$this->type = $a_type;
	}
	
	public function execute () {
		
		/* Build the query string */
		$query_url = self::ranking_api_base;
		$query_url = str_replace(':category', $this->category, $query_url);
		$query_url = str_replace(':period', $this->period, $query_url);
		$query_url = str_replace(':type', $this->type, $query_url);
		
		try {
			$config = new Config;
			$config->setClientTimeout(20);
			
			$reader = new Reader($config);
			$resource = $reader->discover($query_url);
			
			$parser = $reader->getParser(
				$resource->getUrl(),
				$resource->getContent(),
				$resource->getEncoding()
			);
			
			$feed = $parser->execute();
			$items = $feed->getItems();
			$videos = array();
			$position = 1;
		
			foreach ($items as $item) {
				$id = [];
				$points_match = [];
				$plays_match = [];
				$comments_match = [];
				$mylists_match = [];
				$image_match = [];
				preg_match('/([0-9a-z]{2}[0-9]+)$/', $item->url, $id);
				
				if (isset($id[0])) {
				    
				    $parts = preg_split('/合計|毎時|日間|週間|月間/',$item->content, PREG_SPLIT_OFFSET_CAPTURE);
                    //$regex = '/alt\=\"' . mb_substr(str_ireplace('/', '\\/', $item->title), 4) . '\" src\=\"(.*)\"/';
                    $regex = '/src\=\"(.*)?\"/';
                    preg_match($regex, $parts[0], $image_match);
				    
				    if ($this->period == RankingPeriod::Total) {
                        preg_match('/\<strong\>([0-9,]+)\<\/strong\>pts./', $parts[0], $points_match);
                        preg_match('/\再生：<strong\>([0-9,]+)\<\/strong\>/', $parts[1], $plays_match);
                        preg_match('/\コメント：<strong\>([0-9,]+)\<\/strong\>/', $parts[1], $comments_match);
                        preg_match('/\マイリスト：<strong\>([0-9,]+)\<\/strong\>/', $parts[1], $mylists_match);
                    } else {
                        preg_match('/\<strong\>([0-9,]+)\<\/strong\>pts./', $parts[0], $points_match);
                        preg_match('/再生：\<strong\>([0-9,]+)\<\/strong\>/', $parts[2], $plays_match);
                        preg_match('/コメント：\<strong\>([0-9,]+)\<\/strong\>/', $parts[2], $comments_match);
                        preg_match('/マイリスト：\<strong\>([0-9,]+)\<\/strong\>/', $parts[2], $mylists_match);
                    }
        
					
					$video = [
						'contentId' => $id[0],
						'title'     => mb_substr($item->title, 4),
						'url'       => $item->url,
                        'thumbnail' => end($image_match),
                        'points'    => (int) str_replace(',', '', end($points_match)),
                        'plays'     => (int) str_replace(',', '', end($plays_match)),
                        'comments'  => (int) str_replace(',', '', end($comments_match)),
                        'mylists'   => (int) str_replace(',', '', end($mylists_match))
					];
					
					$videos[$position] = $video;
				} else {
					throw new \Exception();
				}
				
				$position++;
			}
			
			$status = 200;
			$code = 'OK';
			$message = '';
			$data = $videos;
			$total = count($videos);
		} catch (\Exception $e) {
			//trigger_error('Invalid Video identifier');
			$status = 404;
			$code = 'Not Found';
			$message = 'Impossible to fetch Ranking';
			$data = null;
			$total = 0;
		}
		
		return new Response($status, $code, $message, $data, $total);
		
	}
	
}