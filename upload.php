<?php
include_once "config.php";

if(isset($_FILES['inputFile'])) {
	$uploadImg = $_FILES['inputFile'];
    //$ext = strtolower(substr($uploadImg["name"],strrpos($uploadImg["name"],'.')));
    $ext = strtolower(substr($uploadImg["type"], strrpos($uploadImg["type"],'/')+1));

	$date = new DateTime();
	$new_name = generateRandomString(6).$date->getTimestamp().".".$ext;
	
	//$target_file = $target_dir . basename($_FILES["inputFile"]["name"]);
	$target_file = $target_dir . $new_name;
	
	$check = getimagesize($_FILES["inputFile"]["tmp_name"]);
	if($check == false) {
	    echo json_encode(["error"=>"File is not an image"]); exit;
	}
	
	// Check if file already exists
	if (file_exists($target_file)) {
	    echo json_encode(["error"=>"Sorry, file already exists"]); exit;
	}
	// Check file size
	if ($_FILES["inputFile"]["size"] > 5000000) {
	    echo json_encode(["error"=>"Sorry, your file is too large"]); exit;
	}
	// Allow certain file formats
	if($ext != "jpg" && $ext != "png" && $ext != "jpeg" && $ext != "bmp" && $ext != "gif" ) {
	    echo json_encode(["error"=>"Sorry, only JPG, JPEG, PNG, BMP & GIF files are allowed"]); exit;
	}
	
	if (move_uploaded_file($_FILES["inputFile"]["tmp_name"], $target_file)) {
		$uploadfile = [
			"file"	=>	$target_file,
			"url"	=>	$parent_url.$target_dir.$new_name,
			"type"	=>	$ext
		];
		
		$imgsize = getimagesize($uploadfile['file']);
		list($width, $height, $type, $attr) = $imgsize;
		$uploadfile['size'] = ["width"=>$width, "height"=>$height];
		
		$_SESSION['uploadImage'] = $uploadfile;
		echo json_encode($uploadfile);
	} else {
	    echo json_encode(["error"=>"Sorry, there was an error uploading your file"]); exit;
	}
}
