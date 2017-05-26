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
// FRIENDS OF FRIEND
$app->get('/api/user/fofriend/{id}', function(request $request, response $response){
		$id = $request->getAttribute('id');

	$sql = "SELECT DISTINCT 
			users.firstname,
			users.surname
			FROM friends f1
			JOIN friends f2 
			ON f1.friend_id = f2.user_id
			JOIN users
			ON users.id = f1.user_id
			WHERE f2.friend_id = '" . $id . "'
			AND f1.user_id not in
			(SELECT user_id
			FROM friends 
			WHERE user_id !='" . $id . "'
			AND friend_id ='" . $id . "')
			AND f1.user_id !='" . $id . "'";
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

//SUGGESTED FRIENDS
$app->get('/api/suggested/{id}', function(request $request, response $response){
		$id = $request->getAttribute('id');

	$sql = "SELECT  distinct
users.firstname,
users.surname
FROM friends f
JOIN friends f2 ON f.friend_id = f2.user_id
JOIN friends f3 ON f2.friend_id = f3.user_id
JOIN users ON f3.friend_id = users.id
WHERE f3.friend_id NOT IN (
SELECT friend_id 
FROM friends 
WHERE user_id = '" . $id . "'
UNION
SELECT friend_id
from friends
WHERE user_id = f2.user_id
)
AND (
SELECT COUNT(*)
from friends 
where friend_id = f3.friend_id
)>=2
AND f.user_id = '" . $id . "'";
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
