<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/10/22
 * Time: 18:11
 */

namespace NiconicoPHP;

/**
 * Class AbstractQueryAPI
 *
 * @package NiconicoPHP
 *
 * @method array get_targets()
 * @method array get_fields()
 * @method array get_filters()
 * @method string get_sort()
 * @method integer get_offset()
 * @method integer get_limit()
 */
abstract class AbstractQueryAPI {
	
	protected $targets;
	
	protected $fields;
	
	protected $filters;
	
	protected $polarity = 'desc';
	
	protected $sort;
	
	protected $offset = 0;
	
	protected $limit = 10;
	
	protected $test = false;
	
	public function __call ($name, $args = null) {
		
		$action = strtolower(substr($name, 0, 3));
		$property = strtolower(substr($name, 4));
		
		$properties = get_class_vars(get_class($this));
		
		if ($action === 'get') {
			if ((property_exists($this, $property) && array_key_exists($property, $properties))) {
				return $this->{$property};
			}
		}
		
		throw new \InvalidArgumentException('Function Not Found');
		
	}
	
	public function targets(array $targets) {
		
		foreach ($targets as $item) {
			switch ($item) {
				case 'title':
				case 'description':
				case 'tags':
				case 'tags_exact':
					break;
				default:
					throw new \UnexpectedValueException('Invalid search target "' . $item . '"');
			}
		}
		
		$this->targets = $targets;
		return $this;
		
	}
	
	abstract function fields (array $fields);
	
	abstract function add_filter (Filter $filter);
	
	abstract function sort_by ($string);
	
	abstract function size ($integer);
	
	abstract function offset ($integer);
	
	abstract function query ($string);
	
	public function test ($string) {
		
		$limit = $this->limit;
		$offset = $this->offset;
		
		$this->limit = 0;
		$this->offset = 0;
		$this->test = true;
		
		$result = $this->query($string);
		
		$this->limit = $limit;
		$this->offset = $offset;
		$this->test = false;
		
		return $result;
		
	}
	
}