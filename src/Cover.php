<?php
	namespace UtopiaCover;
	class Cover {
		public function __construct() {
			$this->loadEnvironment();
		}
		
		function loadEnvironment() {
			$dotenv = \Dotenv\Dotenv::create(__DIR__ . "/../");
			$dotenv->load();
		}
		
		public function updateGroupCover() {
			$path_bg    = __DIR__ . '/../img/cover_bg.jpg';
			$path_data  = __DIR__ . '/../cache/coverData.json';
			$path_cover = 'cover.png';
			
			//load last data
			$json = file_get_contents($path_data);
			if(!file_exists($path_data)) {
				$data_last = [];
			} else {
				$data_last = json_decode($json, true);
			}
			
			//load background image
			$img_bg  = imageCreateFromJpeg($path_bg);
			
			//uns sync info
			$url = 'http://api.idyll.info/api/uns/sync_info';
			$json = Utilities::curlGET($url);
			if(! Utilities::isJSON($json)) {
				//error. use cached data
				$stats_total_blocks = $data_last['total_blocks'];
				$stats_last_record_names_registered = $data_last['last_record_names_registered'];
				$stats_state = $data_last['state'];
			} else {
				$arr = json_decode($json, true);
				$stats_total_blocks = $arr['total_blocks'];
				$stats_last_record_names_registered = $arr['last_record_names_registered'];
				$stats_state = $arr['state'];
			}
			
			$url = 'http://api.idyll.info/api/rates';
			$json = Utilities::curlGET($url);
			if(! Utilities::isJSON($json)) {
				$stats_rate_RUB = $data_last['rate_RUB'];
				$stats_rate_EUR = $data_last['rate_EUR'];
				$stats_rate_BTC = $data_last['rate_BTC'];
			} else {
				$arr = json_decode($json, true);
				$stats_rate_RUB = $arr['RUB'];
				$stats_rate_EUR = $arr['EUR'];
				$stats_rate_BTC = $arr['BTC'];
			}
			
			//выбор цвета текста и размера шрифта
			$text_color = imagecolorallocate($img_bg, 220, 220, 220);
			//putenv('GDFONTPATH=' . realpath('.'));
			$font_size = 22;
			$font_file = __DIR__ . '/../fonts/akrobat.ttf';
			$x_fix = -5;
			$y_fix = 27;
			
			imagettftext($img_bg, $font_size, 0, 163+$x_fix, 235+$y_fix, $text_color, $font_file, 'Blocks: ' . $stats_total_blocks);
			imagettftext($img_bg, $font_size, 0, 163+$x_fix, 270+$y_fix, $text_color, $font_file, 'Last uNS: ' . $stats_last_record_names_registered);
			imagettftext($img_bg, $font_size, 0, 163+$x_fix, 310+$y_fix, $text_color, $font_file, 'Status: ' . $stats_state);
			
			imagettftext($img_bg, $font_size, 0, 560+$x_fix, 235+$y_fix, $text_color, $font_file, '1 CRP = ' . $stats_rate_BTC . ' BTC');
			imagettftext($img_bg, $font_size, 0, 560+$x_fix, 270+$y_fix, $text_color, $font_file, '1 CRP = ' . $stats_rate_RUB . ' RUB');
			imagettftext($img_bg, $font_size, 0, 560+$x_fix, 310+$y_fix, $text_color, $font_file, '1 CRP = ' . $stats_rate_EUR . ' EUR');
			
			//data for save
			$data_new = [
				'total_blocks' => $stats_total_blocks,
				'last_record_names_registered' => $stats_last_record_names_registered,
				'state' => $stats_state,
				'rate_RUB' => $stats_rate_RUB,
				'rate_EUR' => $stats_rate_EUR,
				'rate_BTC' => $stats_rate_BTC
			];
			//save
			file_put_contents($path_data, json_encode($data_new));
			
			$is_debug = false;
			//save image
			//if($is_debug) {
			//	header('Content-Type: image/png');
			//	imagePng($img_bg);
			//} else {
				imagePng($img_bg, $path_cover);
			//}
			
			if(!$is_debug) {
				//change group cover
				$vk     = new VKCover(getenv('user_api_key'));
				$url    = $vk->PhotoUploadServer(getenv('group_id'));
				$photo  = $vk->UploadPhoto($url['upload_url'], $path_cover);
				$result = $vk->SavePhoto($photo['hash'], $photo['photo']);
			}
		}
	}
	