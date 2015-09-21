<?php

function qrcodepng($voucher_number, $filename = null){
	//
	include_once('voucher/phpqrcode/qrlib.php');

	// Get Voucher Number and use it as file name
	$s_voucher_number = $voucher_number;
	if($filename == NULL){
		$s_filename = 'voucher/qrcodepng' . DIRECTORY_SEPARATOR . $s_voucher_number . '.png';
	}else{
		$s_filename = $filename;
	}

	// Generate PNG file
	QRcode::png($s_voucher_number, $s_filename, QR_ECLEVEL_H, 4);

	// return PNG file name
	if (file_exists($s_filename)) {
		return $s_filename;
	} else {
		return FALSE;
	}
}
?>