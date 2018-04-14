<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/12/03
 * Time: 22:38
 */

namespace NiconicoPHP;

use NiconicoPHP\User;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use \DOMDocument;

/**
 * Class Video
 *
 * @package NiconicoPHP
 *
 * @property string $title
 * @property string $description
 * @property string $thumbnail_url
 * @property \DateTime $publication_date
 * @property string $watch_url
 * @property string $length
 * @property integer $view_counter
 * @property integer $comment_counter
 * @property integer $mylist_counter
 * @property array $tags
 * @property array $categories
 * @property User $user
 */
class Video {
	
	private $resource_id;
	
	private $loaded = false;
	
	private $xml_string;
	
	/**
	 * @var string
	 */
	private $content_id;
	
	/**
	 * @var string
	 */
	private $title;
	
	/**
	 * @var string
	 */
	private $description;
	
	/**
	 * @var string
	 */
	private $thumbnail_url;
	
	/**
	 * @var \DateTime;
	 */
	private $publication_date;
	
	/**
	 * @var string
	 */
	private $watch_url;
	
	/**
	 * @var string
	 */
	private $length;
	
	/**
	 * @var integer
	 */
	private $view_counter;
	
	/**
	 * @var integer
	 */
	private $comment_counter;
	
	/**
	 * @var integer
	 */
	private $mylist_counter;
	
	/**
	 * @var array
	 */
	private $categories = [];
	
	/**
	 * @var array
	 */
	private $tags = [];
	
	/**
	 * @var User
	 */
	private $user;
	
	public function __construct ($smid) {
		$this->resource_id = $smid;
		$this->validate();
	}
	
	public function __get ($name) {
		
		if (property_exists($this, $name) && ($name !== 'resource_id' && $name !== 'loaded' && $name !== 'xml_string')) {
			if (!$this->loaded) {
				$this->parse();
			}
			
			return $this->$name;
		}
		
	}
	
	private function validate ($retrieve_only = false) {
		
		$client = new Client();
		$http_response = $client->get('http://ext.nicovideo.jp/api/getthumbinfo/' . $this->resource_id);
		
		$http_response_contents = $http_response->getBody()->getContents();
		
		if ($retrieve_only === false) {
			$document = new DOMDocument("1.0", "UTF-8");
			libxml_use_internal_errors(true);
			$xml_success = $document->loadXML($http_response_contents);
			
			if (!$xml_success) {
				throw new \RuntimeException(libxml_get_last_error()->message, libxml_get_last_error()->code);
			}
			
			libxml_clear_errors();
			
			$code = strtoupper($document->getElementsByTagName('nicovideo_thumb_response')->item(0)->getAttribute('status'));
			
			if ($code !== 'OK') {
				if ($code === 'FAIL') {
					$error = $document->getElementsByTagName('nicovideo_thumb_response')->item(0)->getElementsByTagName('error')->item(0);
					
					$code = $error->getElementsByTagName('code')->item(0)->nodeValue;
					//$description = $error->getElementsByTagName('description')->item(0)->nodeValue;
					switch ($code) {
						case 'DELETED':
							$message = 'Video ' . $this->resource_id . ' has been deleted.';
							break;
						case 'COMMUNITY':
							$message = 'Video ' . $this->resource_id . ' is a channel restricted.';
							break;
						case 'NOT_FOUND':
						default:
							$message = 'Video ' . $this->resource_id . ' is not found or invalid.';
					}
					
					throw new \RuntimeException($message, 500);
					
				}
			} else {
				$this->xml_string = $http_response_contents;
			}
		} else {
			$this->xml_string = $http_response_contents;
		}
		
	}
	
	public function parse () {
		
		if (!$this->xml_string) {
			$this->validate(true);
		}
		
		try {
			
			$document = new DOMDocument("1.0", "UTF-8");
			libxml_use_internal_errors(true);
			$xml_success = $document->loadXML($this->xml_string);
			
			if (!$xml_success) {
				throw new \RuntimeException(libxml_get_last_error()->message, libxml_get_last_error()->code);
			}
			
			libxml_clear_errors();
			
			$code = strtoupper($document->getElementsByTagName('nicovideo_thumb_response')->item(0)->getAttribute('status'));
			
			if ($code === 'OK') {
				$thumb = $document->getElementsByTagName('nicovideo_thumb_response')->item(0)->getElementsByTagName('thumb')->item(0);
				
				$this->content_id = $thumb->getElementsByTagName('video_id')->item(0)->nodeValue;
				$this->title = $thumb->getElementsByTagName('title')->item(0)->nodeValue;
				$this->description = $thumb->getElementsByTagName('description')->item(0)->nodeValue;
				$this->thumbnail_url = $thumb->getElementsByTagName('thumbnail_url')->item(0)->nodeValue;
				$this->publication_date = new \DateTime($thumb->getElementsByTagName('first_retrieve')->item(0)->nodeValue);
				$this->length = $thumb->getElementsByTagName('length')->item(0)->nodeValue;
				$this->view_counter = (int) $thumb->getElementsByTagName('view_counter')->item(0)->nodeValue;
				$this->comment_counter = (int) $thumb->getElementsByTagName('comment_num')->item(0)->nodeValue;
				$this->mylist_counter = (int) $thumb->getElementsByTagName('mylist_counter')->item(0)->nodeValue;
				$this->watch_url = $thumb->getElementsByTagName('watch_url')->item(0)->nodeValue;
				$this->user = new User($thumb->getElementsByTagName('user_id')->item(0)->nodeValue, $thumb->getElementsByTagName('user_nickname')->item(0)->nodeValue);
				
				/* Tags */
				$tag_domains = $thumb->getElementsByTagName('tags');
				
				foreach ($tag_domains as $domain) {
					$domain_name = $domain->getAttribute('domain');
					
					if ($domain->hasChildNodes()) {
						foreach ($domain->childNodes as $child_node) {
							if ($child_node->nodeType == XML_ELEMENT_NODE) {
								$this->tags[$domain_name][] = $child_node->nodeValue;
								
								if ($child_node->hasAttribute('category')) {
									$this->categories[] = $child_node->nodeValue;
								}
							}
						}
					}
				}
				
				$this->loaded = true;
			} else {
				if ($code === 'FAIL') {
					$error = $document->getElementsByTagName('nicovideo_thumb_response')->item(0)->getElementsByTagName('error')->item(0);
					
					$code = $error->getElementsByTagName('code')->item(0)->nodeValue;
					//$description = $error->getElementsByTagName('description')->item(0)->nodeValue;
					switch ($code) {
						case 'DELETED':
							$message = 'Video ' . $this->resource_id . ' has been deleted.';
							break;
						case 'COMMUNITY':
							$message = 'Video ' . $this->resource_id . ' is a channel restricted.';
							break;
						case 'NOT_FOUND':
						default:
							$message = 'Video ' . $this->resource_id . ' is not found or invalid.';
					}
					
					throw new \RuntimeException($message, 500);
					
				}
			}
		} catch (\Exception $e) {
			throw $e;
		}
		
	}
	
}