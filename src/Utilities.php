<?php
	namespace UtopiaCover;
	class Utilities {
		function isJSON($string = ""): bool {
			return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;
		}
		
		function data_filter($string = "", $db_link = null) {
			//\UtopiaCover\Utilities::data_filter
			$string = strip_tags($string);
			$string = stripslashes($string);
			$string = htmlspecialchars($string);
			$string = trim($string);
			if(isset($db_link) && $db_link != null) {
				$string = $db_link->filter_string($string);
			}
			return $string;
		}
		
		function cURL($url, $ref, $header, $cookie, $p = null): string {
			$ch =  curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			if(isset($_SERVER['HTTP_USER_AGENT'])) {
				curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			}
			if($ref != '') {
				curl_setopt($ch, CURLOPT_REFERER, $ref);
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			if($cookie != '') {
				curl_setopt($ch, CURLOPT_COOKIE, $cookie);
			}
			if ($p) {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
			}
			$result =  curl_exec($ch);
			curl_close($ch);
			if ($result){
				return $result;
			} else {
				return '';
			}
		}
		
		function curlGET($url): string {
			return Utilities::cURL($url, '', '', '');
		}
	}
	