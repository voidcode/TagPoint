<?php
//session_start();
//connect to db
$dbh = new PDO('mysql:host=localhost;dbname=ChannelPoint', 'root', 'sascha302');

if(isset($_POST['email']) && isset($_POST['passwd']))
{
	$stmt = $dbh->prepare('SELECT * FROM users WHERE email=? AND passwd=? LIMIT 0 , 1');
	if($stmt->execute(array($_POST['email'], $_POST['passwd'])))
	{
		while($row = $stmt->fetch())
		{
			$_SESSION['isLogOn'] = true;
			$_SESSION['userid'] = $row['id'];
			$_SESSION['user_channels'] = preg_replace('/\s+/', '', explode(',', $row['listening_on']));
			$_SESSION['user_email'] = $_POST['email'];
		}
	}
}

if(isset($_GET['view']))
{
	$_SESSION['select_channel'] = $_GET['view'];
}

if($_SESSION['isLogOn']===true)
{
	//USER OS
}
else if(isset($_POST['newPassword']) && isset($_POST['email']) )
{
	$emne = "New password for your ChannelPoint"; //Emnefeltet
	$besked = "<h1 style='background-color: #006699'>
		        THIS IS HARD CODE!!
		      </h1>";
	$header  = "MIME-Version: 1.0" . "\r\n";
	$header .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
	$header .= "from:postmaster@cnode.dk";
	mail($_POST['email'], $emne, $besked, $header); //Send!!
	header("Location:logon.php?status=email-sendt");
	$_SESSION['isLogOn'] = false;
}
//else
	//header("Location:logon.php?status=access-denied");
?>
<!DOCTYPE="html">
<html>
	<head>
		<link href='http://fonts.googleapis.com/css?family=Ubuntu&subset=cyrillic,latin' rel='stylesheet' type='text/css' />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<meta charset="utf-8">
		<link rel="shortcut icon" href="images/icon.png" />
		<link rel="stylesheet" href="css/default-stylesheet.css" type="text/css" >
	</head>
	<body onload="joinChannel('test');">
		<div id="wapper">
			<div id="top">
				<div id="top-left">
					<a href="logon.php?sessionsdestroy" title="LogOff">GroupPoint</a>
				</div>	
				<div id="top-rigth">
					<a href="javascript:void(0)" onclick="document.getElementById('popup-box').style.display='block';document.getElementById('popup-overlaylink').style.display='block'" title="">
						<img src="images/profile_icon.png"/>
					</a>
        			<div id="popup-box" class="popup-box">
							<ul>
								<li>
									<h1><img src="images/profile_icon.png"/>Your profile</h1>
								</li>
								<li>
									<label name="email">Fristname</label>
								</li>
								<li>
									<input type="text" name="text" placeholder="Add your fristname" value="" class="fristname"/>
								</li>
								<li>
									<label name="email">Lastname</label>
								</li>
								<li>
									<input type="text" name="text" placeholder="Add your lastname" value="" class="lastname"/>
								</li>
								<li>
									<label name="email">Email</label>
								</li>
								<li>
									<input type="text" name="email" placeholder="Your e-mail" value="terkelsorensen@gmail.com"/>
								</li>
							</ul>	
						<a href = "javascript:void(0)" onclick = "document.getElementById('popup-box').style.display='none';document.getElementById('popup-overlaylink').style.display='none'">Close</a>
					</div>
        			<div id="popup-overlaylink" class="popup-overlay"></div>
				</div>		
			</div>
			<div id="content">
				<div id="content-left">
					<h1>listening on:</h1>
					<div id="content-left-addchannel">
						<input class="inputChannelTitle" type="text" placeholder="#" />
						<a class="btnCeleteChannel" title="Remove an channel" href="#"><img src="images/minus.png"/></a>
						<a class="btnNewChannel" title="Add an channel" href="#"><img src="images/plus.png"/></a>
					</div>
					<ol class="getChannelByUserId"></ol>
				</div>
				<div id="content-middle">
					<div id="content-middle-top">
						<form action="" method="post">
							<div id="content-middle-top-left">
								<textarea wrap="hard" placeholder="Share something . . ." name="contents"></textarea>
							</div>
							<div id="content-middle-top-rigth">
								<input type="submit" value="Send" name="submit_sendpacket" id="submit-sendpacket"/>
							</div>
						</form>
						
					</div>
					<script type="text/javascript">
					$('.getChannelByUserId').on('click','li', function(event){
						alert($('.getChannelByUserId li').val());	
					});
					
					$('.btnNewChannel').on('click', function(event){
						if($('.inputChannel').val() !="")
						{
							$.post('restservices.php', {
								func: 'newChannel',
								title: $('.inputChannelTitle').val()
							}).done(function(data){
								$('.getChannelByUserId').empty();
								LoadChannelByUserId();//updaye
							});
						}
						else
							$('.inputChannel').css('background-color', 'red');
					});
					var LoadChannelByUserId = function(){
						$.getJSON('restservices.php', { 
								func: 'getChannelBy'
							}, function(data) {
						    $.each(data, function(index, element) {
						        $('.getChannelByUserId').append($('<li>', {
						            text: element.title
						        }).append($('<a>', { href: element.title})));
						    });
						});
					};
					LoadChannelByUserId();

					$('.btnLogOn').on('click', function(event) {
						$.getJSON('restservices.php', { 
							Tags: 'getTagsByUserId',
							userId: ,
							passwd: $('.passwd').val()
						}, function(user) {
							alert('User->token: '+user[0].token);
							getLastSelectedTagByUserId(user[0].id);
						});
					});
					</script>
					<div id="content-middle-buttom">
					<?php 
						if(isset($_GET['view']) )
						{
							$emailInMsgbox =null;
							$Q = $dbh->prepare('CALL getChannelByTitle(?, ?);');
							if($Q->execute(array($_GET['view'], 30))) //parm: channel, limit/max
							{
								if($Q->rowCount() > 0)
								{
									$emailInMsgbox = array();
										while($row = $Q->fetch(PDO::FETCH_ASSOC))
										{
											if(!in_array($row['email'], $emailInMsgbox))
											{
												array_push($emailInMsgbox, $row['email']);
											}
											echo '<div class="msgbox-item">';
												echo '<div class="msgbox-item-left">';
													echo '<img src="http://www.gravatar.com/avatar/'.md5( strtolower( trim( $row['email'] ) ) ).'?s=60"/>';
													echo '<p style="font-size:8px;">'.$row['create_at'].'</p>';
												echo '</div>';
												echo '<div class="msgbox-item-rigth">';
													echo '<p>'.$row['contents'].'</p>';
												echo '</div>';
											echo '</div>';	
										}
								}
								else
								{
									echo '<div class="msgbox-item">';
									echo 'This channel is empty!';
									echo '</div>';
								}
							}
						}
					?>
					</div>
				</div>
				<div id="content-rigth">
					<?php
					$i=0;
					foreach($emailInMsgbox as &$email)
					{	
						if($i >=2)
						{
							echo '<br/>';
							$i=0;
						}
echo '<img src="http://www.gravatar.com/avatar/'.md5( strtolower( trim( $email ) ) ).'?s=60" title="View only packets from: '.$email.'"/>';
						$i++;
					}
					if(count($emailInMsgbox) > 0)
						echo '<br/>'.count($emailInMsgbox).' writers';	
				 	?>
				</div>
			</div>
		</div>		
	</body>
</html>