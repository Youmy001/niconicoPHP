<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/10/19
 * Time: 19:49
 */

namespace NiconicoPHP;

class SnapshotFilter implements Filter {
    
    public $type;
    
    public $field;
	
	public function __construct ($type, $field, $options) {
		
		if ($type == 'range' || $type == 'equal') {
			$this->type = $type;
			$this->field = $field;
			
			switch ($field) {
				case 'start_time':
				case 'view_counter':
				case 'comment_counter':
				case 'mylist_counter':
				case 'last_res_body':
				case 'length_seconds':
					break;
				default:
					throw new \UnexpectedValueException('Invalid return field target "' . $field . '"');
			}
			
			if ($type == 'range') {
				if (is_array($options) && (count($options) > 0)) {
					if (isset($options['from'])) {
						$this->from = $options['from'];
					}
					
					if (isset($options['to'])) {
						$this->to = $options['to'];
					}
					
					if (isset($options['include_lower'])) {
						$this->include_lower = (bool) $options['include_lower'];
					}
					
					if (isset($options['include_upper'])) {
						$this->include_upper = (bool) $options['include_upper'];
					}
				} else {
					throw new \InvalidArgumentException('Missing Range options');
				}
			} else {
				if (isset($options) && (is_string($options) || is_integer($options))) {
					$this->value = $options;
				} else {
					throw new \InvalidArgumentException('Missing Value');
				}
 			}
		} else {
			throw new \InvalidArgumentException('Invalid Filter Type');
		}
		
	}
}