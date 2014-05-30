<?php 
session_start();
//connect to db
$conn = new PDO('mysql:host=localhost;dbname=TagPoint', 'root', 'sascha302');
//INSERT REQUESTS

//TABLE 'Users'--------------------------------------------------------------------------------------------
switch ($_GET['Users']) 
{
	case 'logOn':
		$Q = $conn->prepare('SELECT * FROM Users WHERE email=? AND passwd=? LIMIT 0 , 1');
		if($Q->execute(array($_GET['email'], $_GET['passwd'])))
		{
			if($Q->rowCount() > 0)
			{
				$_SESSION['isLogOn']=true;
				echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
			}
			else
				echo json_encode(array('error'=>'Not an real password or email'));
		}
	break;
	case 'LogOff':
		unset($_SESSION['isLogOn']);
	break;
}

//if($_SESSION['isLogOn']==true)
//{
//TABLE 'Tags'--------------------------------------------------------------------------------------------
switch ($_GET['Tags'])
{
	/*Get an Packet base on Users.id*/
	case 'getTagsByUserId':
		$Q = $conn->prepare('SELECT * FROM Tags WHERE fkUserId=?');
		if($Q->execute(array($_GET['userId'])))
			if($Q->rowCount() > 0)
				echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
	break;
	case 'getLastSelectedTagByUserId':
		$Q = $conn->prepare('SELECT Users.lastSelectedTagId
		FROM Tags
		INNER JOIN Users ON Tags.id = Users.lastSelectedTagId
		WHERE Users.id = ?
		LIMIT 0 , 30');
		if($Q->execute(array($_GET['userId'])))
			if($Q->rowCount() > 0)
				echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
	break;
	case 'getTagById':
		$Q = $conn->prepare('SELECT * FROM Tags WHERE id = ?');
		if($Q->execute(array($_GET['id'])))
			if($Q->rowCount() > 0)
				echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
	break;
}
//TABLE 'Packets'-----------------------------------------------------------------------------------------
switch ($_GET['Packets'])
{
	case 'getPacketByUserId':
		$Q = $conn->prepare('SELECT * FROM Tags WHERE fkUserId=?');
		if($Q->execute(array($_GET['userId'])))
			if($Q->rowCount() > 0)
				echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
	break;
	case 'getPacketByTagId':
		$Q = $conn->prepare('SELECT * FROM Tags WHERE fkTagId=?');
		if($Q->execute(array($_GET['tagId'])))
			if($Q->rowCount() > 0)
				echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
	break;
	case 'getPacketByTagIdAndUserId':
		$Q = $conn->prepare('SELECT * FROM Tags WHERE fkTagId=? AND fkUserId=?');
		if($Q->execute(array($_GET['userId'], $_GET['tagId'])))
			if($Q->rowCount() > 0)
				echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
	break;
}

//} else { echo json_encode(array('msg'=>'You need to do an logOn!')); }
?>