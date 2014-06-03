<?php 
session_start();
//connect to db
$conn = new PDO('mysql:host=localhost;dbname=TagPoint', 'root', 'sascha302');
//INSERT REQUESTS
/*
require 'vendor/autoload.phar';
use GeoIp2\Database\Reader;
if(isset($_GET['geoip']))
{
	echo 'This creates the Reader object, which should be reused across<br/>';

		$reader = new Reader('/usr/local/share/GeoIP/GeoIP2-City.mmdb');
		
		$record = $reader->city('128.101.101.101');
		
		echo $record->country->isoCode . "\n"; // 'US'
		echo $record->country->name . "\n"; // 'United States'
		echo $record->country->names['zh-CN'] . "\n"; // '美国'
		
		echo $record->mostSpecificSubdivision->name . "\n"; // 'Minnesota'
		echo $record->mostSpecificSubdivision->isoCode . "\n"; // 'MN'
		
		echo $record->city->name . "\n"; // 'Minneapolis'
		
		echo $record->postal->code . "\n"; // '55455'
		
		echo $record->location->latitude . "\n"; // 44.9733
		echo $record->location->longitude . "\n"; // -93.2323
		
	
}
*/
//TABLE 'Users'--------------------------------------------------------------------------------------------
switch ($_GET['Users']) 
{
	case 'isLogOn':
		if($_SESSION['isLogOn'])
			echo '['.json_encode(array('isLogOn'=>true)).']';
		else
			echo '['.json_encode(array('isLogOn'=>false)).']';
	break;
	case 'logOn':
		$Q = $conn->prepare('SELECT * FROM Users WHERE email=? AND passwd=? LIMIT 0 , 1');
		if($Q->execute(array($_GET['email'], $_GET['passwd'])))
		{
			if($Q->rowCount() > 0)
			{
				$_SESSION['isLogOn']=true;
				echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
			}
		}
	break;
	case 'LogOff':
		unset($_SESSION['isLogOn']);
	break;
	case 'getStartTagsIdsByUserId':
		$Q = $conn->prepare('SELECT startTags FROM Users WHERE id=?');
		if($Q->execute(array($_GET['userId'])))
			if($Q->rowCount() > 0)
				echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
	break;
	case 'getStartChannelsByUserId':
		$Q = $conn->prepare('SELECT startChannels FROM Users WHERE id=?');
		if($Q->execute(array($_GET['userId'])))
		{
			if($Q->rowCount() > 0)
			{
				//convert 'startChannels'-string to array
				$row = $Q->fetch(PDO::FETCH_ASSOC);
				$channelsIdsArray = explode(',', $row['startChannels']);
				//lookup any startChannelsIds in 'Channels'-table, then get the row by id
				//fill it into $latArray and $lngArray
				$sqlGetChannelsById = 'SELECT * FROM Channels WHERE id=?';
				$jsonString='';
				for ($i = 0; $i < count($channelsIdsArray); $i++)
				{
					//echo 'lookUpTagId: '.$tagsIdsArray[$i].'<br/>';
					$Q = $conn->prepare($sqlGetChannelsById);
					if($Q->execute(array($channelsIdsArray[$i])))
						if($Q->rowCount() > 0)		
						{				
							foreach ($Q->fetchAll(PDO::FETCH_ASSOC) as $tag) {
								$jsonString .= json_encode($tag).',';
							}	
						}
				}
				echo '['.substr($jsonString,  0, strlen($jsonString)-1).']';
			}
		}
		break;
}

//if($_SESSION['isLogOn']==true)
//{
//TABLE 'Channelss'--------------------------------------------------------------------------------------------
switch ($_GET['Channels'])
{
	case 'getChannelsByUserId':
		$Q = $conn->prepare('SELECT * FROM Channels WHERE fkUserId=?');
		if($Q->execute(array($_GET['userId'])))
			if($Q->rowCount() > 0)
				echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
	break;
	case 'getLastSelectedChannelByUserId':
		$Q = $conn->prepare('SELECT Users.lastSelectedChannelId
		FROM Tags
		INNER JOIN Users ON Tags.id = Users.lastSelectedChannelId
		WHERE Users.id = ?
		LIMIT 0 , 30');
		if($Q->execute(array($_GET['userId'])))
			if($Q->rowCount() > 0)
				echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
	break;
	case 'getChannelById':
		$Q = $conn->prepare('SELECT * FROM Channels WHERE id = ?');
		if($Q->execute(array($_GET['id'])))
			if($Q->rowCount() > 0)
				echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
	break;
	case 'getChannelsByUserId':
		$Q = $conn->prepare('SELECT * FROM Channels WHERE fkUserUd = ?');
		if($Q->execute(array($_GET['userId'])))
			if($Q->rowCount() > 0)
				echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
		break;
	case 'createNewTag':
		$Q = $conn->prepare('INSERT INTO Channels VALUES (NULL, ?, NOW(), ?, ?, ?, ?, ?)');
		if($Q->execute(array($_GET['title'], $_GET['userId'], $_GET['lat'], $_GET['lng'], $_GET['motd'], $_GET['icon'])))
		{
			$Q = $conn->prepare('SELECT * FROM Channels WHERE id=?');
			if($Q->execute(array($_GET['id'])))
				if($Q->rowCount() > 0)
					echo json_encode($Q->fetchAll(PDO::FETCH_ASSOC));
		}
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