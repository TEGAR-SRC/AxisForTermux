<?php

class TestRunner
{
    protected $passed = 0;
    protected $failed = 0;

    public function assertEquals($expected, $actual, $message = '')
    {
        if ($expected === $actual) {
            $this->passed++;
            echo "✅ PASS: $message\n";
        } else {
            $this->failed++;
            echo "❌ FAIL: $message\n";
            echo "   Expected: " . var_export($expected, true) . "\n";
            echo "   Actual:   " . var_export($actual, true) . "\n";
        }
    }

    public function assertTrue($condition, $message = '')
    {
        $this->assertEquals(true, $condition, $message);
    }

    public function runTests()
    {
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (strpos($method, 'test') === 0) {
                echo "\nRunning $method...\n";
                try {
                    $this->$method();
                } catch (Exception $e) {
                    $this->failed++;
                    echo "❌ FAIL: $method threw exception: " . $e->getMessage() . "\n";
                }
            }
        }

        echo "\nSummary:\n";
        echo "Passed: {$this->passed}\n";
        echo "Failed: {$this->failed}\n";

        if ($this->failed > 0) {
            exit(1);
        }
    }
}
