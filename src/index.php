<?php
/*
 * reference to base directory
 */
$baseDir = dirname(__DIR__, 1);
require($baseDir . '/src/views/header.php');

$target5days = './views/daily.php';
$daysFcast = <<<HEREDOC
<a href="$target5days">get a 7 day forecast</a>
HEREDOC;
$targetRaw = './views/raw.php';
$raw = <<<HEREDOC
<a href="$targetRaw">show raw json</a>
HEREDOC;
echo '<br />';
echo $raw;
echo '<br />';
echo $daysFcast;
echo '<br />';

require($baseDir . '/src/views/footer.php');


