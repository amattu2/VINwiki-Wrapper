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
namespace amattu2;

use TypeError;

// Exception Classes
class UnknownHTTPException extends \Exception
{}
class InvalidHTTPResponseException extends \Exception
{}
class InvalidVINWikiStatus extends \Exception
{}
class InvalidVINWikiToken extends \Exception
{}
class InvalidVINWikiSession extends \Exception
{}
class InvalidVINWikiPerson extends \Exception
{}
class InvalidVINwikiUUID extends \Exception
{}

/**
 * A VINwiki.com API wrapper
 */
class VINwiki
{
  /**
   * VINwiki Authorization Token
   *
   * @var ?string
   */
  private ?string $token = null;

  /**
   * VINwiki Person Object
   *
   * @var ?VINwiki\Models\Person
   */
  private ?VINwiki\Models\Person $person = null;

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
    "authenticate" =>   self::BASE_URL . "auth/authenticate",
    "plate_lookup" =>   self::BASE_URL . "vehicle/plate_lookup",
    "update_vehicle" => self::BASE_URL . "vehicle/vin/",
    "vehicle_post" =>   self::BASE_URL . "vehicle/post/",
    "vehicle_search" => self::BASE_URL . "vehicle/search",
    "post_delete" =>    self::BASE_URL . "post/delete/", // TODO

