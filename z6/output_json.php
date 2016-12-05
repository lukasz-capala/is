<?php
header('Content-type: text/json');
header('Content-Disposition: attachment; filename="summary.json"');

echo file_get_contents("results.json");
unlink("results.json");
?>