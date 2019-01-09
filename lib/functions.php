<?php
//require_once('connect.php');
//require_once __DIR__ . '/infobip/vendor/autoload.php';
//require_once __DIR__ . '/vendor/autoload.php'; // Loads the library
//use Twilio\Rest\Client;
//
//use infobip\api\client\SendSingleTextualSms;
//use infobip\api\configuration\BasicAuthConfiguration;
//use infobip\api\model\sms\mt\send\textual\SMSTextualRequest;

function connect(){
    $mysqli = new mysqli("localhost", "optionex_admin", "4pata34nasa25na!", "optionex_db");
    return $mysqli;
}
function fetchAssocStatement($stmt)
{
    if($stmt->num_rows>0)
    {
        $result = array();
        $md = $stmt->result_metadata();
        $params = array();
        while($field = $md->fetch_field()) {
            $params[] = &$result[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $params);
        if($stmt->fetch())
            return $result;
    }

    return null;
}

function VinSearch($vin){
$vin_details = file_get_contents('https://vpic.nhtsa.dot.gov/api/vehicles/decodevinextended/'.$vin.'?format=json');
$car = json_decode($vin_details);
return $car;
}

function returnVehicle($make, $model){
    $mysqli=  connect();
    $make= strtoupper($make);
    $model = strtoupper($model);
    
    $stmt = $mysqli->prepare("SELECT make, make_id FROM makes WHERE make=? LIMIT 1");
    $stmt->bind_param('s', $make);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows >=1){
    $row=  fetchAssocStatement($stmt);
    $makeid=$row['make_id'];
    }
    else{
    //add to vehicle db
    $stmt1=$mysqli->prepare("INSERT INTO makes (make) VALUES (?)");
    $stmt1->bind_param('s', $make);
    $stmt1->execute();
    $makeid = $stmt1->insert_id;
    $stmt1->close();
    //add to models
    $stmt1=$mysqli->prepare("INSERT INTO model (make_id, model) VALUES (?,?)");
    $stmt1->bind_param('is', $makeid, $model);
    $stmt1->execute();
    $modelid = $stmt1->insert_id;
    $stmt1->close();
    }
    $stmt->close();
    
    //model
    $stmt = $mysqli->prepare("SELECT model_id FROM model WHERE make_id=? AND model=? LIMIT 1");
    $stmt->bind_param('is', $makeid, $model);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows >=1){
    $row=  fetchAssocStatement($stmt);
    $modelid=$row['model_id'];
    }
    else{
    //add to models
    $stmt1=$mysqli->prepare("INSERT INTO model (make_id, model) VALUES (?,?)");
    $stmt1->bind_param('is', $makeid, $model);
    $stmt1->execute();
    $modelid = $stmt1->insert_id;
    $stmt1->close();
    }
    $stmt->close();
    
    $resp= array("makeid"=>$makeid, "make"=>$make, "modelid"=>$modelid, "model"=>$model);
    return $resp;
}

function convertToStringToDate($date) {
    if ($date != "") {
        $newdate = strtotime($date);
        $newdate = date('m/d/Y h:i a', $newdate);
    } else {
        $newdate = "";
    }
    return $newdate;
}

function generatePassword($cc=6){
     $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $cc; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function generateNumber($cc=6){
     $alphabet = "0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $cc; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function formatphone($phone){
    $ph =preg_replace("/[^0-9,.]/", "", $phone);
    if(substr($ph, 0, 3)=="234"){
        $ph= substr_replace($ph,0,0, 3);
    }
    if(substr($ph, 0, 1)!="0"){
        $ph= "0".$ph;
    }
    return $ph;
}

function reversephone($phone){
    $ph =preg_replace("/[^0-9,.]/", "", $phone);
    if(substr($ph, 0, 1)=="0"){
        $ph= substr_replace($ph,"234",0, 1);
    }else{
        $ph=$phone;
    }
    return $ph;
}

function generatepasscode(){
   // $pre = generatePassword(2).strtotime(date('m/d/Y h:ia')).generatePassword(2);
    $pre =strtotime(date('m/d/Y h:i:s'));
    return $pre;
}

function sendtxt($phone, $msg){
    $url="http://www.smslive247.com/http/index.aspx?cmd=sendquickmsg&owneremail=megatech.ngr@gmail.com&subacct=CONTACT080&subacctpwd=contact123&message=".UrlEncode($msg)."&sender=DND_BYPASSEnTrance&sendto=".$phone."&msgtype=0";
/* call the URL */
if ($f = @fopen($url, "r"))
{
$resp = fgets($f, 255);
//$resp = substr($answer, 0, 2);
}else{
    $resp="fail";
}
return $resp;
}

//function sendmst($no, $msg){
//   $no= reversephone($no);
//    $url ="http://192.169.200.81/~kimandseun/api.php?api=sendmst&no=".urlencode($no)."&msg=".urlencode($msg);
//$string = file_get_contents($url);
//$result = json_decode($string, TRUE);
//return $result;
//}

function inmst($no, $msg){
//allow remote access to this script, replace the * to your domain e.g http://www.example.com if you wish to recieve requests only from your server
header("Access-Control-Allow-Origin: *");
//rebuild form data
$postdata = http_build_query(
    array(
        'username' => "seun.sodimu@yahoo.com",
        'password' => "godzilla",
  'message' => $msg,
  'mobiles' => $no,
  'sender' => "EnTrance",
    )
);
//prepare a http post request
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);
//craete a stream to communicate with betasms api
$context  = stream_context_create($opts);
//get result from communication
$result = file_get_contents('http://login.betasms.com/api/', false, $context);
//return result to client, this will return the appropriate respond code
return $result;
}

