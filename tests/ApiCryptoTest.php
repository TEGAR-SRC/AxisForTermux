<?php

require_once 'apiaxis.php';

class MockApiCrypto extends ApiCrypto
{
    public $mockResponse = '{"data": "success"}';
    public $mockHttpCode = 200;
    public $mockCurlError = '';
    public $mockExecReturn = true;

    protected function executeCurl($ch)
    {
        if ($this->mockExecReturn === false) {
            return false;
        }
        return $this->mockResponse;
    }

    // New method to be implemented in ApiCrypto for testability
    protected function getInfo($ch)
    {
        return $this->mockHttpCode;
    }
}

class ApiCryptoTest
{
    private $statusKey;
    private $messageKey;

    public function __construct() {
        $this->statusKey = openssl_decrypt("7zax6fD8","AES-128-CTR",base64_decode("bHljb3h6"),0,base64_decode("MDgwNDIwMDIxNjAxMjAwNA=="));
        $this->messageKey = openssl_decrypt("8Sej7uToZg==","AES-128-CTR",base64_decode("bHljb3h6"),0,base64_decode("MDgwNDIwMDIxNjAxMjAwNA=="));
    }

    public function testSuccess()
    {
        $mock = new MockApiCrypto();
        $mock->mockHttpCode = 200;
        $mock->mockResponse = '{"data": "success"}';

        $result = $mock->cHeader_POST("test_request");

        if ($result !== '{"data": "success"}') {
            echo "FAIL: Expected success response, got '$result'\n";
            return false;
        }
        echo "PASS: Success test\n";
        return true;
    }

    public function testHttpError()
    {
        $mock = new MockApiCrypto();
        $mock->mockHttpCode = 500;
        $mock->mockResponse = '<html>Error</html>';

        $result = $mock->cHeader_POST("test_request");

        $decoded = json_decode($result, true);
        if ($decoded === null) {
            echo "FAIL: Result is not valid JSON for HTTP 500. Got: $result\n";
            return false;
        }

        if (!isset($decoded[$this->statusKey])) {
             echo "FAIL: Status key not found in response\n";
             return false;
        }

        if ($decoded[$this->statusKey] !== false) {
             echo "FAIL: Expected status false, got " . var_export($decoded[$this->statusKey], true) . "\n";
             return false;
        }

        if (!isset($decoded[$this->messageKey])) {
            echo "FAIL: Message key not found in response\n";
            return false;
        }

        if (strpos($decoded[$this->messageKey], "HTTP Error") === false) {
             echo "FAIL: Expected 'HTTP Error' in message, got: " . $decoded[$this->messageKey] . "\n";
             return false;
        }

        echo "PASS: HTTP Error test\n";
        return true;
    }

    public function testCurlError()
    {
        $mock = new MockApiCrypto();
        $mock->mockExecReturn = false;
        $mock->mockCurlError = "Timeout";

        $result = $mock->cHeader_POST("test_request");

        $decoded = json_decode($result, true);
        if ($decoded === null) {
            echo "FAIL: Result is not valid JSON for Curl Error\n";
            return false;
        }

        if ($decoded[$this->statusKey] !== false) {
             echo "FAIL: Expected status false for curl error\n";
             return false;
        }

        echo "PASS: Curl Error test\n";
        return true;
    }

    public function run() {
        $success = $this->testSuccess();
        $http = $this->testHttpError();
        $curl = $this->testCurlError();

        if (!$success || !$http || !$curl) {
            exit(1);
        }
    }
}

$test = new ApiCryptoTest();
$test->run();
