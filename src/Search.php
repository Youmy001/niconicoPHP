<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/10/18
 * Time: 22:17
 */

namespace NiconicoPHP;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;

class Search extends AbstractQueryAPI {
	
	const search_api_base = 'http://api.search.nicovideo.jp/api/v2/:service/contents/search';
	
	protected $service;
	
	public function video () {
		
		$this->service = 'video';
		return $this;
	}
	
	public function live () {
		
		$this->service = 'live';
		return $this;
		
	}
	
	public function illust () {
		
		$this->service = 'illust';
		return $this;
		
	}
	
	public function manga () {
		
		$this->service = 'manga';
		return $this;
		
	}
	
	public function book () {
		
		$this->service = 'book';
		return $this;
		
	}
	
	public function channel () {
		
		$this->service = 'channel';
		return $this;
		
	}
	
	public function channel_article () {
		
		$this->service = 'channelarticle';
		return $this;
		
	}
	
	public function news () {
		
		$this->service = 'news';
		return $this;
		
	}
	
	public function fields (array $fields) {
		
		if (empty($this->service)) {
			throw new \DomainException('Cannot set fields if the service is not defined');
		}
		
		foreach ($fields as $item) {
			switch ($item) {
				case 'contentId':
				case 'title':
				case 'description':
				case 'tags':
				case 'categoryTags':
				case 'viewCounter':
				case 'commentCounter':
				case 'mylistCounter':
				case 'startTime':
				case 'communityIcon':
					break;
				default:
					if (!($item == 'liveStatus' && $this->service == 'live')) {
						throw new \UnexpectedValueException('Invalid return field "' . $item . '"');
					}
			}
		}
		
		$this->fields = $fields;
		return $this;
		
	}
	
	public function add_filter (Filter $filter) {
		
		if (is_a($filter, '\NiconicoPHP\SearchFilter')) {
			$this->filters[] = (object)(array) $filter;
		} else {
			throw new \InvalidArgumentException();
		}
		
		return $this;
		
	}
	
	public function sort_by ($string) {
		
		switch ($string) {
			case 'viewCounter':
			case 'commentCounter':
			case 'mylistCounter':
			case 'startTime':
			case 'thumbnailUrl':
				break;
			default:
				if (!($string == 'scoreTimeshiftReserved' && $this->service == 'live')) {
					throw new \UnexpectedValueException('Invalid return field "' . $string . '"');
				}
		}
		
		$this->sort = $string;
		return $this;
		
	}
	
	public function desc () {
		
		throw new \DomainException('Cannot Modify Order on Searches');
		
		/*$this->polarity = 'desc';
		return $this;*/
		
	}
	
	public function asc () {
		
		throw new \DomainException('Cannot Modify Order on Searches');
		
		/*$this->polarity = 'asc';
		return $this;*/
		
	}
	
	public function size ($limit) {
		
		if (!is_int($limit)) {
			throw new \InvalidArgumentException('$limit must be an integer');
		}
		
		if ($limit >= 0 && $limit <= 100) {
			$this->limit = $limit;
		} else {
			throw new \OutOfRangeException('$limit must be a positive integer smaller or equal to 100');
		}
		
		return $this;
		
	}
	
	public function offset ($offset) {
		
		if (!is_int($offset)) {
			throw new \InvalidArgumentException('$offset must be an integer');
		}
		
		if ($offset >= 0 && $offset <= 1600) {
			$this->offset = $offset;
		} else {
			throw new \OutOfRangeException('$offset must be a positive integer smaller or equal to 1600');
		}
		
		return $this;
		
	}
	
	public function query ($string) {
		
		if (empty($this->service)) {
			throw new \DomainException('Cannot search if service is not set');
		}
		
		if (empty($this->targets)) {
			throw new \DomainException('Cannot search if no search fields are specified');
		}
		
		if (!is_string($string) || empty($string)) {
			throw new \InvalidArgumentException('The search string must be a string and must not be empty');
		}
		
		if (empty($this->sort)) {
			throw new \DomainException('Sort method required');
		}
		
		$api_string = self::search_api_base;
		$api_string = str_replace(':service', $this->service, $api_string);	// Add the service
		$api_string .= "?q=$string";										// Add the search query
		$api_string .= "&targets=" . join(',', $this->targets);				// Add search fields (targets)
		
		if (!empty($this->fields)) {
			$api_string .= "&fields=" . join(',', $this->fields);                // Add return fields (fields)
		}
		
		if (!empty($this->filters)) {
			foreach ($this->filters as $filter) {
				foreach ($filter->options as $item => $value) {
					$api_string .= '&filters[' . $filter->field . '][' . $item . ']=' . urlencode($value);
				}
			}
		}
		
		/* TODO Add sort order */
		/*if ($this->polarity == 'desc') {
			$symbol = '-';
		} else {
			$symbol = '+';
		}*/
		
		$api_string .= '&_sort=' . $this->sort;								// Add sort method
		$api_string .= '&_offset=' . $this->offset;							// Add Limit
		$api_string .= '&_limit=' . $this->limit;							// Add Offset
		$api_string .= "&_context=apiguide";								// Add Context Statement
		
		/** Execute Query **/
		try {
			$client = new Client();
			$http_response = $client->get($api_string);
			
			$http_response_contents = $http_response->getBody()->getContents();
			$json_results = json_decode($http_response_contents);
			
			if (json_last_error() !== JSON_ERROR_NONE) {
				throw new \RuntimeException(json_last_error_msg());
			}
			
			if ($json_results->meta->status == 200) {
				$status = $json_results->meta->status;
				$code = 'OK';
				$message = '';
				
				if($this->test) {
					$data = null;
					$total = $json_results->meta->totalCount;
				} else {
					$data = array();
					
					foreach ($json_results->data as $item) {
						$data[] = (object)(array) $item;
					}
					
					$total = count($data);
				}
			} else {
				$status = $json_results->meta->status;
				$code = $json_results->meta->errorCode;
				$message = $json_results->meta->errorMessage;
				$data = null;
				$total = 0;
			}
			
			$response = new Response($status, $code, $message, $data, $total);
			return $response;
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
				$http_response_contents = $e->getResponse()->getBody()->getContents();
				$json_results = json_decode($http_response_contents);
				
				$status = $json_results->meta->status;
				$code = $json_results->meta->errorCode;
				$message = $json_results->meta->errorMessage;
				
				$response = new Response($status, $code, $message);
				return $response;
			} else {
				throw new \RuntimeException('Nico Search API not reachable');
			}
		}
		
	}

}