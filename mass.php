<?php
/*

Jangan dibuat jika tidak mau eror
coded by roy
 */

//$tanggal_sekarang = date("d");
$data = file_get_contents("akun.txt");
$pisah = array_filter(explode("\n", $data));
foreach ($pisah as $kunci) {
	$pisah_1 = explode("|", $kunci);
	$user = $pisah_1[0];
	$pass = $pisah_1[1];
	popen("start php asli.php $user $pass", "w");

}

?>