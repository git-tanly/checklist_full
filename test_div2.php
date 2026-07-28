<?php
$c = file_get_contents('D:\Tanly\Program\Daily Report\checklist_full\resources\views\daily-reports\partials\form-209.blade.php');
$lines = explode("\n", $c);
$stack = 0;
$lineNum = 0;
$lastUnclosedLine = '';
foreach ($lines as $line) {
    $lineNum++;
    preg_match_all('/<div\b[^>]*>/', $line, $opens);
    preg_match_all('/<\/div\s*>/', $line, $closes);
    $o = count($opens[0]);
    $c2 = count($closes[0]);
    if ($o > $c2) {
        $lastUnclosedLine = "Line $lineNum: " . trim($line);
    }
    $stack += $o - $c2;
}
echo "Final stack: $stack (should be 0)\n";
echo "Last unclosed div line: $lastUnclosedLine\n";
