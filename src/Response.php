<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/10/20
 * Time: 15:46
 */

namespace NiconicoPHP;

class Response {
	
	public $status;
	
	public $code;
	
	public $message;
	
	public $data;
	
	public $total;
	
	public function __construct ($status, $code, $message, $data = null, $total = 0) {
		
		if (!is_numeric($status)) {
			throw new \InvalidArgumentException();
		}
		
		$this->status = $status;
		$this->code = $code;
		$this->message = $message;
		$this->data = $data;
		$this->total = $total;
		
	}
	
	public function has_data () {
		
		return (bool) $this->data;
		
	}
	
}