/*!
 * MiniManager Login Script v0.0.1
 * http://www.cybrax.org/mm/
 *
 * Copyright (c) 2009 Cybrax Cyberspace
 *
 * Date: 2009-25-11 02:23:00
 * Revision: 1
 */
 
var LoggingIn = false;

$(document).ready(function() {
	$("#login_user").focus();
});      
 
function doLogin()
{
	var user = $("#login_user");
	var pass = $("#login_pass");

	if(LoggingIn)
	{
		$('#message').html("Please wait");
		user.focus();
		return;
	}
	
//	$('#message').hide('slow');
	if(user.val().length != 0 && pass.val().length != 0)
		ShowMessage("Processing Login", false);
	else
		LoggingIn = true;

	$('a.btnLogin').html("Wait.");
//	$('#btnLogin').val("Wait.");
	
	var sha1pass = hex_sha1(user.val().toUpperCase()+":"+pass.val().toUpperCase());
	var saveme = 0;
	if($("#remember").is(':checked') == true)
		saveme = 1;
		
	$.ajax({
	  type: "POST",
	  url: "query.php",
	  data: "user=" + user.val() + "&" + "pass=" + sha1pass + "&realm=" + $("#realm").val() + "&remember=" + saveme,
	  success: function(msg){
		LoginCallBack(msg)
	  }
	});
 
 	user.val("");
	pass.val("");
	user.focus();
}

function ShowMessage(msg, bTime)
{
	$('#message').html(msg);
	$('#message').show('slow');
	if(bTime)
		setTimeout ( "ClearMessage();", 2500 );	
}

function ClearMessage()
{
	LoggingIn = false;
	$('#message').hide('slow');
	$('#btnLogin').html("Login");
}

function LoginCallBack(data)
{
	rArray = data.split("~");

	switch(rArray[0])
	{
		case "100":
			window.location.href = rArray[1];
			break;
		default:
			ShowMessage(rArray[1] + rArray[0], true);
	}
}