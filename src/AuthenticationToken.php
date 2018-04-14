<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/10/15
 * Time: 21:06
 */

namespace NicoPHP;


final class AuthenticationToken {
	
	public $mail_or_phone;
	
	public $password;
	
	public function __construct ($mail_or_phone, $password) {
		
		$this->mail_or_phone = $mail_or_phone;
		$this->password = $password;
		
	}
	
}