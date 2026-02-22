<?php
// Mock STDIO to avoid script execution if it checks for CLI input
// But the script checks for CLI SAPI and file name match.
// When included here, SCRIPT_FILENAME will be test_double_packet.php, so the main block won't run.

require_once 'apiaxis.php';

class TestApiAXIS extends ApiAXIS {
    public $calls = [];

    // Override methods to capture calls instead of executing real logic
    function Result_BuyPackage_v2($token, $pkgid) {
        $this->calls[] = "v2:$token:$pkgid";
    }

    function Result_BuyPackage_v3($token, $pkgid) {
        $this->calls[] = "v3:$token:$pkgid";
    }
}

echo "Testing DoublePacket...\n";

// We need to test the DoublePacket method.
// Initially, DoublePacket is a global function. After refactoring, it will be a method.
// This test is designed to verify the method AFTER refactoring.
// If run before refactoring, it will fail because the method doesn't exist on the class.

$test = new TestApiAXIS();

if (!method_exists($test, 'DoublePacket')) {
    echo "DoublePacket method not found on ApiAXIS class. (This is expected before refactoring)\n";
    exit(1);
}

$test->DoublePacket("TEST_TOKEN", "TEST_PKGID");

if (count($test->calls) !== 2) {
    echo "FAIL: Expected 2 calls, got " . count($test->calls) . "\n";
    print_r($test->calls);
    exit(1);
}

if ($test->calls[0] !== "v2:TEST_TOKEN:TEST_PKGID") {
    echo "FAIL: First call mismatch. Got: " . $test->calls[0] . "\n";
    exit(1);
}

if ($test->calls[1] !== "v3:TEST_TOKEN:TEST_PKGID") {
    echo "FAIL: Second call mismatch. Got: " . $test->calls[1] . "\n";
    exit(1);
}

echo "PASS: DoublePacket called v2 and v3 correctly.\n";
