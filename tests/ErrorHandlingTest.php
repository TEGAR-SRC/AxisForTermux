<?php

require_once 'apiaxis.php';

class MockApiAXIS extends ApiAXIS
{
    protected function executeCurl($ch)
    {
        return false;
    }
}

// Helper to decrypt keys for verification
function getDecryptedKey($encrypted) {
    return openssl_decrypt($encrypted, "AES-128-CTR", base64_decode("bHljb3h6"), 0, base64_decode("MDgwNDIwMDIxNjAxMjAwNA=="));
}

function runTest() {
    $mock = new MockApiAXIS();
    $result = $mock->cHeader_POST("test_request");

    echo "Result from cHeader_POST: " . $result . "\n";

    $decoded = json_decode($result, true);
    if ($decoded === null) {
        echo "FAIL: Result is not valid JSON\n";
        exit(1);
    }

    $statusKey = getDecryptedKey("7zax6fD8");
    $messageKey = getDecryptedKey("8Sej7uToZg==");

    if (!isset($decoded[$statusKey])) {
        echo "FAIL: Status key '$statusKey' not found in response\n";
        exit(1);
    }

    if ($decoded[$statusKey] !== false) {
        echo "FAIL: Expected status to be false, got " . var_export($decoded[$statusKey], true) . "\n";
        exit(1);
    }

    if (!isset($decoded[$messageKey])) {
        echo "FAIL: Message key '$messageKey' not found in response\n";
        exit(1);
    }

    if (strpos($decoded[$messageKey], "CURL Error:") === false) {
        echo "FAIL: Expected message to contain 'CURL Error:', got '" . $decoded[$messageKey] . "'\n";
        exit(1);
    }

    echo "PASS: Error handling test successful!\n";
}

runTest();
