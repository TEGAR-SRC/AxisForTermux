<?php
// verify_fix.php

// Include the modified file
require_once 'apiaxis.php';

echo "Verifying ApiCrypto::decrypt_base...\n";

// Test cases
$tests = [
    "9Dak7fa1LE2kNF62YztSo2AZzhNMqhm5qtMpR0/nrL0mYV6b4NK93Yt/DMGyd+T96Lo=" => "https://api3des.herokuapp.com/index.php?content=%s",
    "7zax6fD8" => "status",
    "8Sej7uToZg==" => "message",
    "+Syz7/z/dz32IFL2" => "encrypt_3des",
    "+COk/A==" => "data",
    "3Tq57sPgcTagNlrwf35j9CgwxT9IhwI=" => "AxisForTermux By LyCoXz"
];

$failed = false;

foreach ($tests as $cipher => $expected) {
    $result = ApiCrypto::decrypt_base($cipher);
    if ($result !== $expected) {
        echo "[FAIL] Decryption failed for '$cipher'.\n";
        echo "       Expected: '$expected'\n";
        echo "       Got:      '$result'\n";
        $failed = true;
    } else {
        echo "[PASS] Decrypted successfully: '$expected'\n";
    }
}

// Verify no hardcoded IV/Key in openssl_decrypt calls
$content = file_get_contents('apiaxis.php');
if (strpos($content, 'openssl_decrypt("9Dak7fa1LE') !== false) {
    echo "[FAIL] Found old openssl_decrypt call with hardcoded string.\n";
    $failed = true;
}

if (substr_count($content, 'openssl_decrypt') !== 1) {
    echo "[FAIL] Expected exactly 1 call to openssl_decrypt, found " . substr_count($content, 'openssl_decrypt') . ".\n";
    $failed = true;
}

if ($failed) {
    echo "\nVerification FAILED.\n";
    exit(1);
} else {
    echo "\nVerification PASSED.\n";
    exit(0);
}
