<?php
/*
  Produced 2021
  By https://amattu.com/links/github
  Copy Alec M.
  License GNU Affero General Public License v3.0
*/

// Class Namespace
namespace amattu;

// Exception Classes
class UnknownHTTPException extends \Exception {}
class InvalidHTTPResponseException extends \Exception {}
class InvalidVINWikiStatus extends \Exception {}
class InvalidVINWikiToken extends \Exception {}
class InvalidVINWikiSession extends \Exception {}
class InvalidVINWikiPerson extends \Exception {}
class InvalidVINwikiUUID extends \Exception {}

/**
 * A vinwiki.com API access class
 */
class VINWiki {
  // Variables
  private $token = null;
  private $person = null;
  private $endpoints = Array(
    "authenticate" => "https://rest.vinwiki.com/auth/authenticate", /* POST */
    "pl82vin" => "https://rest.vinwiki.com/vehicle/plate", /* POST */
    "update_vehicle" => "https://rest.vinwiki.com/vehicle/vin/", /* POST */
    "create_post" => "https://rest.vinwiki.com/vehicle/post/", /* POST */
    "vehicle_search" => "https://rest.vinwiki.com/vehicle/search", /* POST */
    "feed" => "https://rest.vinwiki.com/vehicle/feed/", /* GET */
    "me" => "https://rest.vinwiki.com/person/notification_count/me", /* GET */
    "person_feed" => "https://rest.vinwiki.com/person/feed/", /* GET */
    "person_profile" => "https://rest.vinwiki.com/person/profile/", /* GET */
  );
  private $endpoint_cache = Array();
  private const INVALID_TOKEN_NO_SETUP = "Invalid authorization token. Call setup_session first";
  private const VINWIKI_POST_DATE_FORMAT = "Y-m-d\TH:i:s.000\Z";

  /**
   * Setup a VINWiki Session
   *
   * @param string $login
   * @param string $password
   * @return bool result
   * @throws TypeError
   * @throws UnknownHTTPException
   * @throws InvalidHTTPResponseException
   * @throws InvalidVINWikiStatus
   * @throws InvalidVINWikiToken
   * @throws InvalidVINWikiPerson
   * @author Alec M. <https://amattu.com>
   * @date 2021-04-02T10:31:52-040
   */
  public function setup_session(string $login, string $password) : bool
  {
    // Save Parameters, Setup Session
    $endpoint = $this->endpoints["authenticate"];
    $post_result = $this->http_post($endpoint, Array(
      "login" => $login,
      "password" => $password
    ));
    $result = null;

    // Check HTTP Result
    if (!$post_result) {
      throw new UnknownHTTPException("http_post failed for an unknown reason");
    }
    if (!($result = json_decode($this->endpoint_cache[$endpoint], true))) {
      throw new InvalidHTTPResponseException("VINWiki returned an unknown response");
    }
    if ($result["status"] !== "ok") {
      throw new InvalidVINWikiStatus("VINWiki rejected the authorization for an unkown reason");
    }
    if (!isset($result["token"]) || empty($result["token"]) || empty($result["token"]["token"])) {
      throw new InvalidVINWikiToken("VINWiki provided an invalid authorization token");
    }
    if (!isset($result["person"]) || empty($result["person"])) {
      throw new InvalidVINWikiPerson("VINWiki returned an invalid person array");
    }

    // Return
    $this->person = $result["person"];
    $this->token = $result["token"]["token"];
    return true;
  }

  /**
   * Fetch a VIN from a license plate
   *
   * @see setup_session
   * @param string license plate
   * @param string U.S. state abbreviation
   * @return array VINWiki decode response
   * @throws TypeError
   * @throws InvalidVINWikiSession
   * @author Alec M. <https://amattu.com>
   * @date 2021-04-02T11:01:55-040
   */
  public function pl82vin(string $license_plate, string $state_abbr) : ?array
  {
    // Check Parameters
    if (!$this->token) {
      throw new InvalidVINWikiSession(self::INVALID_TOKEN_NO_SETUP);
    }
    if (!$license_plate || strlen($license_plate) <= 0 || strlen($license_plate) > 30) {
      return null;
    }
    if (!$state_abbr || strlen($state_abbr) <= 0 || strlen($state_abbr) > 3) {
      return null;
    }

    // Fetch License Plate
    $endpoint = $this->endpoints["pl82vin"];
    $post_result = $this->http_post($endpoint, Array(
      "plate" => $license_plate,
      "state" => $state_abbr
    ), $this->token);
    $result = null;

    // Check HTTP Result
    if (!$post_result) {
      return null;
    }
    if (!($result = json_decode($this->endpoint_cache[$endpoint], true))) {
      return null;
    }
    if ($result["status"] !== "ok" || !isset($result["plate_lookup"])) {
      return null;
    }

    // Return
    return $result["plate_lookup"];
  }

