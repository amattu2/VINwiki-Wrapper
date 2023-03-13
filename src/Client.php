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

// Class Namespace
namespace amattu2\VINwiki;

use TypeError;
use Exception;

// Exception Classes
class InvalidHTTPResponseException extends Exception
{}
class InvalidVINwikiStatus extends Exception
{}
class InvalidVINwikiToken extends Exception
{}
class InvalidVINwikiPerson extends Exception
{}
class InvalidVINwikiUUID extends Exception
{}

/**
 * A VINwiki.com API wrapper
 */
class Client
{
  /**
   * VINwiki Authorization Token
   *
   * @var ?string
   */
  private  ?string $token = null;

  /**
   * VINwiki Person Object
   *
   * @var ?Models\Person
   */
  private  ?Models\Person$person = null;

  /**
   * VINwiki API base URL
   *
   * @var string
   */
  private const BASE_URL = "https://rest.vinwiki.com/";

  /**
   * VINwiki API endpoints
   *
   * @var array $endpoints [name => url]
   */
  private array $endpoints = [
    /* POST */
    "authenticate" => self::BASE_URL . "auth/authenticate",
    "plate_lookup" => self::BASE_URL . "vehicle/plate_lookup",
    "update_vehicle" => self::BASE_URL . "vehicle/vin/",
    "update_person" => self::BASE_URL . "person/id/",
    "vehicle_post" => self::BASE_URL . "vehicle/post/",
    "vehicle_search" => self::BASE_URL . "vehicle/search",
    "post_delete" => self::BASE_URL . "post/delete",

    /* GET */
    "vehicle" => self::BASE_URL . "vehicle/vin/",
    "feed" => self::BASE_URL . "vehicle/feed/",
    "me" => self::BASE_URL . "person/notification_count/me",
    "person_feed" => self::BASE_URL . "person/feed/",
    "person_profile" => self::BASE_URL . "person/profile/",
    "person_posts" => self::BASE_URL . "person/posts/",
    "recent_vins" => self::BASE_URL . "person/recent_vins",
  ];

  /**
   * Class Constructor
   *
   * @param string $username VINwiki Username or Email
   * @param string $password VINwiki Password
   */
  public function __construct(string $username, string $password)
  {
    $result = null;
    $post_result = Utils::Post($this->endpoints["authenticate"], [
      "login" => $username,
      "password" => $password,
    ]);

    if (!$post_result || !($result = json_decode($post_result, true))) {
      throw new InvalidHTTPResponseException("VINwiki returned an unknown response");
    }
    if ($result["status"] !== "ok") {
      throw new InvalidVINwikiStatus("VINwiki rejected the authorization for an unknown reason");
    }
    if (!isset($result["token"]) || empty($result["token"]) || empty($result["token"]["token"])) {
      throw new InvalidVINwikiToken("VINwiki provided an invalid authorization token");
    }
    if (!isset($result["person"]) || empty($result["person"])) {
      throw new InvalidVINwikiPerson("VINwiki returned an invalid person array");
    }

    $this->person = new Models\Person($result["person"]);
    $this->token = $result["token"]["token"];
  }

  /**
   * Get a VINwiki person profile by UUID
   *
   * If no UUID is provided, the user's profile will be returned
   *
   * @param string UUID of the person to fetch
   * @return ?Models\Person profile
   * @throws TypeError
   * @throws InvalidVINwikiPerson
   * @throws InvalidVINwikiUUID
   */
  public function getPersonProfile(string $uuid = "") : ?Models\Person
  {
    // Check Parameters
    if (empty($uuid)) {
      return $this->person;
    }

    // Fetch Profile
    $get_result = Utils::Get($this->endpoints["person_profile"] . $uuid, $this->token);
    $result = null;

    // Check HTTP Result
    if (!($result = json_decode($get_result, true))) {
      return null;
    }
    if ($result["status"] !== "ok" || !isset($result["profile"])) {
      return null;
    }

    // Return
    return new Models\Person($result["profile"]);
  }

  /**
   * Update a VINwiki person profile by UUID
   *
   * @param  string $uuid
   * @param  Models\Person $person
   * @return Models\Person|null the updated profile
   */
  public function updatePersonProfile(string $uuid, Models\Person $person): ?Models\Person
  {
    // Create Post
    $endpoint = $this->endpoints["update_person"] . $uuid;
    $post_result = Utils::Post($endpoint, $person->jsonSerialize(), $this->token);
    $result = null;

    // Check HTTP Result
    if (!($result = json_decode($post_result, true))) {
      return null;
    }
    if ($result["status"] !== "ok") {
      return null;
    }

    // Return
    return new Models\Person($result);
  }

