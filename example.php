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
$wrapper = new amattu2\VINwiki\Client($dotenv['username'], $dotenv['password']);

echo "<h1>Logged in person is</h1>", "<pre>";
print_r($wrapper->getPersonProfile());
echo "</pre>";

// echo "<h1>Update person</h1>", "<pre>";
// $currentPerson = $wrapper->getPersonProfile();
// $person = new amattu2\VINwiki\Models\Person([
//   "email" => "email@address.com",
//   "bio" => "This is a test bio from the VINWiki-Wrapper PHP library.",
// ]);
// print_r($wrapper->updatePersonProfile($currentPerson->uuid, $person));
// echo "</pre>";

// Fetch a user VINWiki notification feed
// echo "<h1>Notifications</h1>", "<pre>";
// print_r($wrapper->getPersonNotifications());
// echo "</pre>";


// Fetch a user VINWiki feed
// echo "<h1>User (Self) Feed</h1>", "<pre>";
// print_r($wrapper->getPersonFeed());
// echo "</pre>";


// Fetch a VINwiki person's posts
// echo "<h1>User Posts</h1>", "<pre>";
// print_r($wrapper->getPersonPosts());
// echo "</pre>";


// Fetch a list of recent VINs the user has posted on
// echo "<h1>User Recent VINs</h1>", "<pre>";
// print_r($wrapper->getRecentVins());
// echo "</pre>";


// Fetch vehicle decode by license plate (pl82vin)
// echo "<h1>Plate to Vehicle</h1>", "<pre>";
// print_r($wrapper->plateLookup("HELLO", "US", "CA"));
// echo "</pre>";


// Fetch a vehicle feed by VIN
// echo "<h1>Fetch Feed By VIN</h1>", "<pre>";
// print_r($wrapper->getFeed("WBAPL33579A406957"));
// echo "</pre>";


// Fetch a vehicle by VIN
// echo "<h1>Fetch Vehicle By VIN</h1>", "<pre>";
// print_r($wrapper->getVehicle("WBAPL33579A406957"));
// echo "</pre>";


// Perform vehicle search by query
// echo "<h1>Vehicle Search Query</h1>", "<pre>";
// print_r($wrapper->vehicleSearch("2011 Toyota Corolla"));
// echo "</pre>";


// Update Vehicle By VIN
// echo "<h1>Update Vehicle</h1>", "<pre>";
// print_r($wrapper->updateVehicle("WBAPL33579A406957", 2009, "BMW", "335i", "xDrive"));
// echo "</pre>";


// Create a post on a VIN
// echo "<h1>Create a feed post</h1>", "<pre>";
// $post = new amattu2\VINwiki\Models\VehiclePost([
//   "mileage" => 43000,
//   "text" => "This is a test post from the VINWiki-Wrapper PHP library.",
// ]);
// print_r($wrapper->createPost("WBAPL33579A406957", $post));
// echo "</pre>";


// Delete a post by UUID
// print_r($wrapper->deletePost("f3b5b2b0-1b5a-11e9-9c4b-0b3b2b0b3b2b"));
