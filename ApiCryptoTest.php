<?php

require_once 'apiaxis.php';
require_once 'TestRunner.php';

class ApiCryptoMock extends ApiCrypto
{
    public $mockResponse = '';
    public $lastCurlHandle = null;

    protected function executeCurl($ch)
    {
        $this->lastCurlHandle = $ch;
        return $this->mockResponse;
    }
}

class ApiCryptoTest extends TestRunner
{
    public function testEncrypt()
    {
        $crypto = new ApiCryptoMock();

        // Mock response for Api_Encrypt
        // encrypt() expects:
        // 1. Api_Encrypt returns JSON: {"data": "base64(json)"}
        // 2. The inner JSON has "decrypt_3des" key
        $innerJson = json_encode(['decrypt_3des' => 'encrypted_value_123']);
        $mockResponse = json_encode(['data' => base64_encode($innerJson)]);

        $crypto->mockResponse = $mockResponse;

        $result = $crypto->encrypt('plain_text');

        $this->assertEquals('encrypted_value_123', $result, 'encrypt() should return the value from decrypt_3des field');
    }

    public function testDecrypt()
    {
        $crypto = new ApiCryptoMock();

        // Mock response for Api_Decrypt
        // decrypt() expects:
        // 1. Api_Decrypt returns JSON: {"data": "base64(json)"}
        // 2. The inner JSON has "encrypt_3des" key
        $innerJson = json_encode(['encrypt_3des' => 'decrypted_value_123']);
        $mockResponse = json_encode(['data' => base64_encode($innerJson)]);

        $crypto->mockResponse = $mockResponse;

        $result = $crypto->decrypt('encrypted_text');

        $this->assertEquals('decrypted_value_123', $result, 'decrypt() should return the value from encrypt_3des field');
    }
}

$test = new ApiCryptoTest();
$test->runTests();
