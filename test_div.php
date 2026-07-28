<?php
$files = [
    'form-209.blade.php',
    'form-xfh.blade.php',
    'form-chamas.blade.php',
    'form-nagano.blade.php',
    'form-voda.blade.php',
    'form-jm.blade.php',
    'form-bqt.blade.php',
    'form-ird.blade.php',
];
$dir = 'D:\Tanly\Program\Daily Report\checklist_full\resources\views\daily-reports\partials\\';
foreach ($files as $f) {
    $c = file_get_contents($dir . $f);
    $open = substr_count($c, '<div');
    $close = substr_count($c, '</div>');
    $diff = $open - $close;
    echo "$f: <div=$open </div>=$close diff=$diff";
    if ($diff !== 0) echo ' ** UNBALANCED **';
    echo "\n";
}
