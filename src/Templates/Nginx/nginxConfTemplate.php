<?php foreach($config as $key => $value) {
if (is_string($value) || is_int($value)) {
echo "{$key} {$value};\n";
}
}
?>

events {
<?php foreach($config['events'] as $key => $value) {
$value = is_array($value) ? implode(' ',$value) : $value;
echo "    {$key} {$value};\n";
} ?>
}

http {
<?php foreach($config['http'] as $key => $value) {
$value = is_array($value) ? implode(' ', $value) : $value;
echo "    {$key} {$value};\n";
} ?>
<?php foreach($config['includes'] as $key => $value) {
echo "    include {$value};\n";
} ?>
}
