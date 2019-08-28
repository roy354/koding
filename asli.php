<?php
require 'class.php';
$user = $argv[1];
$pass = $argv[2];
$waktu_1_hari = 86400;
// Loop Endless
while (1) {
	$jam_awal = round(microtime(1));
	$baru = new social();
	$baru->login($user, $pass);
	$get = $baru->get_primary(); // dapat id postingan baru
	for ($i = 0; $i < 1500; $i++) {
		$baru->react($get);
		//sleep(1);
		//$rand = rand(0, 10);
		sleep($rand); // Supaya Server askdaraz gak down
		$id = $baru->cari($get);
		$get = $id;
	}
	$baru->logout();
	$jam_akhir = round(microtime(1))-$jam_awal;
	$sleep = $waktu_1_hari-$jam_akhir;
	sleep($sleep);
}
?>