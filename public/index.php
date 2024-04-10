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
    // print_r($data);
    if(strlen($gmail)==0&&strlen($pwd)==0){
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Username and Passsword should not be empty']));
        return $response;
    }
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
        if($gmail==$result['gmail']&&$pwd==$result['password']){
            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Successfully logged in']));
        }else{
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Gmail id or password does not exists please register']));
        }
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'catcherror ']));
        }

    return $response->withHeader('Content-Type', 'application/json');
    
});

$app->post('/registrationflutter',function(Request $request,Response $response){
    $data =  json_decode(file_get_contents('php://input'), true);  
    $name = $data['name'];
    $gmail = $data['gmail'];
    $password = $data['password'];
    $confirmpassword = $data['confirmPassword'];
    if(strlen($name)==0&&strlen($gmail)==0&&strlen($password)&&strlen($confirmpassword)==0){
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Fields are should not be empty']));
        return $response;
    }
    if($password!=$confirmpassword){
        $response->getBody()->write(json_encode(['success' => false, 'message' => "password and confirmpassword doesn't match "]));
        return $response;
    }
    function gmail_verify($gmail){
            if (strpos($gmail,'@pixelexpert.net') > 0) {
                return false; 
            } else {
                return true;
        }     
    }
    if(gmail_verify($gmail)==true){
        $response->getBody()->write(json_encode(['success' => false, 'message' => "provide pixel expert email "]));
        return $response;
    }
    
    try{
        require_once('../backend/dbconnection.php');
        $emailvalidquerry = "SELECT gmail FROM registration;";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($gmail!=$result['gmail']){
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'user already Registered']));
            return $response;
        }
        $query = "INSERT INTO registration(name, gmail, password, confirmPassword) VALUES (:name, :gmail, :password, :confirmPassword);";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':name',$name);
        $stmt->bindParam(':gmail',$gmail);
        
        $timing=[
            'cost' => 12
        ];
        $hshedpwd=password_hash($password,PASSWORD_BCRYPT,$timing);
        $stmt->bindParam(':password',$hshedpwd);
        $hashedconpwd=password_hash($confirmpassword,PASSWORD_BCRYPT,$timing);
        $stmt->bindParam(':confirmPassword',$hashedconpwd);
        $stmt->execute();
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Successfully Registered']));
    }catch(PDOException $e){
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'catcherror'.$e->getMessage()]));

    }
    return $response->withHeader('Content-Type', 'application/json');
    
});

$app->get('/components',function(Request $request,Response $response){
   require_once('../backend/dbconnection.php');
   $query="SELECT Component_name FROM main";
   $stmt=$pdo->prepare($query);
   $stmt->execute();
   $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
   $response->getBody()->write(json_encode($result));
   return $response->withHeader('Content-Type', 'application/json');
});


$app->run();


