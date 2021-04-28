<?php
session_start();
//echo '<pre>',print_r($_SERVER),'</pre>';

// block XSite
if(preg_match("/curl|libcurl/i", $_SERVER['HTTP_USER_AGENT'])){
	exit;
}
if(!isset($_SESSION['tm'])){$_SESSION['tm']=0;}
if(!isset($_SESSION['blc'])){
	$_SESSION['blc'] = 0;
}else{
	if($_SESSION['blc'] === 5){
		echo "mbalik";
		session_destroy();
		header('location:index.php');
		exit;
	}
}
if(!isset($_SERVER['HTTP_REFERER']) && !empty($_POST)){
	echo "kosong kabeh";
	exit;
}	
if(isset($_SERVER['HTTP_REFERER']) && !empty($_POST)){
	if(isset($_SERVER['HTTP_REFERER'])){
		$ref = str_replace(array('https://', 'http://'), "" , $_SERVER['HTTP_REFERER']);
		$uri = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		//echo $ref.' !== '.$uri;
		if($ref !== $uri){
			echo "ref gak podo";
			exit;
		}
	}else{
		echo "ref gak onok";
		exit;
	}
	
	//print_r($_SESSION);
	if($_SESSION['tm'] !== 0){
		//echo $_SESSION['tm'] - round(microtime(true) * 1000);
		if(($_SESSION['tm'] - round(microtime(true) * 1000)) > -8){
			$_SESSION['blc']++;
			echo "itung ses";
			exit;
		}
	}else{
		echo "itungan kosong";
		exit;
	}
	
}
	
$_SESSION['tm'] = round(microtime(true) * 1000);
// end block XSite

$c = mysqli_connect("localhost", "root", "", "masjidq");

class Jimat{
	
	function _sqli($c, $str, $jml){
		$r = substr(mysqli_real_escape_string($c, $str), 0, $jml);
		return $r;
	}

	function _xss($str){
		return htmlentities($str);
	}

	function genpas($str){
		return sha1(md5(sha1($str)));
	}

	function ismail($email){ 
		return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
	}

	function token() {
		$length = rand(100,150);
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}
