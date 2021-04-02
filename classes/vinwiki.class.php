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

/**
 * A vinwiki.com API access class
 */
class VINWiki {
  // Variables
  private $login = "";
  private $password = "";
  private $token = null;
  private $endpoints = Array(
    "authenticate" => "https://rest.vinwiki.com/auth/authenticate",
    "feed" => "https://rest.vinwiki.com/vehicle/feed/",
    "pl82vin" => "https://rest.vinwiki.com/vehicle/plate"
  );
  private $endpoint_cache = Array();

  /**
   * Setup the VINWiki class
   *
   * @throws None
   * @author Alec M. <https://amattu.com>
   * @date 2021-03-31T11:03:28-040
   */
  public function __construct() {}

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
   * @author Alec M. <https://amattu.com>
   * @date 2021-04-02T10:31:52-040
   */
  public function setup_session(string $login, string $password) : bool
  {
    // Save Parameters, Setup Session
    $this->login = $login;
    $this->password = $password;
    $post_result = $this->http_post("authenticate", Array(
      "login" => $login,
      "password" => $password
    ));
    $result = null;

    // Check HTTP Result
    if (!$post_result) {
      throw new UnknownHTTPException("http_post failed for an unknown reason");
    }
    if (!($result = json_decode($this->endpoint_cache["authenticate"], true))) {
      throw new InvalidHTTPResponseException("VINWiki returned an unknown response");
    }
    if ($result["status"] !== "ok") {
      throw new InvalidVINWikiStatus("VINWiki rejected the authorization for an unkown reason");
    }
    if (!isset($result["token"]) || empty($result["token"]) || empty($result["token"]["token"])) {
      throw new InvalidVINWikiToken("VINWiki provided an invalid authorization token");
    }

    // Return
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
    if (!$this->token) {
      throw new InvalidVINWikiSession("Invalid authorization token. Call setup_session first");
    }
    if (!$license_plate || strlen($license_plate) <= 0 || strlen($license_plate) > 30) {
      return null;
    }
    if (!$state_abbr || strlen($state_abbr) <= 0 || strlen($state_abbr) > 3) {
      return null;
    }

    // Fetch License Plate
    $post_result = $this->http_post("pl82vin", Array(
      "plate" => $license_plate,
      "state" => $state_abbr
    ), $this->token);
    $result = null;

    // Check HTTP Result
    if (!$post_result) {
      return null;
    }
    if (!($result = json_decode($this->endpoint_cache["pl82vin"], true))) {
      return null;
    }
    if ($result["status"] !== "ok" || !isset($result["plate_lookup"])) {
      return null;
    }

    // Return
    return $result["plate_lookup"];
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
    return $feed;
  }

  /**
   * Perform HTTP Post Request
   *
   * @param string $endpoint
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
    curl_setopt($handle, CURLOPT_URL, $this->endpoints[$endpoint]);
    curl_setopt($handle, CURLOPT_POST, count($fields));
    curl_setopt($handle, CURLOPT_POSTFIELDS, $field_string);
    curl_setopt($handle, CURLOPT_FAILONERROR, 1);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($handle, CURLOPT_MAXREDIRS, 2);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($handle, CURLOPT_TIMEOUT, 10);

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
   * @return string HTTP result body | null
   * @throws TypeError
   * @author Alec M. <https://amattu.com>
   * @date 2021-03-31T18:13:00-040
   */
  private function http_get(string $endpoint) : ?string
  {
    // cURL Initialization
    $handle = curl_init();
    $result = "";
    $error = 0;

    // Options
    curl_setopt($handle, CURLOPT_URL, $endpoint);
    curl_setopt($handle, CURLOPT_HTTPGET, 1);
    curl_setopt($handle, CURLOPT_FAILONERROR, 1);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($handle, CURLOPT_MAXREDIRS, 2);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($handle, CURLOPT_TIMEOUT, 10);

    // Fetch Result
    $result = curl_exec($handle);
    $error = curl_errno($handle);

    // Return
    curl_close($handle);
    return $result && !$error ? $result : "";
  }
}
?>
