<?php
/*
  Produced 2021
  By https://amattu.com/links/github
  Copy Alec M.
  License GNU Affero General Public License v3.0
*/

// Class Namespace
namespace amattu;

/**
 * A vinwiki.com API access class
 */
class VINWiki {
  // Variables
  private $login = "";
  private $password = "";
  private $token = "";
  private $endpoint_cache = Array();
  private $endpoints = Array(
    "authenticate" => "https://rest.vinwiki.com/auth/authenticate"
  );

  /**
   * Setup A VINWiki Session
   *
   * @param string $login
   * @param string $password
   * @throws
   * @author Alec M. <https://amattu.com>
   * @date 2021-03-31T11:03:28-040
   */
  public function __construct(string $login, string $password)
  {
    // Save Parameters, Setup Session
    $this->login = $login;
    $this->password = $password;
    $result = $this->http_post("authenticate", Array(
      "login" => $login,
      "password" => $password
    ));

    // Check Result
    if ($result) {
      print_r($this->endpoint_cache["authenticate"]);
    }
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
  private function http_post(string $endpoint, array $fields) : bool
  {
    // cURL Initialization
    $handle = curl_init();
    $field_string = "";
    $result = "";
    $error = 0;
    foreach($fields as $k => $v) { $field_string .= $k ."=". $v . "&"; }
    rtrim($field_string, "&");

    // Options
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
}
?>