function sendmst($no, $msg){
    $resp ="";
    $body = chunk_split($msg,'160',' |*| ');
$array = explode("|*|", $body);
foreach ($array as $value) {
    $set =inmst($no, $value);
    $resp .= "|".$set;
}
    
 return $resp;
}


function sendmst2($no, $msg){
//allow remote access to this script, replace the * to your domain e.g http://www.example.com if you wish to recieve requests only from your server
header("Access-Control-Allow-Origin: *");
//rebuild form data
$postdata = http_build_query(
    array(
        'auth' => "vvgfcx-Df5fM",
  'text' => $msg,
  'to' => $no,
  'from' => "EnTrance",
    )
);
//prepare a http post request
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);
//craete a stream to communicate with betasms api
$context  = stream_context_create($opts);
//get result from communication
$result = file_get_contents('http://69.16.239.72:7070/SmsApi/Send', false, $context);
//return result to client, this will return the appropriate respond code
return $result;
}

function sendmstt($no, $msg){
    $no = reversephone($no);
// Initializing SendSingleTextualSms client with appropriate configuration
$client = new SendSingleTextualSms(new BasicAuthConfiguration($_SESSION['smsuser'], $_SESSION['smspwd']));

// Creating request body
$requestBody = new SMSTextualRequest();
$requestBody->setFrom("EnTrance");
$requestBody->setTo($no);
$requestBody->setText($msg);

// Executing request
try {
    $response = $client->execute($requestBody);
    $sentMessageInfo = $response->getMessages()[0];
    $res = array("CurrStat"=>"Sent", "MessageID"=>$sentMessageInfo->getMessageId(), "Receiver"=>$sentMessageInfo->getTo(), "MessageStatus"=>$sentMessageInfo->getStatus()->getName());
   
} catch (Exception $exception) {
    $res = array("CurrStat"=>"Failed", "StatusCode"=>$exception->getCode(), "ErrorMessage"=>$exception->getMessage());
}
return $res;
}

function twilio_sms($no, $msg){
    // Your Account SID and Auth Token from twilio.com/console
$sid = 'AC5d0cea3b4884266f0ebf64346848c514';
$token = '726c4a2ddc03f9ab49a91c1240509b1e';
$client = new Client($sid, $token);

// Use the client to do fun stuff like send text messages!
$client->messages->create(
    // the number you'd like to send the message to
    $no,
    array(
        // A Twilio phone number you purchased at twilio.com/console
        'from' => '+16507726435',
        // the body of the text message you'd like to send
        'body' => $msg
    )
);
}

function percentageOf( $number, $everything, $decimals = 2 ){
    return round( $number / $everything * 100, $decimals );
}


function email_send($to, $subject, $message, $options = array('format' => 'html', 'attachment' => '')) {
	$mg_api_key = 'api:key-be4125cc1b05751e9f72805310234aba';
	$domain = "mg.en-trance.com";
	
	//if($options['format']=='html')
		$message = nl2br($message);
	
	$postfields = array('from' => 'EnTrance Web Admin <support@en-trance.com>',
		'to' => $to,
		'subject' => $subject,
		$options['format'] => $message,
		'h:Reply-To' => 'EnTrance Web Admin <support@en-trance.com>'
	);
	
	if($options["attachment"]!='')
	{
		$postfields["attachment"] = '@'.$options["attachment"];
		
		/*
		$temp_file = $_SERVER["DOCUMENT_ROOT"].'/../temp_files/'.uniqid($r_user["user_id"]).'.html';
		file_put_contents($temp_file,$options["attachment"]);
		$postfields["attachment"] = '@'.$temp_file;
		*/
	}
	//$postfields["attachment"]='@testfile.txt';
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD,  $mg_api_key);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_URL, 'https://api.mailgun.net/v3/mg.en-trance.com/messages');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$j = json_decode(curl_exec($ch), true);
	$info = curl_getinfo($ch);
	
	if($info['http_code'] != 200)
	{
		$j["status"] = "error";
		$j["status_message"] = "sending error message here";
	}
	curl_close($ch);
	
	@unlink($temp_file);
	
	return $j;
}

?>
