<?php
/*
 * Produced: Sun Mar 12 2023
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

namespace amattu2\VINwiki\Models;

/**
 * A VINwiki Person
 */
class Person extends BaseModel
{
  protected mixed $avatar;
  protected ?string $bio = "";
  protected ?string $display_name = "";
  protected ?string $email;
  protected ?string $first_name;
  protected int $follower_count;
  protected int $following_count;
  protected int $following_vehicle_count;
  protected int $id;
  protected ?string $last_name;
  protected string $location = "";
  protected int $post_count;
  protected mixed $profile;
  protected mixed $profile_picture_uuid;
  protected ?string $social_facebook = "";
  protected ?string $social_instagram = "";
  protected ?string $social_linkedin = "";
  protected ?string $social_twitter = "";
  protected ?string $username;
  protected string $uuid;
  protected ?string $website_url = "";

  public function __construct(array $data)
  {
    parent::__construct($data);
  }
}
