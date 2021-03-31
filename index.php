<?php
// Setup wrapper
require("classes/vinwiki.class.php");

// Spawn a new instance
$wrapper = new amattu\VINWiki();

// Fetch a vehicle feed
echo "<pre>";
print_r($wrapper->fetch_feed("4JGBB8GB4BA662410"));
echo "</pre>";
?>
