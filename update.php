<?php
include_once "config.php";

$img = $_SESSION["uploadImage"]["file"];
$url = $_SESSION["uploadImage"]["url"];
$ext = strtolower(substr($img,strrpos($img,'.')));
$dir = substr($img, 0, strrpos($img,'/')+1);
$parentUrl = substr($url, 0, strrpos($url,'/')+1);
$imgsize = getimagesize($img);
list($width, $height, $type, $attr) = $imgsize;
$rerera = $width/$_POST['imgSiW'];
$reimra = $width/$_POST['imgCiW'];
$quality = 80;
if (in_array($ext,array('.jpg','.jpeg'))) {
	$image = @imagecreatefromjpeg($img); 
	$quality = 85;
    $imageMethod = "imagejpeg";
} elseif ($ext == '.png') {
	$image = @imagecreatefrompng($img); 
	$quality = 7;
    $imageMethod = "imagepng";
} elseif ($ext == '.gif') {
	$image = @imagecreatefromgif($img); 
	$imageMethod = "imagegif";
} elseif ($ext == '.bmp') {
	$image = @imagecreatefromwbmp($img); 
	$imageMethod = "imagewbmp";
}

$degrees = $_POST['imgRd'];
if($degrees) {
	$degrees = 360 - $degrees;
	$image = imagerotate($image, $degrees, 0);
}

$udwidth= $rerera*$_POST['crPosW'];
$udhight= $rerera*$_POST['crPosH'];
$new_image = imagecreatetruecolor($udwidth, $udhight);
if ($ext == '.png')
	$color = imagecolorallocatealpha($new_image, 0, 0, 0, 127); 	//	Tranperant
else
	$color = imagecolorallocate($new_image, 255, 255, 255);
imagefill($new_image, 0, 0, $color);

$dst_x = 0;		$src_x = 0;
$dst_y = 0;		$src_y = 0; 
$nImgCiLt = $_POST['imgCiLt'];		
$nImgCiTp = $_POST['imgCiTp'];
$nImgCiW  = $_POST['imgCiW'];
$nImgCiH  = $_POST['imgCiH'];

if($degrees==90 || $degrees==270) {
	$nImgCiLt = $_POST['imgCiLt'] + ($_POST['imgCiW']-$_POST['imgCiH'])/2;
	$nImgCiTp = $_POST['imgCiTp'] + ($_POST['imgCiH']-$_POST['imgCiW'])/2;
	$nImgCiW  = $_POST['imgCiH'];		$nImgCiH = $_POST['imgCiW'];
	$tmp 	= $width;
	$width  = $height;
	$height = $tmp;
} 

if($nImgCiLt > $_POST['crPosLt']) {
	$dst_x = $rerera*($nImgCiLt-$_POST['crPosLt']);	
} else {
	$src_x = $reimra*($_POST['crPosLt'] - $nImgCiLt);
}

if($nImgCiTp > $_POST['crPosTp']) {
	$dst_y = $rerera*($nImgCiTp - $_POST['crPosTp']);	
} else {
	$src_y = $reimra*($_POST['crPosTp'] - $nImgCiTp);
}

if($_POST['crPosW'] > $nImgCiW) {
	$src_w = $width;
	$dst_w = $rerera*$nImgCiW;
} else {
	$src_w = $reimra*$_POST['crPosW'];
	$dst_w = $udwidth;
}

if($_POST['crPosH'] > $nImgCiH) {
	$src_h = $height;
	$dst_h = $rerera*$nImgCiH;
} else {
	$src_h = $reimra*$_POST['crPosH'];
	$dst_h = $udhight;
}

imagecopyresampled($new_image, $image, $dst_x, $dst_y, $src_x, $src_y , $dst_w, $dst_h, $src_w, $src_h);

$date = new DateTime();
$new_name = generateRandomString(6).$date->getTimestamp().$ext;
if($imageMethod($new_image, $dir.$new_name, $quality)) {
	$updatefile = [
		"file"	=>	$dir.$new_name,
		"url"	=>	$parentUrl.$new_name,
		"type"	=>	$_SESSION['uploadImage']['type'],
		"size"	=>	[
			"height"	=>	$rerera*$_POST['crPosH'],
			"width"		=>	$rerera*$_POST['crPosW']
		],
		"rerera"	=>	$rerera,
		"reimra"	=>	$reimra,
		"info"		=>	[
			"dst_x"	=>	$dst_x, 
			"dst_y"	=>	$dst_y, 
			"src_x"	=>	$src_x, 
			"src_y"	=>	$src_y, 
			"dst_w"	=>	$dst_w, 
			"dst_h"	=>	$dst_h, 
			"src_w"	=>	$src_w, 
			"src_h"	=>	$src_h
		]
	];
	$_SESSION['uploadImage'] = $updatefile;
	echo json_encode($updatefile);
} else {
    echo 'Error when update image!';
}
die();
