<?php 

ini_set('display_errors', 1);
define('MAX_FILE_SIZE', 1 * 1024 * 1024); //1MB
define('THUMBNAIL_WIDTH', 400);
define('IMAGES_DIR', __DIR__ . '/../images');
define('THUMBNAIL_DIR', __DIR__ . '/../thumbs');


if (!function_exists('imagecreatetruecolor')) {
	echo "GD not installed";
	exit;
}

require (__DIR__ . '/../lib/ImageUploader.php');
require_once(__DIR__ . '/../config/config.php');

$app = new MyApp\Controller\Index();

$app->run();

$app->me();


$uploader = new \MyApp\ImageUploader();



define("ARTICLE_MAX_NUM","4");
define("FIRST_VISIT_PAGE","1");

$keijban_file = 'keijiban.txt';
$count = sizeof(file('keijiban.txt'));
$tweet_count = $count/5;

$article_num = $tweet_count;
$index_num = ARTICLE_MAX_NUM*5;
$max_page = ceil($tweet_count / ARTICLE_MAX_NUM);
$articles = file( $keijban_file );

if(!isset($_GET['page'])){
    $now_page = FIRST_VISIT_PAGE;
}else if(preg_match("/^[1-9][0-9]{n}$/",$_GET['page'])){
    $now_page = h($_GET['page']);
}
else {
  $now_page = FIRST_VISIT_PAGE;
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
    <form action="logout.php" method="post" id="logout">
      <?= h($app->me()->email); ?><input type="submit" value="Log Out">
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
    </form>
  </div>

  <h2>掲示板</h2>

	<a href="/new.php"><h4>投稿する</h4></a>

<!-- 	<ul>
      <?php foreach ($images as $image) : ?>
        <li>
          <a href="<?php echo h(basename(IMAGES_DIR)) . '/' / basename($image); ?>">
            <img src="<?php echo h($image); ?>">
          </a>
        </li>
       <?php endforeach; ?>
    </ul> -->

	<?php

/** 記事表示 **/
$start = ($now_page - 1) * $index_num;
$output = array_slice($articles,$start,$index_num);
echo '全記事数：'.$article_num.' 現在のページ：'.$now_page;


if(empty($output)){
    echo "記事はありません<br>";
}else{
    foreach($output as $val){
        echo $val.'<br>';
    }
}
 
if($now_page > 1){
    echo '<a href=\'?page='.($now_page-1).'\')><-前へ </a>';
}
if($now_page < $max_page){
    echo '<a href=\'?page='.($now_page+1).'\'> 次へ-></a>';
}


	?>
</body>
</html>