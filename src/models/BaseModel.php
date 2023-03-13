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
 * A base model for all VINwiki models
 */
class BaseModel
{
  /**
   * Construct a new model
   *
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $reflection = new \ReflectionClass($this);
    $base = get_parent_class($this::class);

    foreach ($data as $k => $v) {
      if ($reflection->hasProperty($k) === false) {
        continue;
      }

      $type = $reflection->getProperty($k)->getType()->getName();
      if (class_exists($type) && is_subclass_of($type, $base)) {
        $this->$k = new $type($v);
        continue;
      }

      $this->$k = $v;
    }
  }

  /**
   * Get model property
   *
   * @param  string $name
   * @return mixed value
   */
  public function __get(string $name): mixed
  {
    return $this->$name ?? null;
  }

  /**
   * Check if model property exists
   *
   * @param  string  $name
   * @return boolean
   */
  public function __isset(string $name): bool
  {
    if (!property_exists($this, $name)) {
      return false;
    }

    return true;
  }
}