  /**
   * Update a vehicle by VIN
   *
   * @see setup_session
   * @param string $vin
   * @param int $year
   * @param string $make
   * @param string $model
   * @param string $trim
   * @return bool status
   * @throws TypeError
   * @throws InvalidVINWikiSession
   * @author Alec M. <https://amattu.com>
   * @date 2021-04-02T11:42:11-040
   */
  public function update_vehicle(string $vin, int $year, string $make, string $model, $trim = "") : bool
  {
    // Check Parameters
    if (!$this->token) {
      throw new InvalidVINWikiSession(self::INVALID_TOKEN_NO_SETUP);
    }
    if (strlen($vin) != 17) {
      return false;
    }

    // Update Vehicle
    $endpoint = $this->endpoints["update_vehicle"] . $vin;
    $post_result = $this->http_post($endpoint, Array(
      "year" => $year,
      "make" => $make,
      "model" => $model,
      "trim" => $trim
    ), $this->token);
    $result = null;

    // Check HTTP Result
    if (!($result = json_decode($this->endpoint_cache[$endpoint], true))) {
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
   * @param string $vin
   * @param array Array(
   *  string ?"class_name",
   *  string ?"client",
   *  DateTime ?"event_date",
   *  int ?"mileage",
   *  string "text"
   * )
   * @return array VINWiki response
   * @throws TypeError
   * @throws InvalidVINWikiSession
   * @author Alec M. <https://amattu.com>
   * @date 2021-04-02T11:46:39-040
   */
  public function create_post(string $vin, array $post) : ?array
  {
    // Check Parameters
    if (!$this->token) {
      throw new InvalidVINWikiSession(self::INVALID_TOKEN_NO_SETUP);
    }
    if (strlen($vin) != 17) {
      return null;
    }
    if (!isset($post["text"]) || strlen($post["text"]) <= 0) {
      return null;
    }
    if (!isset($post["class_name"])) {
      $post["class_name"] = "generic";
    }
    if (!isset($post["client"])) {
      $post["client"] = "web";
    }
    if (!isset($post["event_date"]) || !($post["event_date"] instanceof \DateTime)) {
      $post["event_date"] =  date(self::VINWIKI_POST_DATE_FORMAT);
    } else {
      $post["event_date"] = $post["event_date"]->format(self::VINWIKI_POST_DATE_FORMAT);
    }

    // Create Post
    $endpoint = $this->endpoints["create_post"] . $vin;
    $post_result = $this->http_post($endpoint, $post, $this->token);
    $result = null;

    // Check HTTP Result
    if (!($result = json_decode($this->endpoint_cache[$endpoint], true))) {
      return null;
    }
    if ($result["status"] !== "ok") {
      return null;
    }
    if (!isset($result["post"])) {
      return null;
    }

    // Return
    return $result["post"];
  }

  /**
   * Fetch Vehicle Feed By VIN
   *
   * @param string $vin
   * @return array feed array
   * @throws TypeError
   * @author Alec M. <https://amattu.com>
   * @date 2021-03-31T18:19:58-040
   */
  public function fetch_feed(string $vin) : array
  {
    // Checks
    if (strlen($vin) != 17) {
      return Array();
    }

    // Variables
    $result = $this->http_get($this->endpoints["feed"] . $vin);
    $feed = $result ? json_decode($result, true) : Array();

    // Return
    return $feed["feed"];
  }

  /**
   * Fetch a user notification feed
   *
   * @return ?array VINWiki notification result
   * @throws InvalidVINWikiSession
   * @author Alec M. <https://amattu.com>
   * @date 2021-04-08T11:11:54-040
   */
  public function fetch_person_notifications() : ?array
  {
    // Check Parameters
    if (!$this->token) {
      throw new InvalidVINWikiSession(self::INVALID_TOKEN_NO_SETUP);
    }

    // Fetch Notifications
    $get_result = $this->http_get($this->endpoints["me"], $this->token);
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
   * @return ?array VINWiki feed
   * @throws InvalidVINWikiSession
   * @throws InvalidVINWikiPerson
   * @throws InvalidVINwikiUUID
   * @author Alec M. <https://amattu.com>
   * @date 2021-04-08T11:37:33-040
   */
  public function fetch_person_feed() : ?array
  {
    // Check Parameters
    if (!$this->token) {
      throw new InvalidVINWikiSession(self::INVALID_TOKEN_NO_SETUP);
    }
    if (!$this->person || empty($this->person)) {
      throw new InvalidVINWikiPerson("No valid VINWiki person set. Please call setup_session first.");
    }
    if (!isset($this->person["uuid"]) || empty($this->person["uuid"])) {
      throw new InvalidVINwikiUUID("No valid VINWiki uuid set. Please call setup_session first.");
    }

    // Fetch Feed
    $get_result = $this->http_get($this->endpoints["person_feed"] . $this->person["uuid"], $this->token);
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
    return $result["feed"];
  }

  /**
   * Fetch a VINWiki person profile
   *
   * @param string VINWiki UUID
   * @return ?array profile
   * @throws TypeError
   * @throws InvalidVINWikiSession
   * @throws InvalidVINWikiPerson
   * @throws InvalidVINwikiUUID
   * @author Alec M. <https://amattu.com>
   * @date 2021-04-08T12:05:59-040
   */
  public function fetch_person_profile(string $uuid = "") : ?array
  {
    // Check Parameters
    if (!$this->token) {
      throw new InvalidVINWikiSession(self::INVALID_TOKEN_NO_SETUP);
    }
    if (empty($uuid)) {
      if (!$this->person || empty($this->person)) {
        throw new InvalidVINWikiPerson("No VINWiki UUID provided and a default is not available");
      }
      if (!isset($this->person["uuid"]) || empty($this->person["uuid"])) {
        throw new InvalidVINwikiUUID("No VINWiki UUID provided and a default is not available");
      }

      // Default
      $uuid = $this->person["uuid"];
    }

    // Fetch Profile
    $get_result = $this->http_get($this->endpoints["person_profile"] . $uuid, $this->token);
    $result = null;

    // Check HTTP Result
    if (!($result = json_decode($get_result, true))) {
      return null;
    }
    if ($result["status"] !== "ok") {
      return null;
    }
    if (!isset($result["profile"])) {
      return null;
    }

    // Return
    return $result["profile"];
  }

  /**
   * Search for vehicles
   *
   * @param string VIN / Year / Make / Model
   * @return ?array VINWiki search result
   * @throws TypeError
   * @throws InvalidVINWikiSession
   * @author Alec M. <https://amattu.com>
   * @date 2021-04-09T10:14:57-040
   */
  public function vehicle_search(string $query) : ?array
  {
    // Check Parameters
    if (!$this->token) {
      throw new InvalidVINWikiSession(self::INVALID_TOKEN_NO_SETUP);
    }
    if (!$query || strlen($query) < 3) {
      return null;
    }

    // Query For Vehicles
    $post_result = $this->http_post($this->endpoints["vehicle_search"], Array(
      "query" => $query,
    ), $this->token);
    $result = null;

    // Check HTTP Result
    if (!$post_result) {
      return null;
    }
    if (!($result = json_decode($this->endpoint_cache[$this->endpoints["vehicle_search"]], true))) {
      return null;
    }
    if ($result["status"] !== "ok" || !isset($result["results"])) {
      return null;
    }

    // Return
    return $result["results"];
  }

  /**
   * Perform HTTP Post Request
   *
   * @param string url
   * @param array $params
   * @return boolean result status
   * @throws TypeError
   * @author Alec M. <https://amattu.com>
   * @date 2021-03-31T11:11:18-040
   */
  private function http_post(string $endpoint, array $fields, $bearer = "") : bool
  {
    // cURL Initialization
    $handle = curl_init();
    $field_string = "";
    $result = "";
    $error = 0;
    foreach($fields as $k => $v) { $field_string .= $k ."=". $v . "&"; }
    rtrim($field_string, "&");

    // Options
    if ($bearer) {
      curl_setopt($handle, CURLOPT_HTTPHEADER, ["Authorization: Bearer $bearer"]);
    }
    curl_setopt($handle, CURLOPT_URL, $endpoint);
    curl_setopt($handle, CURLOPT_POST, count($fields));
    curl_setopt($handle, CURLOPT_POSTFIELDS, $field_string);
    curl_setopt($handle, CURLOPT_FAILONERROR, 1);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($handle, CURLOPT_MAXREDIRS, 2);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($handle, CURLOPT_TIMEOUT, 20);

    // Fetch Result
    $result = curl_exec($handle);
    $error = curl_errno($handle);

    // Return
    curl_close($handle);
    $this->endpoint_cache[$endpoint] = $result && !$error ? $result : "";
    return $result && !$error ? true : false;
  }

  /**
   * Perform HTTP GET Request
   *
   * @param string $endpoint
   * @param string $bearer
   * @return string HTTP result body | null
   * @throws TypeError
   * @author Alec M. <https://amattu.com>
   * @date 2021-03-31T18:13:00-040
   */
  private function http_get(string $endpoint, string $bearer = "") : ?string
  {
    // cURL Initialization
    $handle = curl_init();
    $result = "";
    $error = 0;

    // Options
    if ($bearer) {
      curl_setopt($handle, CURLOPT_HTTPHEADER, ["Authorization: Bearer $bearer"]);
    }
    curl_setopt($handle, CURLOPT_URL, $endpoint);
    curl_setopt($handle, CURLOPT_HTTPGET, 1);
    curl_setopt($handle, CURLOPT_FAILONERROR, 1);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($handle, CURLOPT_MAXREDIRS, 2);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($handle, CURLOPT_TIMEOUT, 20);

    // Fetch Result
    $result = curl_exec($handle);
    $error = curl_errno($handle);

    // Return
    curl_close($handle);
    return $result && !$error ? $result : "";
  }
}
?>
