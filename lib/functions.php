<?php 

function h($s){
	return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function setToken(){
    $token = sha1(uniqid(mt_rand(), true));
    $_SESSION['token'] = $token;
}

// function outToken(){
//     $_SESSION['token'] = null;
// }

//トークンをセッションから取得
function checkToken(){
    //セッションが空か生成したトークンと異なるトークンでPOSTされたときは不正アクセス
    if(empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])){
    	  var_dump($_SESSION['token']);
  		  var_dump($_POST['token']);
        echo '不正なPOSTが行われました', PHP_EOL;
        exit;
    }
}
?>