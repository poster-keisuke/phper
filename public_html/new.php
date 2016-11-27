<?php 

ini_set('display_errors', 1);
define('MAX_FILE_SIZE', 1 * 1024 * 1024); //1MB
define('THUMBNAIL_WIDTH', 400);
define('IMAGES_DIR', __DIR__ . '/images');
define('THUMBNAIL_DIR', __DIR__ . '/thumbs');

if (!function_exists('imagecreatetruecolor')) {
	echo "GD not installed";
	exit;
}

//ユーザの一覧
require_once(__DIR__ . '/../config/config.php');
require (__DIR__ . '/../lib/ImageUploader.php');

$uploader = new \MyApp\ImageUploader();
$errMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST')  {
  checkToken();

  if (!$_POST['personal_name'])
    $errMsg = "★名前は必ず入力してください。<br>";
    // var_dump(!$_FILES['image']['size']);
  if(!$_POST['contents']  && !$_FILES['image']['size'])
    $errMsg .= "★画像か、テキストどちらかを入力してください。<br>";

  if(!$errMsg){
    if (!$_FILES['image']['size'] == null) {
      $uploader->upload();
    }
    $uploader->writeData();
  }
}
else {
  setToken();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>PHPer</title>
  <style>
  body {
    text-align: center;
    font-family: Arial, sans-serif;
  }
  ul {
    list-style: none;
    margin: 0;
    padding: 0;
  }
  li {
    margin-bottom: 5px;
  }
  </style>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div id="container">
  <?php 
  if($errMsg) {echo "$errMsg";}

  if(isset($_POST['personal_name'])) {
   $name = h($_POST['personal_name']);
 } else {
  $name = "";
 }

 if(isset($_POST['contents'])) {
   $contents = h($_POST['contents']);
 } else {
  $contents = "";
 }


  ?>
    <form action="<?php print($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
      <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo h(MAX_FILE_SIZE); ?>">
      <input type="file" name="image"><br><br>
      <input type="text" name="personal_name" placeholder="ニックネーム" value="<?php if(isset($name)){ echo $name; } ?>"><br><br>
      <textarea name="contents" rows="8" cols="40" placeholder="今どんな気持ち？"><?php if(isset($contents)){ echo $contents; } ?></textarea><br><br>
      <input type="submit" name="upload" >
    </form>
  </div>
</body>
</html>