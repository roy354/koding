<?php

/**
 *  Jangan Di Ganti kalau Gak bisa oke
 */
class social {

	function __construct($link = "https://www.askdaraz.com") {
		$this->link = $link;

		$this->auth = $auth = str_replace("https://", "", str_replace("http://", "", $this->link));
		$this->react = 0;
		$this->like = 1;
		$this->berhenti = 0;
		//$this->user = null;
		//$this->pass = null;
		//$this->token = file_get_contents("token/".$this->user);
	}
	function login($user, $pass) {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, '' . $this->link . '/requests.php?f=login');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "username=$user&password=$pass");
		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = 'Origin: ' . $this->link . '';
		$headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
		$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36';
		$headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
		$headers[] = 'Accept: */*';
		$headers[] = 'Referer: ' . $this->link . '/';
		$headers[] = 'Authority: ' . $this->auth . '';
		$headers[] = 'X-Requested-With: XMLHttpRequest';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__)."/cookie/".$user);
		//curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		} else {
			if (preg_match("/200/", $result)) {
				echo "Berhasil Login => $user\n";
				$this->user = $user;
				$this->pass = $pass;
			} else {
				echo "$result";
				die("Salah Njiir\n");
			}
		}
		curl_close($ch);

		// Ambil Token
		echo "[*] => Mengambil Token\n";
		$ch = curl_init("$this->link/");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/cookie/".$user);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$hasil = curl_exec($ch);

		curl_close($ch);
		preg_match_all('/<input type="hidden" class="main_session" value="(.*)">/', $hasil, $token);
		//var_dump($token);
		echo "[*] => Menyimpan Token\n";
		fwrite(fopen("token/".$this->user, "w+"), $token[1][0]);
	}
	function react($postid) {
		if ($this->berhenti == 1) {
			$this->berhenti = 0;
			return false;
		}
		$this->token = file_get_contents("token/".$this->user);
		$ch = curl_init();
		$react = array("Like", "Wow", "Love", "HaHa", "Sad", "Angry");

		if ($this->react == 1) {
			$r = $react[array_rand($react)];
		} else {
			$r = $react[0];
		}
		$waktu = date("U");
		if ($this->like == 1) {
			$links = $this->link . "/requests.php?hash=" . $this->token . "&f=posts&s=register_like&post_id=" . $postid . "&timeline_user=&_=" . $waktu . "";
		} else {
			$links = '' . $this->link . '/requests.php?hash=' . $this->token . '&f=posts&s=register_reaction&post_id=' . $postid . '&reaction=' . $r . '&_=' . $waktu . '';
		}
		//echo $links . "\n";
		curl_setopt($ch, CURLOPT_URL, $links);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/cookie/".$this->user);
		$headers = array();
		$headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
		$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36';
		$headers[] = 'Accept: */*';
		$headers[] = 'Referer: ' . $this->link . '/';
		$headers[] = 'Authority: ' . $this->auth . '';
		$headers[] = 'X-Requested-With: XMLHttpRequest';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		//curl_setopt($ch, CURLOPT_VERBOSE, 1);
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		} else {
			if (preg_match("/200/", $result)) {
				echo "[*] Berhasil React => $postid\n ";
				//echo $result;
			} else {
				echo "Gagal Nanti AJA Kita fix\n";
				//echo "$result\n";
				//echo "\n\n";
				$this->berhenti = 1;
				$this->react($postid);
			}
			if (preg_match("/login/", $result)) {
				die("Anjirr Belum Login\n");
			}
		}
		curl_close($ch);
	}
	function lagi() {
		$this->react();
	}
	function logout() {
		$ch = curl_init("$this->link/logout");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/cookie/".$this->user);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36");
		curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/cookie/".$this->user);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$hasil = curl_exec($ch);
		curl_close($ch);
		if (!curl_errno($ch)) {
			echo "[*] => Berhasil Logout\n";
		}else
		{
			echo "[*] => Gagal Logout\n";
		}
		//https://www.askdaraz.com/
		
	}
	function send_balance($jumlah) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->link . '/requests.php?f=wallet&s=send');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/cookie/".$this->user);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "amount=$jumlah&user_id=$id");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

		$headers = array();
		//$headers[] = 'Sec-Fetch-Mode: cors';
		//$headers[] = 'Sec-Fetch-Site: same-origin';
		//$headers[] = 'Dnt: 1';
		$headers[] = 'Accept-Encoding: gzip, deflate, br';
		$headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
		$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36';
		$headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
		$headers[] = 'Accept: */*';
		$headers[] = 'Referer: '.$this->link.'/wallet/';
		$headers[] = 'X-Requested-With: XMLHttpRequest';
		$headers[] = 'Connection: keep-alive';
		$headers[] = 'Origin: '.$this->link.'';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}else
		{
			$json = json_decode($result, 1);
			$stat = $json['status'];
			if ($stat == "200") {
				echo "Berhasil Kirim\n";
			}else
			{
				echo "Gagal Kirim Anjirr\n";
			}
		}
		curl_close($ch);
	}
	function cari($w) {

		$ch = curl_init();
		$waktu = date("U");
		curl_setopt($ch, CURLOPT_URL, 'https://www.askdaraz.com/requests.php?hash='.$this->token.'&f=posts&s=load_more_posts&filter_by_more=all&after_post_id=' . $w . '&user_id=0&page_id=0&group_id=0&event_id=0&posts_count=44&is_api=0&ad_id=0&story_id=0&_='.$waktu.'');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

		curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

		$headers = array();
		//$headers[] = 'Sec-Fetch-Mode: cors';
		//$headers[] = 'Sec-Fetch-Site: same-origin';
		//$headers[] = 'Dnt: 1';
		$headers[] = 'Accept-Encoding: gzip, deflate, br';
		$headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
		$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36';
		$headers[] = 'Accept: */*';
		$headers[] = 'Referer: https://www.askdaraz.com/';
		$headers[] = 'X-Requested-With: XMLHttpRequest';
		curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/cookie/".$this->user);
		$headers[] = 'Connection: keep-alive';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		} else {
			//echo $result;
		}
		curl_close($ch);
		preg_match_all('/<button type="button" class="btn btn-main" id="edit-post-button" onclick="Wo_EditPost\(\d{1,10}\)" >/', $result, $hasil);
		$data = str_replace(array('<button type="button" class="btn btn-main" id="edit-post-button" onclick="Wo_EditPost(', '" >', ')'), "", $hasil[0][0]);
		//print_r($hasil);
		return $data;
	}
	function make_post($postingan = "Ini Cuma Test ") {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/cookie/".$this->user);
		curl_setopt($ch, CURLOPT_URL, 'https://www.askdaraz.com/requests.php?f=posts&s=insert_new_post');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "postText=".$postingan."&videocount=&video_thumb=&musiccount=&answer%5B%5D=&answer%5B%5D=&album_name=&phtoscount=&filename=&postMap=&feeling=&feeling_type=&postPhotos%5B%5D=&postVideo=&postFile=&postMusic=&postPrivacy=0&hash_id=640b1e125b6f9d3653b74e74866bf035baa65553&postRecord=&postSticker=");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

		$headers = array();
		
		$headers[] = 'Origin: '.$this->link.'';
		$headers[] = 'Accept-Encoding: gzip, deflate, br';
		$headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
		$headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 8.0; Pixel 2 Build/OPD3.170816.012) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Mobile Safari/537.36';
		$headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
		$headers[] = 'Accept: */*';
		$headers[] = 'Referer: '.$this->link.'/';
		$headers[] = 'X-Requested-With: XMLHttpRequest';
		$headers[] = 'Connection: keep-alive';
	//	$headers[] = 'Dnt: 1';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		} else {
			if (preg_match("/200/", $result)) {
				echo "[*] => Berhasil Buat Postingan\n";
				// return "";
			}else
			{
				echo "[!] => Gagal Anjirr\n";
			}
		}
		curl_close($ch);
	}
	function get_primary() {
	
	echo "[*] => Mengambil ID PRIMARY\n";
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'https://www.askdaraz.com/requests.php?f=load_posts');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

	$headers = array();
	//$headers[] = 'Sec-Fetch-Mode: cors';
	//$headers[] = 'Sec-Fetch-Site: same-origin';
	//$headers[] = 'Dnt: 1';
	$headers[] = 'Accept-Encoding: gzip, deflate, br';
	$headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
	$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36';
	$headers[] = 'Accept: */*';
	$headers[] = 'Referer: https://www.askdaraz.com/';
	$headers[] = 'X-Requested-With: XMLHttpRequest';
	curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/cookie/".$this->user);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	} else {
		preg_match_all('/<button type="button" class="btn btn-main" id="edit-post-button" onclick="Wo_EditPost\(\d{1,10}\)" >/', $result, $hasil);
		fwrite(fopen("hasil.txt", "w+"), $result);
		$hasil = $hasil[0][2];
		$hasil = str_replace(array('<button type="button" class="btn btn-main" id="edit-post-button" onclick="Wo_EditPost(', ')" >'), "", $hasil);
		echo "[*] => Berhasil Ambil ID Primary\n";
		return $hasil;
	}
	curl_close($ch);
	}
}

?>