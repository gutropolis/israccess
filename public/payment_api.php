<?php
$data=array();

$data['expiration_date'] = '02/21';
$data['total_amount'] = '20';
$data['currency'] = '1';
$data['credit_card_number'] = '12312312';
 
 


 //get a token for the credit card
$token = donfo_create_token($data );
if ($token !=''){
	//if there is a token - make a tranaction
	$confirmationcode = donfo_transaction($data ,$token);
	if ($confirmationcode != ''){
		echo '<h2>Confirmation code is genratiing</h2>';
	}
}else{
	
	echo '<h2>Please make all correction in your payment gateway!!</h2>';
}
 

/**
* Send data from Caldera form to Tranzila to receive the token
*/
function donfo_create_token($data){
    $tranzila_api_host = 'https://secure5.tranzila.com';
    $tranzila_api_path = '/cgi-bin/tranzila71u.cgi';
    $supplier = 'acarragrotok'; // enter your supplier name
    $tranzilapw = 'Il37LK'; //enter your tranzila pw
    $tranzilatoken = '';
    
    $expdate = str_replace("/", "", $data['expiration_date']);
    $expdate = preg_replace('/\s+/', '', $expdate);
	// Prepare transaction parameters>
	$query_parameters['supplier'] = $supplier;
  // $query_parameters['sum'] = $data['total_amount']; //Transaction sum
//	$query_parameters['currency'] = $data['currency']; //Type of currency 1 NIS, 2 USD, 978 EUR, 826 GBP, 392 JPY
	$query_parameters['ccno'] = $data['credit_card_number']; // Test card number = '12312312'
//    $query_parameters['expdate'] = $expdate ;// Card expiry date: mmyy ='0820'
    if($data['currency'] == 1){
  //     $query_parameters['myid'] = $data['id_number']; // ID number = '12312312'
    }
    else{
  //      $query_parameters['myid'] = '';
    }
 //   $query_parameters['cred_type'] = '1'; // This field specifies the type of transaction, 1 - normal transaction, 6 - credit, 8 - payments
	$query_parameters['TranzilaPW'] = $tranzilapw; // Token password if required
  //  $query_parameters['tranmode'] = 'V'; // Mode for verify transaction
    $query_parameters['TranzilaTK'] = 1;
	// Prepare query string
	$query_string = '';
    foreach ($query_parameters as $name => $value) {
        $query_string .= $name . '=' . $value . '&';
    }
    $query_string = substr($query_string, 0, -1); // Remove trailing '&'
    // Initiate CURL
	
	 
    $cr = curl_init();
    curl_setopt($cr, CURLOPT_URL,$tranzila_api_host.$tranzila_api_path);
    curl_setopt($cr, CURLOPT_POST, 1);
    curl_setopt($cr, CURLOPT_FAILONERROR, true);
    curl_setopt($cr, CURLOPT_POSTFIELDS, $query_string);
    curl_setopt($cr, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($cr, CURLOPT_SSL_VERIFYPEER, 0);
    // Execute request
    $result = curl_exec($cr);
    $error = curl_error($cr);
    if (!empty($error)) {
        return ($error);
    }
    curl_close($cr);print_r($result);exit;
    // Preparing associative array with response data
    $response_array = explode('&', $result);print_r($response_array);exit;
    if (count($response_array) >= 1) {
        foreach ($response_array as $value) {
            $tmp = explode('=', $value);
            if (count($tmp) > 1) {
                $response_assoc[$tmp[0]] = $tmp[1];
            }
        }
    }
   // Analyze the result string
    if (!isset($response_assoc['TranzilaTK'])) {
        //getting a token was unsuccessful
		
        //getting a token was unsuccessful 
       
		foreach($response_assoc as $k=> $v){
			$res = 	filter_var($v, FILTER_SANITIZE_STRING);
			        echo '<div class="error">'.$k.': <p>'.$res.'</p></div>';
			
		} 
         
    }  else {
        $tranzilatoken = $response_assoc['TranzilaTK']; //TranzilaTK
       //submit tranzila token  value into the tranzilatoken field
        
    }
    return $tranzilatoken;
}

/**
* Send transaction data from Caldera form to Tranzila and recieve confirmation code.
*/
function donfo_transaction($data ,$token){
    $tranzila_api_host = 'https://secure5.tranzila.com';
    $tranzila_api_path = '/cgi-bin/tranzila71u.cgi';
    $supplier = 'supplier-name'; // enter your supplier name
    $tranzilapw = 'Expert75'; //enter your tranzila pw
    $field_id = 'fld_7259702'; //confirmation field
    $tranzilaconfirmation = '';
    $expdate = str_replace("/", "", $data['expiration_date']);
    $expdate = preg_replace('/\s+/', '', $expdate);
    // Prepare transaction parameters>
    $query_parameters['supplier']   = $supplier;
    $query_parameters['sum']        = $data['total_amount'];       //Transaction sum
    $query_parameters['currency']   = $data['currency'];           //Type of currency 1 NIS, 2 USD, 978 EUR, 826 GBP, 392 JPY
    $query_parameters['ccno']       = $data['credit_card_number']; // Test card number = '12312312'
    $query_parameters['expdate']    = $expdate ;                   // Card expiry date: mmyy ='0820'
    if($data['currency'] == 1){
        $query_parameters['myid'] = $data['id_number']; // ID number = '12312312'
     }
     else{
         $query_parameters['myid'] = '';
     }
    $query_parameters['cred_type']  = '1';                         // This field specifies the type of transaction, 1 - normal transaction, 6 - credit, 8 - payments
    $query_parameters['TranzilaPW'] = $tranzilapw;                 // Token password if required
    $query_parameters['tranmode']   = 'A';                         // Mode for verify transaction
    $query_parameters['TranzilaTK'] = $token;
    /* if ($data['frequency']!= 1){ if there are payments
        //multiple payments
        $query_parameters['fpay'] = $data['total_amount'];
        $query_parameters['spay'] = $data['total_amount'];
        $query_parameters['npay'] = 12;
    } */
    // Prepare query string
    $query_string = '';
    foreach ($query_parameters as $name => $value) {
        $query_string .= $name . '=' . $value . '&';
    }
    $query_string = substr($query_string, 0, -1); // Remove trailing '&'
    // Initiate CURL
    $cr = curl_init();
    curl_setopt($cr, CURLOPT_URL,$tranzila_api_host.$tranzila_api_path);
    curl_setopt($cr, CURLOPT_POST, 1);
    curl_setopt($cr, CURLOPT_FAILONERROR, true);
    curl_setopt($cr, CURLOPT_POSTFIELDS, $query_string);
    curl_setopt($cr, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($cr, CURLOPT_SSL_VERIFYPEER, 0);
    // Execute request
    $result = curl_exec($cr);
    $error = curl_error($cr);
    if (!empty($error)) {
        die ($error);
    }
    curl_close($cr);
    // Preparing associative array with response data
    $response_array = explode('&', $result);
    $response_assoc = array();
    if (count($response_array) > 1) {
        foreach ($response_array as $value) {
            $tmp = explode('=', $value);
            if (count($tmp) > 1) {
                $response_assoc[$tmp[0]] = $tmp[1];
            }
        }
    }
	print_r($response_assoc);
    // Analyze the result string
    if (!isset($response_assoc['Response'])) {
        /**
         * When there is no 'Response' parameter it either means
         * that some pre-transaction error happened (like authentication
         * problems), in which case the result string will be in HTML format,
         * explaining the error, or the request was made for generate token only
         * (in this case the response string will contain only 'TranzilaTK'
         * parameter)
         */
    } else if ($response_assoc['Response'] !== '000') {
        // Any other than '000' code means transaction failure
        // (bad card, expiry, etc..)
    }
    else {
        $tranzilaconfirmation = $response_assoc['ConfirmationCode']; //ConfirmationCode
        $data['transaction_id'] = $tranzilaconfirmation;
        
    }
    return $tranzilaconfirmation;
}
 
?>