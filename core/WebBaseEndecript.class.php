<?php
Class WebBaseEndecript{
	static $key="v(lfeki&^%)$@^&!8jp=k-";
	static $iv="*&$#&K(:";
	static function encode($strIn){
		//EnDecript::$iv=pack("H16",EnDecript::$iv);
		return trim(base64_encode(mcrypt_encrypt(MCRYPT_3DES, self::$key, $strIn, MCRYPT_MODE_CBC,self::$iv)));
	}
	static function decode($strIn){
		//EnDecript::$iv=pack("H16",EnDecript::$iv);
		return trim(mcrypt_decrypt(MCRYPT_3DES,  self::$key, base64_decode($strIn), MCRYPT_MODE_CBC, self::$iv));
	}

}

?>