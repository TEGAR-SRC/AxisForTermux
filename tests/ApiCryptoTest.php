<?php

require_once 'apiaxis.php';

class MockApiCrypto extends ApiCrypto
{
    private $mockResponse;

    public function setMockResponse($response)
    {
        $this->mockResponse = $response;
    }

    protected function executeCurl($ch)
    {
        return $this->mockResponse;
    }
}

function runTest() {
    $mock = new MockApiCrypto();

    // Test Case 1: Successful Decryption
    echo "Running Test Case 1: Successful Decryption...\n";

    $expectedResult = "decrypted_secret_message";

    // Construct the inner JSON structure
    // The key "encrypt_3des" corresponds to the decrypted value of "+Syz7/z/dz32IFL2" used in ApiCrypto::decrypt
    $innerJson = json_encode([
        "encrypt_3des" => $expectedResult
    ]);

    // Construct the outer JSON structure
    // The key "data" corresponds to the decrypted value of "+COk/A==" used in ApiCrypto::decrypt
    $outerJson = json_encode([
        "data" => base64_encode($innerJson)
    ]);

    $mock->setMockResponse($outerJson);

    // Call decrypt with some dummy data (the input to decrypt is passed to Api_Decrypt but since we mock executeCurl, it doesn't matter what it is)
    $result = $mock->decrypt("dummy_input");

    if ($result === $expectedResult) {
        echo "PASS: Decrypted value matches expected result.\n";
    } else {
        echo "FAIL: Expected '$expectedResult', got '$result'.\n";
        exit(1);
    }

    // Test Case 2: Invalid JSON Structure (e.g. missing keys)
    // This is to verify that the code handles missing keys or at least doesn't crash (though current implementation might throw warnings)
    echo "\nRunning Test Case 2: Missing Keys...\n";

    $invalidJson = json_encode([
        "wrong_key" => "some_value"
    ]);

    $mock->setMockResponse($invalidJson);

    // We expect PHP warnings here because the code accesses array offsets without checking existence.
    // We can suppress warnings or just let them show up. For this test, we just want to ensure it doesn't fatal error.

    // Capture output to suppress warnings in test output if desired, or just run it.
    // Since we are in a simple script, we'll just run it and catch exceptions if any (though array access on null/missing index is a warning in PHP < 8, and Warning in PHP 8).

    try {
        // Suppress warnings for this test case as we expect them
        $old_error_reporting = error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
        $result = $mock->decrypt("dummy_input");
        error_reporting($old_error_reporting);

        echo "PASS: Code executed without fatal error on missing keys (result: " . var_export($result, true) . ")\n";
    } catch (Throwable $e) {
        echo "FAIL: Code threw exception on missing keys: " . $e->getMessage() . "\n";
        // Not exiting here as we might want to continue testing other scenarios if we had more
    }

    echo "\nAll tests completed.\n";
}

runTest();
