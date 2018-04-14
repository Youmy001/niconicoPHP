<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/10/19
 * Time: 19:49
 */

namespace NiconicoPHP;

class SearchFilter implements Filter {
	
	public function __construct ($field, array $options) {
		
		switch ($field) {
			case 'contentId':
			case 'tags':
			case 'categoryTags':
			case 'viewCounter':
			case 'commentCounter':
			case 'mylistCounter':
			case 'startTime':
			case 'liveStatus':
				break;
			default:
				throw new \UnexpectedValueException('Invalid return field "' . $field . '"');
		}
		
		if (empty($options)) {
			throw new \InvalidArgumentException('At least one option is required');
		}
		
		foreach ($options as $key => $value) {
			if (!((is_numeric($key)) || ($key == 'lte' || $key == 'gte' || $key == 'lt' || $key == 'gt'))) {
				throw new \InvalidArgumentException('Invalid option name');
			}
			
			if (!(is_numeric($key) || is_string($key))) {
				throw new \InvalidArgumentException('Invalid option value');
			}
		}
		
		$this->field = $field;
		$this->options = $options;
		
	}
	
}