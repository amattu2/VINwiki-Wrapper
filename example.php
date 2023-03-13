<?php
/*
 * Produced: Thu Mar 09 2023
 * Author: Alec M.
 * GitHub: https://amattu.com/links/github
 * Copyright: (C) 2023 Alec M.
 * License: License GNU Affero General Public License v3.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Setup wrapper
require "vendor/autoload.php";

// Instantiate a new wrapper
$dotenv = parse_ini_file(".env");
$wrapper = new amattu2\VINwiki($dotenv['username'], $dotenv['password']);

echo "<h1>Logged in person is</h1>", "<pre>";
print_r($wrapper->getPerson());
echo "</pre>";

// Fetch a vehicle by VIN
// echo "<h1>Fetch Vehicle By VIN</h1>", "<pre>";
// print_r($wrapper->fetch_vehicle("WBAPL33579A406957"));
// echo "</pre>";


// Fetch a vehicle feed by VIN
// echo "<h1>Fetch Feed By VIN</h1>", "<pre>";
// print_r($wrapper->fetch_feed("WBAPL33579A406957"));
// echo "</pre>";


// Fetch vehicle decode by license plate (pl82vin)
// echo "<h1>Plate 2 VIN Decode</h1>", "<pre>";
// print_r($wrapper->plate_lookup("ABC123", "US", "MD"));
// echo "</pre>";


// Update Vehicle By VIN
// echo "<h1>Update Vehicle</h1>", "<pre>";
// print_r($wrapper->update_vehicle("WBAPL33579A406957", 2009, "BMW", "335i", "xDrive"));
// echo "</pre>";


// Fetch a user VINWiki notification feed
// echo "<h1>Notifications</h1>", "<pre>";
// print_r($wrapper->fetch_person_notifications());
// echo "</pre>";


// Perform vehicle search by query
// echo "<h1>Vehicle Search Query</h1>", "<pre>";
// print_r($wrapper->vehicle_search("2009 BMW 335i"));
// echo "</pre>";


// Fetch a user VINWiki feed
// echo "<h1>User (Self) Feed</h1>", "<pre>";
// print_r($wrapper->fetch_person_feed());
// echo "</pre>";


// Fetch a VINWiki user profile
// echo "<h1>User Profile</h1>", "<pre>";
// print_r($wrapper->fetch_person_profile());
// echo "</pre>";


// Fetch a VINwiki person's posts
// echo "<h1>User Posts</h1>", "<pre>";
// print_r($wrapper->fetch_person_posts(""));
// echo "</pre>";


// Create a post on a VIN
// echo "<h1>Create a feed post</h1>", "<pre>";
// $post = new amattu2\VINwiki\Models\VehiclePost([
//   "mileage" => 43000,
//   "text" => "This is a test post from the VINWiki-Wrapper PHP library.",
// ]);
// print_r($wrapper->create_post("WBAPL33579A406957", $post));
// echo "</pre>";
