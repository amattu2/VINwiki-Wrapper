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
 * A VINwiki Vehicle Model
 */
class Vehicle extends BaseModel
{
  protected ?string $created;
  protected ?bool $decoder_fail;
  protected ?int $follower_count;
  protected ?string $icon_photo;
  protected ?int $id;
  protected ?string $long_name;
  protected ?string $make;
  protected ?string $model;
  protected ?bool $ownership;
  protected ?int $post_count;
  protected ?string $poster_photo;
  protected ?string $trim;
  protected ?string $updated;
  protected ?bool $user_updated;
  protected ?string $year;
  protected string $vin;

  public function __construct(array $data)
  {
    parent::__construct($data);
  }
}
