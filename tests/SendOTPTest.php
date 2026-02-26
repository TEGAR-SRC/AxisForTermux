<?php

require_once 'apiaxis.php';

// Helper to decrypt keys for verification (copied from ErrorHandlingTest.php)
function getDecryptedKey($encrypted) {
    return openssl_decrypt($encrypted, "AES-128-CTR", base64_decode("bHljb3h6"), 0, base64_decode("MDgwNDIwMDIxNjAxMjAwNA=="));
}

class MockApiCrypto extends ApiCrypto
{
    public $decryptionCalled = false;
    public $lastDecryptedData;

    function decrypt($data)
    {
        $this->decryptionCalled = true;
        $this->lastDecryptedData = $data;
        // Return a format string compatible with sprintf
        return "mock_query=%s";
    }
}

class MockApiAXIS extends ApiAXIS
{
    public $capturedRequest;

    function cHeader_POST($request)
    {
        $this->capturedRequest = $request;

        // Construct a success response
        $statusKey = getDecryptedKey("7zax6fD8");
        $messageKey = getDecryptedKey("8Sej7uToZg==");

        return json_encode([
            $statusKey => true,
            $messageKey => "OTP Sent Successfully"
        ]);
    }
}

function runTest() {
    echo "Starting SendOTP Test...\n";

    $mockCrypto = new MockApiCrypto();
    $mockAxis = new MockApiAXIS();
    $phoneNumber = "08123456789";

    // Call SendOTP with the mock crypto
    $result = $mockAxis->SendOTP($phoneNumber, $mockCrypto);

    // Verify decryption was called
    if (!$mockCrypto->decryptionCalled) {
        echo "FAIL: ApiCrypto::decrypt was not called.\n";
        exit(1);
    }

    // The expected encrypted string passed to decrypt in SendOTP
    $expectedEncryptedString = "i6e1zC-7idX87EGlntu3L9X_eMfg967OB7GheLpKC5c=";
    if ($mockCrypto->lastDecryptedData !== $expectedEncryptedString) {
         echo "FAIL: ApiCrypto::decrypt was called with unexpected data.\n";
         echo "Expected: $expectedEncryptedString\n";
         echo "Actual: " . $mockCrypto->lastDecryptedData . "\n";
         exit(1);
    }

    // Verify cHeader_POST was called with correct data
    // The query should be "mock_query=08123456789" (from sprintf("mock_query=%s", $phoneNumber))
    // And base64 encoded.
    $expectedQuery = "mock_query=" . $phoneNumber;
    $decodedRequest = base64_decode($mockAxis->capturedRequest);

    if ($decodedRequest !== $expectedQuery) {
        echo "FAIL: cHeader_POST received unexpected request.\n";
        echo "Expected (decoded): $expectedQuery\n";
        echo "Actual (decoded): $decodedRequest\n";
        exit(1);
    }

    // Verify the result returned by SendOTP
    $decodedResult = json_decode($result, true);
    $statusKey = getDecryptedKey("7zax6fD8");

    if ($decodedResult[$statusKey] !== true) {
         echo "FAIL: SendOTP did not return the success response from cHeader_POST.\n";
         exit(1);
    }

    echo "PASS: SendOTP Test passed successfully!\n";
}

runTest();
