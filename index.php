<?php
// Setup wrapper
require("classes/vinwiki.class.php");

// Spawn a new instance
$wrapper = new amattu\VINWiki();

// Setup a Vinwiki session
// Only required for create/update functions
$wrapper->setup_session("USERNAME", "PASSWORD");

/*
// Fetch a vehicle feed
echo "<h1>Fetch Feed By VIN</h1>", "<pre>";
print_r($wrapper->fetch_feed("4JGBB8GB4BA662410"));
echo "</pre>";
*/

/*
// Fetch vehicle decode by license plate (pl82vin)
echo "<h1>Plate 2 VIN Decode</h1>", "<pre>";
print_r($wrapper->pl82vin("HTML", "MD"));
echo "</pre>";
*/

/*
// Update Vehicle By VIN
echo "<h1>Update Vehicle</h1>", "<pre>";
print_r($wrapper->update_vehicle("WBAWB735X9P159047", 2009, "BMW", "335i", "M Sport"));
echo "</pre>";
*/

/*
// Create a post on a VIN
echo "<h1>Create a feed post</h1>", "<pre>";
print_r($wrapper->create_post("WBAWB735X9P159047", Array("text" => "added to vinwiki via API")));
echo "</pre>";
*/
?>
