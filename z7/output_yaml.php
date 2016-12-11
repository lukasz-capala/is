<?php
header('Content-type: text/yaml');
header('Content-Disposition: attachment; filename="summary.yaml"');

echo file_get_contents("results.yaml");
unlink("results.yaml");
?>