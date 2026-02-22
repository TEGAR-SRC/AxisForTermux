<?php

require_once __DIR__ . '/../apiaxis.php';

class MockApiAXIS_DoublePacket extends ApiAXIS
{
    public $calls = [];

    function Result_BuyPackage_v2($token, $pkgid)
    {
        $this->calls[] = [
            'method' => 'Result_BuyPackage_v2',
            'token' => $token,
            'pkgid' => $pkgid
        ];
    }

    function Result_BuyPackage_v3($token, $pkgid)
    {
        $this->calls[] = [
            'method' => 'Result_BuyPackage_v3',
            'token' => $token,
            'pkgid' => $pkgid
        ];
    }
}

function runDoublePacketTest()
{
    $mock = new MockApiAXIS_DoublePacket();
    $testToken = "test_token_123";
    $testPkgid = "test_pkgid_456";

    DoublePacket($testToken, $testPkgid, $mock);

    $calls = $mock->calls;

    if (count($calls) !== 2) {
        echo "FAIL: Expected 2 calls, got " . count($calls) . "\n";
        exit(1);
    }

    $call1 = $calls[0];
    if ($call1['method'] !== 'Result_BuyPackage_v2') {
        echo "FAIL: Expected first call to be Result_BuyPackage_v2, got " . $call1['method'] . "\n";
        exit(1);
    }
    if ($call1['token'] !== $testToken || $call1['pkgid'] !== $testPkgid) {
        echo "FAIL: Result_BuyPackage_v2 arguments mismatch\n";
        exit(1);
    }

    $call2 = $calls[1];
    if ($call2['method'] !== 'Result_BuyPackage_v3') {
        echo "FAIL: Expected second call to be Result_BuyPackage_v3, got " . $call2['method'] . "\n";
        exit(1);
    }
    if ($call2['token'] !== $testToken || $call2['pkgid'] !== $testPkgid) {
        echo "FAIL: Result_BuyPackage_v3 arguments mismatch\n";
        exit(1);
    }

    echo "PASS: DoublePacket test successful!\n";
}

runDoublePacketTest();
