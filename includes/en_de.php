<?php
// encription method
function en_de($type, $ed_var, $ed_key) {
	$key = $ed_key;
	$string = $ed_var; // note the spaces
	$action = $type;
	
	switch ($action) {
		case 'encode':
			$return = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
			break;
		case 'decode':
			$return = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
			break;
		default:
			$return = 'en_de type_error '.$action;
			break;
	}
	
	return $return;
}
?>
