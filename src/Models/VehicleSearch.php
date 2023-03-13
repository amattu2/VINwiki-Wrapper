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
 * A VINwiki Vehicle Lookup model
 */
class VehicleSearch extends BaseModel
{
  protected int $count;
  protected string $term;
  protected ?array $vehicles;

  public function __construct(array $data)
  {
    $this->count = $data['count'];
    $this->term = $data['term'];

    foreach ($data['vehicles'] as $vehicle) {
      $this->vehicles[] = new Vehicle($vehicle);
    }
  }
}
