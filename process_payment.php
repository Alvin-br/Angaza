<?php
header("Content-Type: application/json");

// 1. DARAJA API CREDENTIALS
$consumerKey = 'v8VlytgDDMGXkMhb53myJ954AuVO8resDlukDZwk2UAi5A9A'; 
$consumerSecret = 'tMlNeBagRa4SNN57Z25GwDegiSbJgOajmp5gOv8FdJKTdAQFIOMPGTXfp2b94Eck';
$BusinessShortCode = '174379'; // Use 174379 for Sandbox testing
$Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
$PartyA = ''; // Will be the customer phone number
$AccountReference = 'AngazaOrg';
$TransactionDesc = 'Merchandise Payment';

// 2. GET DATA FROM FRONTEND
$data = json_decode(file_get_contents('php://input'), true);
$phone = $data['phone']; // Format: 2547XXXXXXXX
$amount = $data['amount'];

// 3. GENERATE ACCESS TOKEN
$headers = ['Content-Type:application/json; charset=utf8'];
$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_HEADER, FALSE);
curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
$result = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$result = json_decode($result);
$access_token = $result->access_token;

// 4. INITIATE STK PUSH
$timestamp = date('YmdHis');
$password = base64_encode($BusinessShortCode . $Passkey . $timestamp);

$stk_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$curl_post_data = [
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $BusinessShortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => 'https://yourdomain.com/callback.php', // Where Safaricom sends the receipt
    'AccountReference' => $AccountReference,
    'TransactionDesc' => $TransactionDesc
];

$data_string = json_encode($curl_post_data);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $stk_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

$curl_response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode(["error" => "cURL Error: " . $err]);
} else {
    echo $curl_response; 
}
?>