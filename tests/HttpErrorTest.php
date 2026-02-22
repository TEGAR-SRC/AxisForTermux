<?php

require_once 'apiaxis.php';

class MockApiAXIS extends ApiAXIS
{
    private $mockHttpCode;
    private $mockResponse;

    public function setMockHttpCode($code) {
        $this->mockHttpCode = $code;
    }

    public function setMockResponse($response) {
        $this->mockResponse = $response;
    }

    protected function executeCurl($ch)
    {
        return $this->mockResponse;
    }

    protected function getHttpCode($ch)
    {
        return $this->mockHttpCode;
    }
}

// Helper to decrypt keys for verification
function getDecryptedKey($encrypted) {
    return openssl_decrypt($encrypted, "AES-128-CTR", base64_decode("bHljb3h6"), 0, base64_decode("MDgwNDIwMDIxNjAxMjAwNA=="));
}

function runTest() {
    $mock = new MockApiAXIS();

    // Test Case 1: 500 Internal Server Error
    echo "Running Test Case 1: 500 Internal Server Error\n";
    $mock->setMockHttpCode(500);
    $mock->setMockResponse("<html>Error 500</html>");

    $result = $mock->cHeader_POST("test_request");
    echo "Result: " . $result . "\n";

    $decoded = json_decode($result, true);
    if ($decoded === null) {
        echo "FAIL: Result is not valid JSON\n";
        exit(1);
    }

    $statusKey = getDecryptedKey("7zax6fD8");
    $messageKey = getDecryptedKey("8Sej7uToZg==");

    if (!isset($decoded[$statusKey])) {
        echo "FAIL: Status key '$statusKey' not found\n";
        exit(1);
    }

    if ($decoded[$statusKey] !== false) {
        echo "FAIL: Expected status false, got " . var_export($decoded[$statusKey], true) . "\n";
        exit(1);
    }

    if (!isset($decoded[$messageKey])) {
        echo "FAIL: Message key '$messageKey' not found\n";
        exit(1);
    }

    if (strpos($decoded[$messageKey], "HTTP Error: 500") === false) {
        echo "FAIL: Expected message to contain 'HTTP Error: 500', got '" . $decoded[$messageKey] . "'\n";
        exit(1);
    }

    echo "PASS: Test Case 1 Passed\n\n";

    // Test Case 2: 404 Not Found
    echo "Running Test Case 2: 404 Not Found\n";
    $mock->setMockHttpCode(404);
    $mock->setMockResponse("Not Found");

    $result = $mock->cHeader_POST("test_request");
    echo "Result: " . $result . "\n";

    $decoded = json_decode($result, true);

     if (strpos($decoded[$messageKey], "HTTP Error: 404") === false) {
        echo "FAIL: Expected message to contain 'HTTP Error: 404', got '" . $decoded[$messageKey] . "'\n";
        exit(1);
    }
    echo "PASS: Test Case 2 Passed\n\n";

    // Test Case 3: 200 OK (Success)
    echo "Running Test Case 3: 200 OK\n";
    $mock->setMockHttpCode(200);
    $mock->setMockResponse('{"success": true}');

    $result = $mock->cHeader_POST("test_request");
    echo "Result: " . $result . "\n";

    if ($result !== '{"success": true}') {
        echo "FAIL: Expected '{\"success\": true}', got '$result'\n";
        exit(1);
    }
    echo "PASS: Test Case 3 Passed\n";

}

runTest();
?>
