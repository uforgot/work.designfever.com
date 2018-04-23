<?php
	$_tmp = split(";base64,", $_POST['imgUpload']);
	$_filename = $_POST['filename'];
	if(count($_tmp) == 2) {
		$filename = $_filename;
		$imageData = base64_decode($_tmp[1]);
		$fp = fopen($filename, "wb");
		if($fp) {
			fwrite($fp, $imageData);
			fclose($fp);
			echo "success";
		}
		else {
			echo "failed";
		}
	}

?>