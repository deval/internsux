<?php
// get all survey and put in items table
// jalankan jika ada test baru dibuat di limesurvey

// instantiate a new client
include "deval/JsonRPCClient.php";
define( 'LS_BASEURL', 'https://test.suxesstories.com/');  // adjust this one to your actual LimeSurvey URL
define( 'LS_USER', 'intern' );
define( 'LS_PASSWORD', 'Surabaya2024!?#' );
$myJSONRPCClient = new \org\jsonrpcphp\JsonRPCClient( LS_BASEURL.'/admin/remotecontrol' );

// receive session key
$sessionKey= $myJSONRPCClient->get_session_key( LS_USER, LS_PASSWORD );

// receive surveys list current user can read
$surveys = $myJSONRPCClient->list_surveys( $sessionKey );

// filter only active test
$active = array();
foreach($surveys as $survey){
	if($survey['active']=='Y'){
		$active[] = $survey;
	}
}
 

echo "<pre>";
print_r($active, null );
echo "</pre>";

$sql = "select * from items";
$stmt = ExecuteQuery($sql);
$code = array();

if ($stmt->rowCount() > 0) { 
	while ($row = $stmt->fetch()) {
		echo "<pre>";
		print_r($row, null );
		echo "</pre>";
		$code[] = $row['code'];
	}
	echo "<pre>";
	print_r($code, null );
	echo "</pre>";
	$codes = implode(",", $code);
	foreach($active as $test){
		if(strpos($codes,$test['sid'])!== false){
			// sudah ada
			echo $test['sid']." sudah ada<br>";
		} else {
			echo $test['sid']." belum ada<br>";
			$sql = "insert into items (name, code) values ('".$test['surveyls_title']."','".$test['sid']."')";
			$stmt = ExecuteQuery($sql);
		}
	}
} else {
	foreach($active as $test){
		echo $test['sid']." belum ada<br>";
		$sql = "insert into items (name, code) values ('".$test['surveyls_title']."','".$test['sid']."')";
		$stmt = ExecuteQuery($sql);
	}
}



// release the session key
$myJSONRPCClient->release_session_key( $sessionKey );
?>

<?= GetDebugMessage() ?>
