<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//ALL USERS
$app = new \Slim\App;
$app->get('/api/users', function(request $request, response $response){
	$sql = "SELECT * FROM users ";
	try{
		//DB object
		$db =  new db();
		//Connect do DB
		$db = $db->connect();
		$stmt = $db->query($sql);
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);
		#$db = null;
		echo json_encode($users);

	}	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}';
	}
});

//SINGLE USER
#$app = new \Slim\App;
$app->get('/api/user/{id}', function(request $request, response $response){
	$id = $request->getAttribute('id');
	$sql = "SELECT
        users.`firstname` as firstname,
        users.`surname`,
        users.id
    FROM
        friends
    LEFT JOIN
        users
    ON
        users.`id` = friends.`user_id`
    AND
        users.`id` != '" . $id . "'
    WHERE
        friends.`friend_id` = '" . $id . "'";
	try{
		//DB object
		$db =  new db();
		//Connect do DB
		$db = $db->connect();
		$stmt = $db->query($sql);
		$user = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($user);

	}	catch(PDOException $e){
		echo '{"error": {"text":'.$e->getMessage().'}';
	}
});