    /* GET */
    "vehicle" =>        self::BASE_URL . "vehicle/vin/",
    "feed" =>           self::BASE_URL . "vehicle/feed/",
    "me" =>             self::BASE_URL . "person/notification_count/me",
    "person_feed" =>    self::BASE_URL . "person/feed/",
    "person_profile" => self::BASE_URL . "person/profile/",
    "person_posts" =>   self::BASE_URL . "person/posts/",
    "recent_vins" =>    self::BASE_URL . "person/recent_vins", // TODO
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
      throw new InvalidHTTPResponseException("VINWiki returned an unknown response");
    }
    if ($result["status"] !== "ok") {
      throw new InvalidVINWikiStatus("VINWiki rejected the authorization for an unknown reason");
    }
    if (!isset($result["token"]) || empty($result["token"]) || empty($result["token"]["token"])) {
      throw new InvalidVINWikiToken("VINWiki provided an invalid authorization token");
    }
    if (!isset($result["person"]) || empty($result["person"])) {
      throw new InvalidVINWikiPerson("VINWiki returned an invalid person array");
    }

    $this->person = new VINwiki\Models\Person($result["person"]);
    $this->token = $result["token"]["token"];
  }

  /**
   * Get the current VINwiki person object
   *
   * @return ?VINwiki\Models\Person VINwiki person object
   */
  public function getPerson() : ?VINwiki\Models\Person
  {
    return $this->person;
  }

  /**
   * Fetch a VIN from a license plate
   *
   * @param string license plate
   * @param string country code
   * @param string state/region abbreviation
   * @return ?VINwiki\Models\PlateLookup
   * @throws TypeError
   * @throws InvalidVINWikiSession
   */
  public function plate_lookup(string $license_plate, string $country, string $state_abbr): ?VINwiki\Models\PlateLookup
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
    return new VINwiki\Models\PlateLookup($result["plate_lookup"]);
  }

  /**
   * Update a vehicle by VIN
   *
   * @param string $vin the vehicle to update
   * @param int $year the year of the vehicle
   * @param string $make the make of the vehicle
   * @param string $model the model of the vehicle
   * @param string $trim the trim of the vehicle
   * @return bool status
   * @throws TypeError
   */
  public function update_vehicle(string $vin, int $year, string $make, string $model, string $trim = ""): bool
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
   * Create a new VINWiki post
   *
   * @param string $vin the vehicle to post to
   * @param VINwiki\Models\VehiclePost $post the post to create
   * @return ?VINwiki\Models\FeedPost VINWiki post response
   * @throws TypeError
   * @throws InvalidVINWikiSession
   */
  public function create_post(string $vin, VINwiki\Models\VehiclePost $post): ?VINwiki\Models\FeedPost
  {
    // Create Post
    $endpoint = $this->endpoints["vehicle_post"] . $vin;
    $post_result = Utils::Post($endpoint, $post->jsonSerialize(), $this->token);
    $result = null;

    // Check HTTP Result
    if (!($result = json_decode($post_result, true))) {
      return null;
    }
    if ($result["status"] !== "ok") {
      return null;
    }
    if (!isset($result["post"])) {
      return null;
    }

    // Return
    return new VINwiki\Models\FeedPost($result["post"]);
  }

  /**
   * Fetch Vehicle Feed By VIN
   *
   * @param string $vin
   * @return ?VINwiki\Models\VehicleFeed VINWiki feed result
   * @throws TypeError
   */
  public function fetch_feed(string $vin): ?VINwiki\Models\VehicleFeed
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
    return new VINwiki\Models\VehicleFeed($result);
  }

  /**
   * Fetch a user notification feed
   *
   * @return ?array VINWiki notification result
   * @throws InvalidVINWikiSession
   */
  public function fetch_person_notifications(): ?array
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
   * Fetch a VINWiki person feed
   *
   * @param string $uuid the UUID of the person to fetch
   * @return ?VINwiki\Models\PersonFeed VINWiki feed
   * @throws InvalidVINWikiSession
   * @throws InvalidVINWikiPerson
   * @throws InvalidVINwikiUUID
   */
  public function fetch_person_feed(string $uuid = ""): ?VINwiki\Models\PersonFeed
  {
    // Check Parameters
    if (empty($uuid)) {
      if (!$this->person) {
        throw new InvalidVINWikiPerson("No VINWiki UUID provided and a default is not available");
      }
      if (!isset($this->person->uuid) || empty($this->person->uuid)) {
        throw new InvalidVINwikiUUID("No VINWiki UUID provided and a default is not available");
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
    return new VINwiki\Models\PersonFeed($result);
  }

  /**
   * Fetch a VINWiki person profile
   *
   * @param string UUID of the person to fetch
   * @return ?VINwiki\Models\Person profile
   * @throws TypeError
   * @throws InvalidVINWikiSession
   * @throws InvalidVINWikiPerson
   * @throws InvalidVINwikiUUID
   */
  public function fetch_person_profile(string $uuid = ""): ?VINwiki\Models\Person
  {
    // Check Parameters
    if (empty($uuid)) {
      if (!$this->person) {
        throw new InvalidVINWikiPerson("No VINWiki UUID provided and a default is not available");
      }
      if (!isset($this->person->uuid) || empty($this->person->uuid)) {
        throw new InvalidVINwikiUUID("No VINWiki UUID provided and a default is not available");
      }

      // Default
      $uuid = $this->person->uuid;
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
    return new VINwiki\Models\Person($result["profile"]);
  }

  public function fetch_person_posts(string $uuid = ""): ?VINwiki\Models\PersonPosts
  {
    // Check Parameters
    if (empty($uuid)) {
      if (!$this->person) {
        throw new InvalidVINWikiPerson("No VINWiki UUID provided and a default is not available");
      }
      if (!isset($this->person->uuid) || empty($this->person->uuid)) {
        throw new InvalidVINwikiUUID("No VINWiki UUID provided and a default is not available");
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
    return new VINwiki\Models\PersonPosts($result);
  }

  /**
   * Search for vehicles
   *
   * @param string $query VIN / Year / Make / Model
   * @return ?VINwiki\Models\VehicleSearch vehicle search result
   * @throws TypeError
   */
  public function vehicle_search(string $query): ?VINwiki\Models\VehicleSearch
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

    return new VINwiki\Models\VehicleSearch($result["results"]);
  }

  /**
   * Fetch a vehicle by VIN
   *
   * @param  string $vin
   * @return ?VINwiki\Models\Vehicle
   */
  public function fetch_vehicle(string $vin): ?VINwiki\Models\Vehicle
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

    return new VINwiki\Models\Vehicle($result["vehicle"]);
  }
}