  /**
   * Get the current user's notification feed
   *
   * @return ?array VINwiki notification result
   */
  public function getPersonNotifications() : ?array
  {
    // Fetch Notifications
    $get_result = Utils::Get($this->endpoints["me"], $this->token);
    $result = null;

    // Check HTTP Result
    if (!($result = json_decode($get_result, true))) {
      return null;
    }
    if ($result["status"] !== "ok") {
      return null;
    }
    if (!isset($result["notification_count"])) {
      return null;
    }

    // Return
    return $result["notification_count"];
  }

  /**
   * Get a VINwiki person post feed by UUID
   *
   * If no UUID is provided, the user's feed will be returned
   *
   * @param string $uuid the UUID of the person to fetch
   * @return ?Models\PersonFeed VINwiki feed
   * @throws InvalidVINwikiPerson
   * @throws InvalidVINwikiUUID
   */
  public function getPersonFeed(string $uuid = ""): ?Models\PersonFeed
  {
    // Check Parameters
    if (empty($uuid)) {
      if (!$this->person) {
        throw new InvalidVINwikiPerson("No VINwiki UUID provided and a default is not available");
      }
      if (!isset($this->person->uuid) || empty($this->person->uuid)) {
        throw new InvalidVINwikiUUID("No VINwiki UUID provided and a default is not available");
      }

      // Default
      $uuid = $this->person->uuid;
    }

    // Fetch Feed
    $get_result = Utils::Get($this->endpoints["person_feed"] . $uuid, $this->token);
    $result = null;

    // Check HTTP Result
    if (!($result = json_decode($get_result, true))) {
      return null;
    }
    if ($result["status"] !== "ok") {
      return null;
    }
    if (!isset($result["feed"])) {
      return null;
    }

    // Return
    return new Models\PersonFeed($result);
  }

  /**
   * Get a VINwiki person's posts by UUID
   *
   * @param  string $uuid UUID of the person to fetch
   * @return Models\PersonPosts|null
   * @throws TypeError
   */
  public function getPersonPosts(string $uuid = ""): ?Models\PersonPosts
  {
    // Check Parameters
    if (empty($uuid)) {
      if (!$this->person) {
        throw new InvalidVINwikiPerson("No VINwiki UUID provided and a default is not available");
      }
      if (!isset($this->person->uuid) || empty($this->person->uuid)) {
        throw new InvalidVINwikiUUID("No VINwiki UUID provided and a default is not available");
      }

      // Default
      $uuid = $this->person->uuid;
    }

    // Fetch Profile
    $get_result = Utils::Get($this->endpoints["person_posts"] . $uuid, $this->token);
    $result = null;

    // Check HTTP Result
    if (!($result = json_decode($get_result, true))) {
      return null;
    }
    if ($result["status"] !== "ok" || !isset($result["posts"])) {
      return null;
    }

    // Return
    return new Models\PersonPosts($result);
  }

  /**
   * Get a list of vehicles the user has recently interacted with
   *
   * @return ?Models\RecentVins
   */
  public function getRecentVins(): ?Models\RecentVins
  {
    // Query For Vehicles
    $get_result = Utils::Get($this->endpoints["recent_vins"], $this->token);
    $result = null;

    // Check HTTP Result
    if (!$get_result || !($result = json_decode($get_result, true))) {
      return null;
    }
    if ($result["status"] !== "ok" || !isset($result["recent_vins"])) {
      return null;
    }

    return new Models\RecentVins($result);
  }

  /**
   * Get a (or multiple) vehicles from a license plate
   *
   * @param string license plate
   * @param string country code
   * @param string state/region abbreviation
   * @return ?Models\PlateLookup
   * @throws TypeError
   */
  public function plateLookup(string $license_plate, string $country, string $state_abbr): ?Models\PlateLookup
  {
    // Check Parameters
    if (!$license_plate || strlen($license_plate) <= 0 || strlen($license_plate) > 30) {
      return null;
    }
    if (!$country || strlen($country) <= 0 || strlen($country) > 3) {
      return null;
    }
    if (!$state_abbr || strlen($state_abbr) <= 0 || strlen($state_abbr) > 3) {
      return null;
    }

    // Fetch License Plate
    $post_result = Utils::Post($this->endpoints["plate_lookup"], [
      "plate" => $license_plate,
      "country" => $country,
      "state" => $state_abbr,
    ], $this->token);
    $result = null;

    // Check HTTP Result
    if (!$post_result || !($result = json_decode($post_result, true))) {
      return null;
    }
    if ($result["status"] !== "ok" || !isset($result["plate_lookup"])) {
      return null;
    }

    // Return
    return new Models\PlateLookup($result["plate_lookup"]);
  }

