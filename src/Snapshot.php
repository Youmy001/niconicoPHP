<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/10/22
 * Time: 0:49
 */

namespace NiconicoPHP;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;

/**
 * Class Snapshot
 *
 * @package NiconicoPHP
 *
 * @method string get_issuer()
 */
class Snapshot extends AbstractQueryAPI {
	
	const snapshot_api_base = 'http://api.search.nicovideo.jp/api/snapshot/';
	
	protected $issuer = 'youmy001/niconicoPHP';
	
	public function __construct ($issuer = '') {
		
		if (!empty($issuer) && strlen($issuer) <= 40) {
			$this->issuer = $issuer;
		}
		
		return $this;
		
	}
	
	public function fields (array $fields) {
		
		foreach ($fields as $item) {
			switch ($item) {
				case 'cmsid':
				case 'title':
				case 'description':
				case 'tags':
				case 'start_time':
				case 'thumbnail_url':
				case 'view_counter':
				case 'comment_counter':
				case 'mylist_counter':
				case 'last_res_body':
				case 'length_seconds':
					break;
				default:
					throw new \UnexpectedValueException('Invalid return field target "' . $item . '"');
			}
		}
		
		$this->fields = $fields;
		return $this;
		
	}
	
	public function add_filter (Filter $filter) {
		
		if (is_a($filter, '\NiconicoPHP\SnapshotFilter')) {
			$this->filters[] = (object)(array) $filter;
		} else {
			throw new \InvalidArgumentException();
		}
		
		return $this;
		
	}
	
	public function sort_by ($string) {
		
		switch ($string) {
			case 'last_comment_time':
			case 'view_counter':
			case 'mylist_counter':
			case 'comment_counter':
			case 'start_time':
			case 'length_seconds':
				break;
			default:
				throw new \UnexpectedValueException('Invalid return field "' . $string . '"');
		}
		
		$this->sort = $string;
		return $this;
		
	}
	
	public function desc () {
		
		$this->polarity = 'desc';
		return $this;
		
	}
	
	public function asc () {
		
		$this->polarity = 'asc';
		return $this;
		
	}
	
	public function size ($limit) {
		
		if (!is_int($limit)) {
			throw new \InvalidArgumentException("$limit must be an integer");
		}
		
		if ($limit >= 0 && $limit <= 100) {
			$this->limit = $limit;
		} else {
			throw new \OutOfRangeException("$limit must be a positive integer smaller or equal to 100");
		}
		
		return $this;
		
	}
	
	public function offset ($offset) {
		
		if (!is_int($offset)) {
			throw new \InvalidArgumentException("$offset must be an integer");
		}
		
		if ($offset >= 0) {
			$this->offset = $offset;
		} else {
			throw new \OutOfRangeException("$offset must be a positive integer");
		}
		
		return $this;
		
	}
	
	public function query ($string) {
		
		$return = null;
		
		if (!is_string($string) || empty($string)) {
			throw new \InvalidArgumentException('The search string must be a string and must not be empty');
		}
		
		if (empty($this->targets)) {
			throw new \DomainException('Cannot search if no search fields are specified');
		}
		
		if (empty($this->fields)) {
			throw new \DomainException('Cannot search if return fields are not set');
		}
		
		$api_string = self::snapshot_api_base;
		$query = array();
		
		$query['query'] = $string;
		$query['service'] = ['video'];
		$query['search'] = $this->targets;
		$query['join'] = $this->fields;
		
		if (!empty($this->filters)) {
			$query['filters'] = $this->filters;
		}
		
		if (!empty($this->sort)) {
			$query['sort_by'] = $this->sort;
		}
		
		$query['order'] = $this->polarity;
		$query['from'] = $this->offset;
		$query['size'] = $this->limit;
		$query['issuer'] = $this->issuer;
		
		//return $query;
		
		$json_query = json_encode($query);
		
		try {
			$client = new Client();
			$response = $client->post($api_string, [
				'headers' => [
					'Content-Type' => 'application/json',
					'Accept' => 'application/json'
				],
				'body' => $json_query
			]);
			
			$results = $response->getBody()->getContents();
			$json_results = json_decode($results);
			
			if (json_last_error() !== JSON_ERROR_NONE) {
				//$return = $results;
				$array_json_objects = preg_split("/\n/", $results, -1 , PREG_SPLIT_NO_EMPTY);
				
				foreach ($array_json_objects as $key => $value) {
					$array_json_objects[$key] = json_decode($value);
				}
				
				$half = count($array_json_objects) / 2;
				for ($i=0; $i<$half; $i++) {
					if (isset($array_json_objects[$i]->values)) {
						$values = $array_json_objects[$i]->values;
						$number_values = sizeof($values);
						for ($j = 0; $j < $number_values; $j++) {
							if (isset($values[$j]->_rowid)) {
								unset($values[$j]->_rowid);
							}
						}
						/*${$array_json_objects[$i]->type} = $values;*/
						$array_json_objects[$i]->values = $values;
					}
				}
				
				$status = 200;
				$code = 'OK';
				$message = '';
				
				if ($this->test) {
					$data = null;
					$total = $array_json_objects[0]->values[0]->total;
				} else {
					$data = $array_json_objects[0]->values;
					$total = count($data);
				}
			} else {
				$status= $json_results->errid;
				
				switch ($status) {
					case 101:
						$message = $json_results->desc;
						$code = 'SERVICE_UNAVAILABLE';
						break;
					case 700:
						$message = $json_results->desc;
						$code = 'ILLEGAL_FIELD_QUERY';
						break;
					case 1001:
						$message = $json_results->desc;
						$code = 'QUERY_TIMEOUT';
						break;
					case 300:
					default:
						$message = $json_results->desc;
						$code = 'INVALID_SEARCH_QUERY';
				}
				
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
				throw new \RuntimeException('Nico Snapshot API not reachable');
			}
		}
		
	}
	
}