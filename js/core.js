$(document).ready(function() {	
	var restservices = 'php/restservices.php';
	$('#submit-sendpacket').on('click', function(event){
		alert('submit-sendpacket is clicked');
		$.getJSON("restservice.php", {
			run: "SendPacket"
		}, function(data) {
			alert('function.. data');
		});
	});
});