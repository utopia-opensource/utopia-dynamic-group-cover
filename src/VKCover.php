<?php
	namespace UtopiaCover;

	class VKCover {
		public $cover_width  = 1590;
		public $cover_height = 400;
		public $api_version  = '5.71';
		
		protected $token    = '';
		protected $api_base = 'https://api.vk.com/method/';
		
		public function __construct($token) {
			$this->setToken($token);
		}
		
		public function setToken($token = "") {
			$this->token = $token;
		}
		
		public function photoUploadServer($group_id) {
			$data = [
				'group_id' => $group_id,
				'v'        => $this->api_version,
				'crop_x'   => 0,
				'crop_y'   => 0,
				'crop_x2'  => $this->cover_width,
				'crop_y2'  => $this->cover_height
			];
			
			$out = $this->request($this->api_base . 'photos.getOwnerCoverPhotoUploadServer', $data);
			//exit(json_encode($out));
			
			return $out['response'];
		}

		public function uploadPhoto($url, $file) {
			$data = [
				'photo' => new \CURLFile($file)
			];
			$out = $this->request($url, $data);
			return $out;
		}

		public function savePhoto($hash, $photo) {
			$data = [
				'hash'  => $hash,
				'photo' => $photo,
				'v'     => $this->api_version,
			];
			$out = $this->request($this->api_base . 'photos.saveOwnerCoverPhoto', $data);
			return $out;
		}

		public function request($url, $data = []) {
			$curl = curl_init();
			
			$data['access_token'] = $this->token;
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			
			$out = json_decode(curl_exec($curl), true);
			curl_close($curl);
			
			return $out;
		}
	}
