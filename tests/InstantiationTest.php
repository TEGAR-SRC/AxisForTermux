<?php
require_once 'apiaxis.php';

class InstantiationTest
{
    public function testInstantiation()
    {
        $axis = new ApiAXIS();
        $reflection = new ReflectionClass($axis);
        $property = $reflection->getProperty('crypto');
        $property->setAccessible(true);
        $crypto = $property->getValue($axis);

        if ($crypto instanceof ApiCrypto) {
            echo "PASS: ApiCrypto instantiated in ApiAXIS constructor.\n";
        } else {
            echo "FAIL: ApiCrypto not instantiated correctly.\n";
            exit(1);
        }
    }
}

$test = new InstantiationTest();
$test->testInstantiation();
