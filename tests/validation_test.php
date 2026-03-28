<?php

function check_validation($input) {
    // Current logic in the code
    $nomor = str_replace(['-', '+',' '],['', '', ''], trim($input));

    // Proposed validation logic
    if (!preg_match('/^[0-9]+$/', $nomor)) {
        return false; // Invalid
    }
    return true; // Valid
}

// Test cases
$inputs = [
    "081234567890" => true,
    "+62 812-3456-7890" => true,
    "invalid123" => false,
    "123abc456" => false,
    "" => false, // empty string should fail regex /^[0-9]+$/
    " " => false, // trim makes it empty
    " - " => false, // becomes empty
];

$failed = 0;
foreach ($inputs as $input => $expected) {
    $result = check_validation($input);
    if ($result !== $expected) {
        echo "Test failed for input '$input'. Expected " . ($expected ? 'valid' : 'invalid') . ", got " . ($result ? 'valid' : 'invalid') . "\n";
        $failed++;
    } else {
         echo "Test passed for input '$input'.\n";
    }
}

if ($failed > 0) {
    exit(1);
}
echo "All validation tests passed.\n";
?>
