<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app->get('/admin', function() { //Rota 

	User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("index");	

});


$app->get('/admin/login', function(){ //Rota chama a tela de login da administração do site

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false

	]);

	$page->setTpl("login");

});

	$app->post('/admin/login', function(){

		User::login($_POST["login"], $_POST["password"]);

		header("Location: /admin");
		exit;

});

$app->get('/admin/logout', function(){

	User::logout();

	header("Location: /admin/login");
	exit;
});

$app->get("/admin/forgot", function(){ //Abre a tela de redefinir a senha

	$page = new PageAdmin([ // oculta o header e footer
		"header"=>false,
		"footer"=>false

	]);

	$page->setTpl("forgot");

});

$app->post("/admin/forgot", function(){

	$_POST["email"];
	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/send");
	exit;
});

$app->get("/admin/forgot/send", function(){

	$page = new PageAdmin([ // oculta o header e footer
		"header"=>false,
		"footer"=>false

	]);

	$page->setTpl("forgot-send");


});

$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([ // oculta o header e footer
		"header"=>false,
		"footer"=>false

	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
	
});


$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=>10 //código para salvar o hash no banco 
	]);
		

	$user->setPassword($password);

	$page = new PageAdmin([ // oculta o header e footer
		"header"=>false,
		"footer"=>false

	]);

	$page->setTpl("forgot-reset-success");


	});


?>