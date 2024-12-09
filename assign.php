    public function rowInserting($rsold, &$rsnew)
    {
    // instantiate a new client
    include "deval/JsonRPCClient.php";
    define( 'LS_BASEURL', 'https://test.suxesstories.com/');  // adjust this one to your actual LimeSurvey URL
    define( 'LS_USER', 'intern' );
    define( 'LS_PASSWORD', 'Surabaya2024!?#' );
    $myJSONRPCClient = new \org\jsonrpcphp\JsonRPCClient( LS_BASEURL.'/admin/remotecontrol' );

    // receive session key
    $sessionKey = $myJSONRPCClient->get_session_key( LS_USER, LS_PASSWORD );
    $sql = "select code from items where `id`=".$rsnew["item_id"];
    $iSurveyID = ExecuteScalar($sql);
    $sql = "select name from items where `id`=".$rsnew["item_id"];
    $test_name = ExecuteScalar($sql);
    $sql = "select name from participants where `id`=".$rsnew["participant_id"];
    $participant_name = ExecuteScalar($sql);
    $sql = "select email from participants where `id`=".$rsnew["participant_id"];
    $participant_email = ExecuteScalar($sql);

    	// prepare participant data to send
    	$aParticipantData = array(array("firstname"=>$participant_name, "email"=>$participant_email));

    	// send participant data and get token
    	$atoken = $myJSONRPCClient->add_participants($sessionKey, $iSurveyID, $aParticipantData, true);

    	// save token 
    	$rsnew["token"] = $atoken[0]["token"];

    	// send email
    	$Email = new Email;
    	$Email->Sender = "no-reply@suxesstories.com";
    	$Email->AddRecipient($participant_email);
    	$Email->Subject = "Your Test Link";
    	$Email->Content = "Hi, " . $participant_name .
    		". This is your test link for " . $test_name . ": " .
    		"https://test.suxesstories.com/index.php/" . $iSurveyID . "?token=" . $atoken[0]["token"];
    	$EmailSent = $Email->Send();

    	// save if success
    	if($EmailSent){
    		$rsnew["email_sent"] = "1";
    	} else {
    		$rsnew["email_sent"] = "0";
    	}
        return true;
    }
