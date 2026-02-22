<?php

require_once __DIR__ . '/../apiaxis.php';

class MockApiCryptoForLogin extends ApiCrypto
{
    public function decrypt($data)
    {
        // Return a format string suitable for sprintf with 2 args (msisdn, otp)
        // Original code expects: sprintf(decrypt(...), msisdn, otp)
        return "login=%s&otp=%s";
    }
}

class MockApiAXISForLogin extends ApiAXIS
{
    public $lastPostData = null;

    // Override cHeader_POST to capture the request payload
    function cHeader_POST($request)
    {
        $this->lastPostData = $request;
        // Return a dummy JSON response as LoginOTP expects to return this result
        return json_encode(["status" => true, "message" => "Mock Success"]);
    }
}

function runLoginOTPTest() {
    echo "Running LoginOTP Test...\n";

    $mockAxis = new MockApiAXISForLogin();
    $mockCrypto = new MockApiCryptoForLogin();

    $msisdn = "08123456789";
    $otp = "123456";

    // Call LoginOTP with the mock crypto
    $result = $mockAxis->LoginOTP($msisdn, $otp, $mockCrypto);

    // Verify the query construction
    // The query should be "login=08123456789&otp=123456" (from our mock crypto decrypt)
    // And then base64 encoded.

    $expectedQuery = sprintf("login=%s&otp=%s", $msisdn, $otp);
    $expectedEncoded = base64_encode($expectedQuery);

    if ($mockAxis->lastPostData === $expectedEncoded) {
        echo "PASS: Payload construction correct.\n";
    } else {
        echo "FAIL: Payload mismatch.\n";
        echo "Expected: " . $expectedEncoded . "\n";
        echo "Actual:   " . $mockAxis->lastPostData . "\n";
        exit(1);
    }

    echo "LoginOTP Test Completed Successfully.\n";
}

runLoginOTPTest();
?>
