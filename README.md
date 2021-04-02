# Introduction
This is a PHP-7 VinWiki.com API endpoint wrapper. Take a look at the index files or Usage section below.

# Usage
### Setup a class instance
```PHP
// Require file
require("classes/vinwiki.class.php");

// Spawn instance
$wrapper = new amattu\VINWiki();
```

### Fetch a API token
PHPDoc
```PHP
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
```

Usage
```PHP
$wrapper->setup_session("email|username", "password")
```

Only needed if you use:
- pl82vin
- update_vehicle
- create_post

### Fetch a vehicle feed
PHPDoc
```PHP
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
```

Usage
```PHP
$wrapper->fetch_feed("VIN_NUMBER")
```

### Decode a license plate
PHPDoc
```PHP
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
```

Usage
```PHP
$wrapper->pl82vin("PLATE_NO", "STATE")
```

Success return result
```
Array
(
    [vin] =>
    [description] =>
    [year] =>
    [make] =>
    [model] =>
)
```

### Update a vehicle
PHPDoc
```PHP
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
```

Usage
```PHP
$wrapper->update_vehicle("VIN", "YEAR", "MAKE", "MODEL", "TRIM")
```

Success return result
```
true
```

### Create a post
PHPDoc
```PHP
/**
 * Create a new VINWiki post
 *
 * @param string $vin
 * @param array Array(
 *  ?"class_name",
 *  ?"client",
 *  ?"event_date",
 *  ?"mileage",
 *  "text"
 * )
 * @return array VINWiki response
 * @throws TypeError
 * @throws InvalidVINWikiSession
 * @author Alec M. <https://amattu.com>
 * @date 2021-04-02T11:46:39-040
 */
public function create_post(string $vin, array $post) : ?array
```

Usage
```PHP
$wrapper->create_post("VIN", Array("text" => "added to vinwiki via API"))
```

Success return result
```
Array
(
    [uuid] =>
    [id] =>
    [type] =>
    [client] =>
    [dest_url] =>
    [subject_uuid] =>
    [data] =>
    [post_text] =>
    [comment_count] =>
    [locale] =>
    [post_time] =>
    [post_date] =>
    [post_date_ago] =>
    [event_time] =>
    [event_date] =>
    [person] => Array
        (
            [id] =>
            [uuid] =>
            [short_url] =>
            [username] =>
            [avatar] =>
        )
    [vehicle] => Array
        (
            [vin] =>
            [id] =>
            [make] =>
            [model] =>
            [year] =>
            [trim] =>
            [long_name] =>
            [follower_count] =>
            [post_count] =>
        )
    [image] => Array
        (
        )

)
```

# Notes
N/A

# Requirements & Dependencies
PHP 7.0+
