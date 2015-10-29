<?php

$from 	= $_GET['From'];
$to  	= $_GET['To'];
$body   = $_GET['Body'];

// Get the PHP helper library from twilio.com/docs/php/install
require_once('lib/Services/Twilio.php'); // Loads the library
 
// Your Account Sid and Auth Token from twilio.com/user/account
$sid = "ACxxxxxxxx"; 
$token = "xxxxxxxxxx"; 
$client = new Services_Twilio($sid, $token);

$message = $client->account->messages->create(array(
    "From" => $_GET['From'],
    "To" => $to,
    "Body" => $body,
));
 
// Display a confirmation message on the screen
echo "Sent message {$message->sid}";
