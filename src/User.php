<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 16/12/03
 * Time: 23:03
 */

namespace NiconicoPHP;


class User {
	
	public $id;
	
	public $username;
	
	public $icon;
	
	public function __construct ($user_id, $user_name, $user_icon = null) {
		$this->id = $user_id;
		$this->username = $user_name;
		
		if (!is_null($user_icon)){
			$this->icon = $user_icon;
		} else {
			$this->icon = 'http://usericon.nimg.jp/usericon/' . intval($user_id / 10000) . '/' . $user_id . '.jpg';
		}
	}
	
}