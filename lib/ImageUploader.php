<?php 

namespace MyApp;

class ImageUploader {

	private $_imageFileName;
	public $_imageType;

	public function upload() {
		try {
		    // error check
			$this->_validateUpload();

			// type check
			$ext = $this->_validateImageType();

			// save
			$savePath = $this->_save($ext);

			// create thumbnail
			$this->_createThumbnail($savePath);

		} catch (\Exception $e) {
			echo $e->getMessage();
			exit;
		}
		// redirect
		// header('Location: http://' . $_SERVER['HTTP_HOST']);
		// exit;
	}

	// public function getImages() {
	// 	$images = [];
	// 	$files = [];
	// 	$imageDir = opendir(IMAGES_DIR);
	// 	while (false !== ($file = readdir($imageDir))) {
	// 		if ($file === '.' || $file === '..') {
	// 			continue;
	// 		}
	// 		$files[] = $file;
	// 		if (file_exists(THUMBNAIL_DIR . '/' . $file)) {
	// 			$images[] = basename(THUMBNAIL_DIR) . '/' . $file;
	// 		} else {
	// 			$images[] = basename(IMAGES_DIR) . '/' . $file;
	// 		}
	// 	}
	// 	array_multisort($files, SORT_DESC, $images);
	// 	return $images;
	// }


	private function _createThumbnail($savePath) {
		$imageSize = getimagesize($savePath);
		$width = $imageSize[0];
		$height = $imageSize[1];
		if ($width > THUMBNAIL_WIDTH) {
			$this->_createThumbnailMain($savePath, $width, $height);
		}
	}

	private function _createThumbnailMain($savePath, $width, $height) {
		switch ($this->_imageType) {
			case IMAGETYPE_GIF:
			    $srcImage = imagecreatefromgif($savePath);
				break;
			case IMAGETYPE_JPEG:
			    $srcImage = imagecreatefromjpeg($savePath);
				break;
			case IMAGETYPE_PNG:
			    $srcImage = imagecreatefrompng($savePath);
				break;
		}
		$thumbHeight = round($height * THUMBNAIL_WIDTH / $width);
		$thumbImage = imagecreatetruecolor(THUMBNAIL_WIDTH, $thumbHeight);
		imagecopyresampled($thumbImage, $srcImage, 0, 0, 0, 0, THUMBNAIL_WIDTH, $thumbHeight, $width, $height);

		switch ($this->_imageType) {
			case IMAGETYPE_GIF:
			    imagegif($thumbImage, THUMBNAIL_DIR . '/' . $this->_imageFileName);
				break;
			case IMAGETYPE_JPEG:
			    imagejpeg($thumbImage, THUMBNAIL_DIR . '/' . $this->_imageFileName);
				break;
			case IMAGETYPE_PNG:
			 	imagepng($thumbImage, THUMBNAIL_DIR . '/' . $this->_imageFileName);
				break;
		}

	}



	private function _save($ext) {
		$this->_imageFileName = sprintf(
			'%s_%s.%s',
			time(),
			sha1(uniqid(mt_rand(), true)),
			$ext
		);
		$savePath = IMAGES_DIR . '/' . $this->_imageFileName;
		$res = move_uploaded_file($_FILES['image']['tmp_name'], $savePath);
		if ($res === false) {
			throw new \Exception('Could not upload!');
		}
		return $savePath;
	}

	private function _validateImageType() {
	$this->_imageType = exif_imagetype($_FILES['image']['tmp_name']);
		switch ($this->_imageType) {
			case IMAGETYPE_GIF:
			  return 'gif';
			case IMAGETYPE_JPEG:
			  return 'jpeg';
			case IMAGETYPE_PNG:
			  return 'png';
			default:
			  throw new \Exception('PNG/JPEG/GIF only!');
		}
	}

	private function _validateUpload() {
		if (!isset($_FILES['image']) || !isset($_FILES['image']['error'])) {
			throw new \Exception('Upload Error!');
			exit;
		}

		switch ($_FILES['image']['error']) {
			case UPLOAD_ERR_OK:
			  return true;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
			  throw new \Exception('File too large!');
			default:
			throw new \Exception('Err: ' . $_FILES['image']['error']);
		}
	}

	public function writeData() {
    $personal_name = h($_POST['personal_name']);
    $contents = h($_POST['contents']);
    $contents = nl2br($contents);

    if (file_exists('thumbs' . '/' . $this->_imageFileName)) {
		$imageFile = 'thumbs' . '/' . $this->_imageFileName;
	} elseif (file_exists('images' . '/' . $this->_imageFileName)){
		$imageFile = 'images' . '/' . $this->_imageFileName;
	}




    $data = "<hr>\r\n";
    $data = $data."<p>投稿者:". $personal_name ."</p>\r\n";
    $data = $data."<p>内容:</p>\r\n";
    if ($_POST['contents']) {
    	$data = $data."<p>".$contents."</p>\r\n";
    }
    else{
    	$data = $data."<p></p>\r\n";
    }
    if (!$_FILES['image']['size'] == null) {
    	$data = $data."<img src=".$imageFile.">\r\n";
    }
    else{
    	$data = $data."<p></p>\r\n";
    }

    $keijban_file = 'keijiban.txt';

    $fp = fopen($keijban_file, 'ab');

    if ($fp){
        if (flock($fp, LOCK_EX)){
            if (fwrite($fp,  $data) === FALSE){
                print('ファイル書き込みに失敗しました');
            }

            flock($fp, LOCK_UN);
        }else{
            print('ファイルロックに失敗しました');
        }
    }
    fclose($fp);
	header('Location: http://' . $_SERVER['HTTP_HOST']);
	exit;
	}

 //    $data = "投稿者:". $personal_name ."\n";
 //    $data = $data."内容:\n";
 //    if ($_POST['contents']) {
 //    	$data = $data.$content."\n";
 //    }
 //    else{
 //    	$data = $data.""."\n";
 //    }
 //    if (!$_FILES['image']['size'] == null) {
 //    	$data = $data.$imageFile."\n";
 //    }
 //    else{
 //    	$data = $data.""."\n";
 //    }



 //    $keijban_file = 'keijiban3.txt';

 //    $fp = fopen($keijban_file, 'ab');

 //    if ($fp){
 //        if (flock($fp, LOCK_EX)){
 //            if (fwrite($fp,  $data) === FALSE){
 //                print('ファイル書き込みに失敗しました');
 //            }

 //            flock($fp, LOCK_UN);
 //        }else{
 //            print('ファイルロックに失敗しました');
 //        }
 //    }

 //    fclose($fp);
	// header('Location: http://' . $_SERVER['HTTP_HOST']);
	// exit;
	// }

}

?>