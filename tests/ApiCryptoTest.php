<?php

require_once __DIR__ . '/../apiaxis.php';

class MockApiCrypto extends ApiCrypto
{
    private $mockResponse;

    public function setMockResponse($response)
    {
        $this->mockResponse = $response;
    }

    // Override Api_Encrypt to avoid network calls and return controlled data
    function Api_Encrypt($data)
    {
        return $this->mockResponse;
    }
}

function testEncrypt()
{
    echo "Running testEncrypt...\n";

    $mock = new MockApiCrypto();

    $testCases = [
        "SecretMessage123",
        "AnotherSecret",
        "123456",
        "" // Empty string
    ];

    $key = base64_decode("bHljb3h6");
    $iv = base64_decode("MDgwNDIwMDIxNjAxMjAwNA==");
    $keyData = openssl_decrypt("+COk/A==", "AES-128-CTR", $key, 0, $iv); // "data"
    $keyDecrypt3Des = openssl_decrypt("+Cez7/z/dz32IFL2", "AES-128-CTR", $key, 0, $iv); // "decrypt_3des"

    if ($keyData === false || $keyDecrypt3Des === false) {
        echo "FAIL: Failed to decrypt keys for test setup.\n";
        exit(1);
    }

    foreach ($testCases as $expectedValue) {
        // Construct the mock response
        // 1. Inner JSON: { "decrypt_3des": "VALUE" }
        $innerData = array(
            $keyDecrypt3Des => $expectedValue
        );

        // 2. Base64 encode that inner JSON
        $encodedInnerData = base64_encode(json_encode($innerData));

        // 3. Outer JSON: { "data": "BASE64..." }
        $outerData = array(
            $keyData => $encodedInnerData
        );

        $mockResponse = json_encode($outerData);
        $mock->setMockResponse($mockResponse);

        // Call the method under test
        $result = $mock->encrypt("irrelevant_input");

        if ($result === $expectedValue) {
            echo "PASS: encrypt() correctly decrypted '$expectedValue'.\n";
        } else {
            echo "FAIL: encrypt() returned unexpected value for '$expectedValue'. Got '$result'.\n";
            exit(1);
        }
    }
}

// Execute tests
testEncrypt();
