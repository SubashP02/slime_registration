<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Add CORS middleware

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    $response = $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

    // Optional: Allow credentials (cookies, authorization headers, etc) to be included in CORS requests
    // $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');

    return $response;
});


$app->get('/details', function (Request $request, Response $response) {
    $response->getBody()->write(include '../frontend/register.php'
    
);
    return $response;
});

$app->post('/register', function (Request $request, Response $response) {
    $dsn = "mysql:host=localhost:3308;dbname=test";
    $username = "root";
    $password = "";

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $data = $request->getParsedBody();
        $username = $data['username'];
        $email = $data['email'];
        $phone = $data['phone_no'];
        $password = $data['password'];
        $query = "INSERT INTO users(name1, email, phone, password1) VALUES (:name, :email, :phone, :password);";
        if(empty($username)||empty($email)||empty($phone)||empty($password)){
            $empty=1;
        }
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':name', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $hashedPassword);
        if($empty){
            $response->getBody()->write("please enter the all fields");
        }else{
            $stmt->execute();
        $response->getBody()->write("Registration successful! Welcome, $username.");
        }
    } catch (PDOException $e) {
        $response->getBody()->write("Registration failed: " . $e->getMessage());
    }

    return $response;
});

$app->get('/login',function (Request $request,Response $response){
       $response->getBody()->write(include '../frontend/login.php');
       return $response;
});

$app->post('/loginbackend',function(Request $request,Response $response){
        $dsn="mysql:host=localhost:3308;dbname=test";
        $dbname="root";
        $dbpass="";
        try {
            $pdo=new PDO($dsn,$dbname,$dbpass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $data=$request->getParsedBody();
            $name=$data['username'];
            $pwd=$data['pwd'];
            $query="SELECT * FROM users WHERE name1= :name;";
            $stmt=$pdo->prepare($query);
            $stmt->bindParam(':name',$name);
            $stmt->execute();
            $result=$stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($pwd,$result['password1'])){
                $response->getBody()->write("you have successfully logged in");
            }else{
                $response->getBody()->write("Invalid password and username");
            }
        } catch (PDOException $e) {
          $response->getBody()->write("login failed " .$e->getMessage());
            die();
        }
        return $response;
});

$app->post('/loginflutter', function (Request $request, Response $response) {
    $data =  json_decode(file_get_contents('php://input'), true);  
    $gmail=$data['Gmail'];
    $pwd=$data['Password'];
    $dsn="mysql:host=localhost:3308;dbname=pixel";
    $dbname="root";
    $dbpass="";
    try {
        $pdo=new PDO($dsn,$dbname,$dbpass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $query="SELECT * FROM userdetails WHERE gmail= :Gmail;";
        $stmt=$pdo->prepare($query);
        $stmt->bindParam(':Gmail',$gmail);
        $stmt->execute();
        $result=$stmt->fetch(PDO::FETCH_ASSOC);
        if($pwd==$result['password']){
            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Successfully logged in']));
        }else{
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Unsuccessfully ']));
        }
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'catcherror ']));
        }

    return $response->withHeader('Content-Type', 'application/json');;
    
});

$app->run();


