<?php
/*
    Copyright (c) 2011 D. Keith Casey Jr. "caseysoftware"
	Copyright (c) 2015 Artjoms Petrovs    "ArtyCo"
*/

include 'Zendesk.lib.php';

ini_set('display_errors', 0);
error_reporting(0);

define("ZD_SITE", 'xxxxx.zendesk.com');
define("ZD_USER", 'xxxxx@xxxxx.com');
define("ZD_PASS", 'xxxxxxxx');
define("ZD_FIELD",'xxxxxxxx');
define("PLACEHOLDEREMAIL", ZD_USER);
define("EMAIL_DOMAIN", 'youremaildomain.com');

if (isset($_REQUEST)){
    $from    = isset($_REQUEST['From']) ?
                urldecode($_REQUEST['From']) : 'bad phone number';
    $body    = isset($_REQUEST['Body']) ?
                $_REQUEST['Body'] : 'An error occured from this number: '.$from;

    $zd = new Zendesk(ZD_SITE, ZD_USER, ZD_PASS);
	
	$from = urlencode($from);
	$from_search = str_replace('+', '', $from);
	
    $result = $zd->get(ZENDESK_SEARCH, array(
                    'query' => "query=type:ticket+status:new+status:open+" .
                                "status:pending+order_by:updated_at+sort:desc+".
                                "fieldvalue:$from_search"
                )
            );
	//echo $result;
	$result = json_decode($result);
    //var_dump( $result );
	//$attr = $xml->attributes();

    if ($result->count > 0) {
        
		//print_r($result->results);
		$first_ticket = $result->results[0];
		$ticket_id = $first_ticket->id;
        // incoming sms has the same From as an open ticket, just append
        $result = $zd->update(ZENDESK_TICKETS, array(
                'details' => array(
                        'is-public' => true,
                        'comment' => array( "body" => $body, "public" => "true" ),
                        'subject' => substr($body, 0, 80),
                        ),
                'id' => $ticket_id,
                ));
    } else {
        // incoming sms has a new From, so create a new ticket
        $result = $zd->create(ZENDESK_TICKETS, array(
                'details' => array(
                        'comment' => array( body => $body ),
                        'subject' => substr($body, 0, 80),
                        'requester' => array( "name" => $from_search, "email" => uniqid().EMAIL_DOMAIN),
                        'custom_fields' => array (
                            array(
                                'id' => ZD_FIELD,
                                'value' => $from
                            )
                        )
                )));
    }
}

header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response></Response>