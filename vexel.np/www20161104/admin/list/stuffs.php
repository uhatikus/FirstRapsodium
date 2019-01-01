<?php
session_start ();
?>
<html>
<head>
<link rel="stylesheet" href="/css/admin/list/stuffList.css">
<script type="text/javascript" src="/admin/list/js/ajax/listscripts.js"></script>
<script type="text/javascript" src="/js/ajax/ajax.js"></script>
</head>
<body>

<?php
include_once $_SERVER ['DOCUMENT_ROOT'] . '/default.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/functions.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/admin/admin-menu.php';
?>


	<a href="javascript:showOrHideForm('form1');">Добавить товар</a>
	<form id="form1" enctype='multipart/form-data' class="add"
		action="/admin/list/stuffs.php" method="post">
		<div class="block">
			<div style="width: 200px; float: left; margin-top: 10px;">Артикул:</div>
			<input type="text" name="code"><br>
			<div style="width: 200px; float: left; margin-top: 10px;">Название:</div>
			<input type="text" name="name"><br>
			<div style="width: 200px; float: left; margin-top: 10px;">Цена:</div>
			<input type="text" name="price"><br>
		</div>
		<div class="block">
			<p>
				Относится к категориям:<br> <a href="javascript:addCat();">Добавить
					категорию</a>
			</p>
			<div id='catselectdiv'></div>
		</div>
		<div class="block">
			<p>Характеристики:<br></p>
			<textarea rows="10" cols="80" name="chars"></textarea>
		</div>
		<div class="block">
			Фотографии:<br> Основная фотография: <input type="file"
				name="mainphoto"><br> Альбом: <input type="file" multiple
				name="photo[]"><br>
		</div>
		<div class="block">
			Описание:<br>
			<textarea name="desc" rows="10" cols="80"></textarea><br>
		</div>
		<button type="submit">Залить товар</button>
	</form>
	<br>

<?php

if (post ( "code" ) && post ( "name" ) && post ( "price" )) {
	$code = vpost ( "code" );
	$name = vpost ( "name" );
	$price = vpost ( "price" );
	$prices = serialize ( array (
			$price 
	) );
	$priceTimesChange = serialize ( array (
			time () 
	) );
	
	$cats = array ();
	$subcats = array ();
	for($i = 1; post ( "cat$i" ); $i ++) {
		$cats [] = vpost ( "cat$i" );
		$subcats [] = vpost ( "subcat$i" );
	}
	$cats = serialize ( $cats );
	$subcats = serialize ( $subcats );
	
	$charstring = vpost("chars");
	$chars = explode("\n", $charstring);
	$mainCharacteristic = array ();
	$allCharacteristic = array ();
	for ($i = 0; $i < count($chars); $i++) {
		$string = $chars[$i];
		if ($string {0} == "@") {
			$allCharacteristic [] = substr($string, 1);
			$mainCharacteristic [] = substr($string, 1);
		} else {
			$allCharacteristic [] = $string;
		}
	}

	$mainCharacteristic = serialize ( $mainCharacteristic );
	$allCharacteristic = serialize ( $allCharacteristic );
	
	$index = (mysql_fetch_array ( mysql_query ( "SELECT COUNT(1) FROM stuffs" ) ));
	$index = $index [0] + 1;
	
	
	@mkdir ( $_SERVER ['DOCUMENT_ROOT'] . "/images/stuffs/stuff$index" );
	if (file_exists($_FILES ['mainphoto'] ['tmp_name'])) {
		@copy ( $_FILES ['mainphoto'] ['tmp_name'], $_SERVER ['DOCUMENT_ROOT'] . "/images/stuffs/stuff$index/0.png" );
	}
	
	for($i = 1; $i <= count ( $_FILES ['photo'] ['tmp_name'] ); $i ++) {
		@$photoname = $_FILES ['photo'] ['tmp_name'] [$i - 1];
		if (file_exists($photoname)) {
			@copy ( $photoname, $_SERVER ['DOCUMENT_ROOT'] . "/images/stuffs/stuff$index/$i.png" );
		}
	}
	
	$desc = "";
	$descstring = vpost("desc");
	$descarray = explode("\n", $descstring);
	for ($i = 0; $i < count($descarray); $i++) {
		$string = $descarray[$i];
		if ($string {0} == "\t") {
			if ($string{1} == "=") {
				if ($desc != "") {
					$desc += "</p>";
				}
				$desc += "<p>&nbsp;&nbsp;&nbsp;&nbsp;" . substr($string, 2);
			} else {
				if ($desc != "") {
					$desc += "</p>";
				}
				$desc += "<p><span class='header'>" . substr($string, 1) . "</span><br>";
			}
		} else {
			$desc += $string;
		}
	}
	$desc += "</p>";
	
	$bratan = mysql_query ( "INSERT INTO 
			stuffs(code, name, cats, subcats, prices, priceTimesChange, 
			price, mainCharacteristic, allCharacteristic, description) VALUES ('$code', '$name', 
			'$cats', '$subcats', '$prices', '$priceTimesChange', 
			'$price', '$mainCharacteristic', '$allCharacteristic', '$desc');" );
}
$query = mysql_query ( "SELECT * FROM stuffs" );
while ( $string = mysql_fetch_array ( $query ) ) {
	for($i = 0; $i < count ( $string ); $i ++) {
		echo $string [$i] . ' ';
	}
	echo "<br>";
}

// mysql_query("drop table stuffs");
?>
</body>
</html>