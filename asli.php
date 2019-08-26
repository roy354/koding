<?php
require 'class.php';
$user = $argv[1];
$pass = $argv[2];

// Loop Endless
while (1) {
	$tanggal_sekarang = date("d");
	$baru = new social();
	$baru->login($user, $pass);
	$get = $baru->get_primary(); // dapat id postingan baru
	for ($i = 0; $i < 1500; $i++) {
		$baru->react($get);
		//sleep(1);
		$id = $baru->cari($get);
		$get = $id;
	}
	$baru->logout();
	while (1) {
		if ($tanggal_sekarang != date("d")) {
			break;
		}
	}
}
?>