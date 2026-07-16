<?php
$c = file_get_contents('D:\Tanly\Program\Daily Report\checklist_full\resources\views\daily-reports\partials\form-209.blade.php');
$lines = explode("\n", $c);
$stack = 0;
$lineNum = 0;
foreach ($lines as $line) {
    $lineNum++;
    preg_match_all('/<div\b[^>]*>/', $line, $opens);
    preg_match_all('/<\/div\s*>/', $line, $closes);
    $o = count($opens[0]);
    $c2 = count($closes[0]);
    $prevStack = $stack;
    $stack += $o - $c2;
    // Check when stack exceeds 1
    if ($stack > $prevStack && $stack > 1) {
        echo "Stack went to $stack at line $lineNum: " . trim($line) . "\n";
    }
}
echo "Final stack: $stack\n";