  /**
   * Get a vehicle post feed by VIN
   *
   * @param string $vin
   * @return ?Models\VehicleFeed VINwiki feed result
   * @throws TypeError
   */
  public function getFeed(string $vin): ?Models\VehicleFeed
  {
    // Checks
    if (strlen($vin) != 17) {
      return null;
    }

    // Variables
    $post_result = Utils::Get($this->endpoints["feed"] . $vin);
    $result = null;

    if (!$post_result || !($result = json_decode($post_result, true))) {
      return null;
    }
    if ($result["status"] !== "ok") {
      return null;
    }

    // Return
    return new Models\VehicleFeed($result);
  }

  /**
   * Get a vehicle by VIN
   *
   * @param  string $vin
   * @return ?Models\Vehicle
   */
  public function getVehicle(string $vin): ?Models\Vehicle
  {
    // Check Parameters
    if (strlen($vin) != 17) {
      return null;
    }

    // Query For Vehicles
    $get_result = Utils::Get($this->endpoints["vehicle"] . $vin, $this->token);
    $result = null;

    // Check HTTP Result
    if (!$get_result || !($result = json_decode($get_result, true))) {
      return null;
    }
    if ($result["status"] !== "ok" || !isset($result["vehicle"])) {
      return null;
    }

    return new Models\Vehicle($result["vehicle"]);
  }

  /**
   * Search for vehicles by VIN / Year / Make / Model
   *
   * @param string $query the query to search a vehicle by
   * @return ?Models\VehicleSearch vehicle search result
   * @throws TypeError
   */
  public function vehicleSearch(string $query): ?Models\VehicleSearch
  {
    // Check Parameters
    if (!$query || strlen($query) < 3) {
      return null;
    }

    // Query For Vehicles
    $post_result = Utils::Post($this->endpoints["vehicle_search"], [
      "query" => $query,
    ], $this->token);
    $result = null;

    // Check HTTP Result
    if (!$post_result || !($result = json_decode($post_result, true))) {
      return null;
    }
    if ($result["status"] !== "ok" || !isset($result["results"])) {
      return null;
    }

    return new Models\VehicleSearch($result["results"]);
  }

  /**
   * Update a vehicle by VIN
   *
   * @param string $vin the vehicle to update
   * @param int $year the year of the vehicle
   * @param string $make the make of the vehicle
   * @param string $model the model of the vehicle
   * @param string $trim the trim of the vehicle
   * @return bool status of the update
   * @throws TypeError
   */
  public function updateVehicle(string $vin, int $year, string $make, string $model, string $trim = ""): bool
  {
    // Check Parameters
    if (strlen($vin) != 17) {
      return false;
    }

    // Update Vehicle
    $endpoint = $this->endpoints["update_vehicle"] . $vin;
    $post_result = Utils::Post($endpoint, [
      "year" => $year,
      "make" => $make,
      "model" => $model,
      "trim" => $trim,
    ], $this->token);
    $result = null;

    // Check HTTP Result
    if (!($result = json_decode($post_result, true))) {
      return false;
    }
    if ($result["status"] !== "ok") {
      return false;
    }

    // Return
    return true;
  }

  /**
   * Create a new VINwiki post
   *
   * @param string $vin the vehicle to post to
   * @param Models\VehiclePost $post the post to create
   * @return ?Models\FeedPost VINwiki post response or null on failure
   * @throws TypeError
   */
  public function createPost(string $vin, Models\VehiclePost $post): ?Models\FeedPost
  {
    // Create Post
    $endpoint = $this->endpoints["vehicle_post"] . $vin;
    $post_result = Utils::Post($endpoint, $post->jsonSerialize(), $this->token);
    $result = null;

    // Check HTTP Result
    if (!($result = json_decode($post_result, true))) {
      return null;
    }
    if ($result["status"] !== "ok" || !isset($result["post"])) {
      return null;
    }

    // Return
    return new Models\FeedPost($result["post"]);
  }

  /**
   * Delete a VINwiki post
   *
   * @param string $uuid
   * @return boolean status of the delete
   */
  public function deletePost(string $uuid): bool
  {
    // Delete Post
    $endpoint = $this->endpoints["delete_post"] . $uuid;
    $post_result = Utils::Post($endpoint, $this->token);
    $result = null;

    // Check HTTP Result
    if (!($result = json_decode($post_result, true))) {
      return false;
    }
    if ($result["status"] !== "ok" || $result["result"] !== "POST_DELETED") {
      return false;
    }

    // Return
    return true;
  }
}
