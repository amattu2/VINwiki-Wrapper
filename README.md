# Introduction

This is an unofficial [VINwiki.com](https://vinwiki.com) API wrapper for PHP. It offers a simple interface
for interacting with the VINwiki API. Among other things, it allows you to:

- Read and update user profiles
- Read and update vehicle information
- Read, create, and delete posts
- Decode license plates by country and state
- Search for vehicles by VIN, Year, Make, Model, or free-text

# Usage

## Installation

Via Composer

```bash
composer require amattu2/vinwiki-wrapper
```

Then, require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

Instantiate the class:

```php
$wrapper = new amattu2\VINwiki\Client("email|username", "password");
```

## Functions

### People

These functions relate to person interactions.

<details>
  <summary>getPersonProfile(string $uuid = "") : ?VINwiki\Models\Person</summary>

  Returns a VINwiki user profile. If no user is specified,
  the current user's profile is returned.

  ```php
  // for the current user
  print_r($wrapper->getPersonProfile());

  // or for a specific user
  print_r($wrapper->getPersonProfile("61382da4-25c6-494f-8065-87afdfb4f50d"));
  ```

</details>

<details>
  <summary>updatePersonProfile(string $uuid, VINwiki\Models\Person $person): ?VINwiki\Models\Person</summary>

  Updates the current user's profile. Returns the updated profile on success. The only required field is "email".

  See [Models\Person](src/models/Person.php) for more fields.

  ```php
  $person = new amattu2\VINwiki\Models\Person([
    "email" => "abc123@example.com",
  ]);
  print_r($wrapper->updatePersonProfile("{UUID GOES HERE}", $person));
  ```

</details>

<details>
  <summary>getPersonNotifications() : ?array</summary>

  Get the current user's notifications.

  ```php
  print_r($wrapper->getPersonNotifications());
  ```

</details>

<details>
  <summary>getPersonFeed(string $uuid = "") : ?VINwiki\Models\PersonFeed</summary>

  Get a user's post feed. If no user is specified, the current user's feed is returned.

  ```php
  // for the current user
  print_r($wrapper->getPersonFeed());

  // or for a specific user
  print_r($wrapper->getPersonFeed("61382da4-25c6-494f-8065-87afdfb4f50d"));
  ```

</details>

<details>
  <summary>getPersonPosts(string $uuid = ""): ?VINwiki\Models\PersonPosts</summary>

  Get a user's posts. If no user is specified, the current user's posts are returned.

  ```php
  // for the current user
  print_r($wrapper->getPersonPosts());

  // or for a specific user
  print_r($wrapper->getPersonPosts("61382da4-25c6-494f-8065-87afdfb4f50d"));
  ```

</details>

<details>
  <summary>getRecentVins(): ?VINwiki\Models\RecentVins</summary>

  Returns a list of vehicles the user has recently posted on or interacted with.
  Does not include vehicles that the user has only viewed.

  ```php
  print_r($wrapper->getRecentVins());
  ```

</details>

### Vehicles

These functions handle any vehicle-related interactions.

<details>
  <summary>plateLookup(string $plate, string $country, string $state): ?VINwiki\Models\PlateLookup</summary>

  Returns a vehicle decoded by the license plate. Currently supports US/UK plates.

  ```php
  print_r($wrapper->plateLookup("HELLO", "US", "CA"));
  ```

</details>

<details>
  <summary>getFeed(string $vin): ?VINwiki\Models\VehicleFeed</summary>

  Get a vehicle's post feed (i.e. when you visit a vehicle's page on VINwiki.com)

  ```php
  print_r($wrapper->getFeed("WBAPL33579A406957"));
  ```

</details>

<details>
  <summary>getVehicle(string $vin): ?VINwiki\Models\Vehicle</summary>

  Get a vehicle's information by VIN.

  ```php
  print_r($wrapper->getVehicle("WBAPL33579A406957"));
  ```

</details>

<details>
  <summary>vehicleSearch(string $query): ?VINwiki\Models\VehicleSearch</summary>

  Perform a free-text search for vehicles by Year, Make, Model, or VIN.

  ```php
  print_r($wrapper->vehicleSearch("2011 Toyota Corolla"));
  ```

</details>

<details>
  <summary>updateVehicle(string $vin, int $year, string $make, string $model, string $trim = ""): bool</summary>

  Update a vehicle's Year, Make, Model, and Trim by VIN. Returns true if successful.

  ```php
  print_r($wrapper->updateVehicle("WBAPL33579A406957", 2009, "BMW", "335i", "xDrive"));
  ```

</details>

<details>
  <summary>createPost(string $vin, VINwiki\Models\VehiclePost $post): ?VINwiki\Models\FeedPost</summary>

  Create a new post on a vehicle. Requires a VIN and a VehiclePost object. Returns the new post on success.

  See [Models\VehiclePost](src/models/VehiclePost.php) for more information.

  ```php
  // Create a new post class
  $post = new amattu2\VINwiki\Models\VehiclePost([
    "mileage" => 43000,
    "text" => "This is a test post from the VINwiki-Wrapper PHP library.",
  ]);

  // Post it
  print_r($wrapper->createPost("WBAPL33579A406957", $post));
  ```

</details>

<details>
  <summary>deletePost(string $uuid) : bool</summary>

  Delete a post by UUID. Returns true if successful. Requires the user to be the author of the post.

  ```php
  print_r($wrapper->deletePost("61382da4-25c6-494f-8065-87afdfb4f50d"));
  ```

</details>

# Requirements

- PHP 7.4+
- Composer (Preferred)
