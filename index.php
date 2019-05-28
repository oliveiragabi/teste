<?php 
ini_set("display_errors", 1);
error_reporting(E_ALL);

session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User; 
use \Hcode\Model\Category; 

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page();

	$page->setTpl("index");

});

$app->get('/admin', function() {
    User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("index");

});

$app->get('/admin/login', function() {
    
    //desabilitando o header e o footer
	$page = new PageAdmin([
		"header" => false,
		"footer" => false

	]);

	$page->setTpl("login");

});

$app->post('/admin/login', function() {
    User::login($_POST["login"], $_POST["password"]);
    //desabilitando o header e o footer

    header("Location: /ecommerce/index.php/admin");

	exit();

});

$app->get('/admin/logout', function() {
    
    User::logout();

    header("Location: /ecommerce/index.php/admin/login");

	exit();


});

$app->get('/admin/users', function() {
	User::verifyLogin();

	$users = User::listAll();
    
    $page = new PageAdmin();

	$page->setTpl("users", array(
		"users" => $users
	));


});

//acessando via get e tendo como resposta um html
$app->get('/admin/users/create', function() {
	User::verifyLogin();
    
    $page = new PageAdmin();

	$page->setTpl("users-create");


});

$app->get('/admin/users/:iduser/delete', function($iduser) {
	
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /ecommerce/index.php/admin/users");
 	exit;
    
});


$app->get('/admin/users/:iduser', function($iduser) {
	//só o fato de estar como um parâmetro obrigatório de rota, ele ja entende que a funçao consegue enchergar o parametro passado
	User::verifyLogin();

	$users = new User;

	$users->get((int)$iduser);


    $page = new PageAdmin();

	$page->setTpl("users-update", array(
		"users" => $users->getValues()

	));


});

//acessando via post e fazendo o insert dos dados
$app->post("/admin/users/create", function () {

 	User::verifyLogin();

	$users = new User();

 	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

 	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

 		"cost"=>12

 	]);

 	$users->setData($_POST);

	$users->save();

	header("Location: /ecommerce/index.php/admin/users");
 	exit;

});

$app->post('/admin/users/:iduser', function($iduser) {
	
	User::verifyLogin();

	$users = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$users->get((int)$iduser);

	$users-> setData($_POST);

	$users->update();

	header("Location: /ecommerce/index.php/admin/users");
 	exit;
    
});

$app->get('/admin/forgot', function() {

    //desabilitando o header e o footer
	$page = new PageAdmin([
		"header" => false,
		"footer" => false

	]);

	$page->setTpl("forgot");
    
});

$app->post('/admin/forgot', function() {

    $user = User::getForgot($_POST["email"]);

    header("Location: /ecommerce/index.php/admin/forgot/sent");
    exit;
    
});

$app->get('/admin/forgot/sent', function() {

    $page = new PageAdmin([
		"header" => false,
		"footer" => false

	]);

    $page->setTpl("forgot-sent");
    
});

$app->get('/admin/forgot/reset', function() {

	$user = User::validForgotDescrypt($_GET["code"]);

    $page = new PageAdmin([
		"header" => false,
		"footer" => false

	]);

    $page->setTpl("forgot-reset", array(
    	"name"=>$user["desperson"],
    	"code" => $_GET["code"]
    ));
    
});


$app->get('/admin/categories', function(){

	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories", [
		"categories"=>$categories
	]);
});

$app->get('/admin/categories/create', function(){

	User::verifyLogin();

	$page = new PageAdmin();
	
	$page->setTpl("categories-create");
});


$app->post('/admin/categories/create', function(){

	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header("Location: /ecommerce/index.php/admin/categories");
    exit;
});


$app->get('/admin/categories/:idcategory/delete', function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header("Location: /ecommerce/index.php/admin/categories");
    exit;
});


$app->get('/admin/categories/:idcategory', function($idcategory){

	User::verifyLogin();

	$category = new Category();

	//tudo q vem na url é convertido p texto, ai precisa fazer o cast pra inteiro aqui no codigo

	$category->get((int)$idcategory);

	$page = new PageAdmin();
	
	$page->setTpl("categories-update", [

		"category"=>$category->getValues()

	]);

});


$app->post('/admin/categories/:idcategory', function($idcategory){

	User::verifyLogin();

	$category = new Category();

	//tudo q vem na url é convertido p texto, ai precisa fazer o cast pra inteiro aqui no codigo

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();

	header("Location: /ecommerce/index.php/admin/categories");
    exit;
	
});




$app->run();

 ?>