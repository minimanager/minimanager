/*!
 * MiniManager Login Script v0.0.1
 * http://www.cybrax.org/mm/
 *
 * Copyright (c) 2009 Cybrax Cyberspace
 *
 * Date: 2009-25-11 02:23:00
 * Revision: 1
 */
 
// Global Variable to prevent spamming login.
var LoggingIn = false;

// body onload is unreliable jQuery proper onload to focus username
$(document).ready(function() {
	$("#login_user").focus();
});      

// Login function
function doLogin()
{
	// Declare variables
	var user = $("#login_user");
	var pass = $("#login_pass");

	// If we are already loggin in inform and return;
	if(LoggingIn)
	{
		$('#message').html("Please wait, processing login.");
		user.focus();
		return;
	}

	// prevent spamming of the login button
	LoggingIn = true; 

	// Form validation should primarly be done by user browser
	// to prevent webqueries for simple operations, php acts as failsafe.
	if(user.val() == "" || pass.val() == "")
	{
		ShowMessage("Please enter your username and password", true);
		return;
	}

	var sha1pass = "";
	if(user.val() != "" && pass.val() != "")
	{
		// only sha1 when user and pass are not empty
		sha1pass = hex_sha1(user.val().toUpperCase() + ":" + pass.val().toUpperCase());
		// if were not loggin in and user+pass are not empty the login process is started
		ShowMessage("Processing Login", false);
	}

	// Update the login button.
	$('#btnLogin').html("Wait!");
		
	// remeber me
	var saveme = "0";
	if($("#remember").is(':checked') == true) saveme = "1";
	
	// PostData
	var QueryData = "user=" + user.val() + "&" + "pass=" + sha1pass + "&realm=" + $("#realm").val() + "&remember=" + saveme + "&action=login";
	
	// Initiate AJAX query
	$.ajax({
	  type: "POST",
	  url: "query.php",
	  data: QueryData,
	  success: function(msg){
		  // Callback on success
		  LoginCallBack(msg)
	  }
	});

	// extra security, always force user to type username and password
 	user.val("");
	pass.val("");
	user.focus();
}

// ShowMessage Function
function ShowMessage(msg, bTime)
{
	// Update the contents of the message table
	$('#message').html(msg);
	// Show the message
	$('#message').show('slow');
	if(bTime) // if true the message will dissapear after 2,5 seconds.
		setTimeout ( "ClearMessage();", 2500 );	
}

// ClearMessage Function
function ClearMessage()
{
	// LogginIn to false to enabled another attempt
	LoggingIn = false;
	// Update the text of the login button
	$('#btnLogin').html("Login");
	// Hide the message
	$('#message').hide('slow');
}

// LoginCallback;
function LoginCallBack(data)
{
	// data contains the results from our AJAX query
	// split by ~ Format: [errcode~message]
	rArray = data.split("~");

	switch(rArray[0])
	{
		case "100": // Code 100 success redirect.
			window.location.href = rArray[1];
			break;
		default: // something went wrong display text 
			ShowMessage(rArray[1], true);
	}
}

function checkEnter(e){ 
	var characterCode 
	
	//if which property of event object is supported (NN4)
	if(e && e.which)
	{ 
		e = e
		//character code is contained in NN4's which property
		characterCode = e.which 
	}
	else
	{
		e = event
		//character code is contained in IE's keyCode property
		characterCode = e.keyCode
	}

	//if generated character code is equal to ascii 13 (if enter key)
	if(characterCode == 13)
	{
		doLogin();
		return false 
	}
	else
		return true 
}