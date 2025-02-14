<?php
function isValidPhoneNumber($phone_number, $customer_id, $api_key) {
    $api_url = "https://rest-api.telesign.com/v1/phoneid/$phone_number"; // not rest-ww

    $headers = [
        "Authorization: Basic " . base64_encode("$customer_id:$api_key"),
        "Content-Type: application/x-www-form-urlencoded"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); 

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        error_log("cURL Error: " . $curl_error);
        return false;
    }

    if ($http_code !== 200) {
        error_log("API Error: HTTP Code $http_code, Response: $response");
        return false;
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Decode Error: " . json_last_error_msg());
        return false;
    }

    if (!isset($data['phone_type'])) {
        error_log("Invalid API Response: " . print_r($data, true));
        return false;
    }

    $valid_types = ["FIXED_LINE", "MOBILE"];
    return in_array(strtoupper($data['phone_type']), $valid_types); //"VALID" type should not be included in $valid_types, as Telesign does not return "VALID" as a phone type.
}

?>