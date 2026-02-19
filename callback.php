<?php
// 1. Capture the raw JSON data sent by Safaricom
$callbackResponse = file_get_contents('php://input');

// 2. Log the raw response for debugging (optional but recommended)
$logFile = "mpesa_responses.json";
$log = fopen($logFile, "a");
fwrite($log, $callbackResponse . PHP_EOL);
fclose($log);

// 3. Decode the JSON to extract specific details
$data = json_decode($callbackResponse);

$resultCode = $data->Body->stkCallback->ResultCode;
$resultDesc = $data->Body->stkCallback->ResultDesc;
$merchantRequestID = $data->Body->stkCallback->MerchantRequestID;

if ($resultCode == 0) {
    // Payment was successful! Extract details
    $callbackMetadata = $data->Body->stkCallback->CallbackMetadata->Item;
    
    $amount = "";
    $mpesaReceiptNumber = "";
    $phoneNumber = "";
    $transactionDate = "";

    foreach ($callbackMetadata as $item) {
        if ($item->Name == "Amount") $amount = $item->Value;
        if ($item->Name == "MpesaReceiptNumber") $mpesaReceiptNumber = $item->Value;
        if ($item->Name == "PhoneNumber") $phoneNumber = $item->Value;
        if ($item->Name == "TransactionDate") $transactionDate = $item->Value;
    }

    // 4. Save to an Excel-friendly CSV file
    $file = 'payments_ledger.csv';
    
    // Add headers if the file is new
    if (!file_exists($file)) {
        file_put_contents($file, "Date,Receipt,Phone,Amount,Status" . PHP_EOL);
    }

    $line = "$transactionDate,$mpesaReceiptNumber,$phoneNumber,$amount,SUCCESS" . PHP_EOL;
    file_put_contents($file, $line, FILE_APPEND);
}
?>