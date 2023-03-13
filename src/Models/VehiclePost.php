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

use JsonSerializable;
use DateTime;

/**
 * A VINwiki Vehicle Post Model
 *
 * Note: This model is used to CREATE posts
 */
class VehiclePost extends BaseModel implements JsonSerializable
{
  /**
   * Unknown functionality
   *
   *
   * @var string generic
   */
  protected string $class_name = "generic";

  /**
   * The client creating the post
   *
   * @var string web|ios|android|vinbot
   */
  protected string $client = "amattu2\VINwiki-Wrapper";

  /**
   * The date of the event
   *
   * Note: eventually formatted as Y-m-d\TH:i:s.000\Z
   *
   * @var DateTime
   */
  protected DateTime $event_date;

  /**
   * The location of the post
   *
   * e.g.
   * 123 Main St, Anytown, USA
   * or Anytown, USA
   * or Anytown
   * or USA
   *
   * @var string
   */
  protected string $locale = "";

  /**
   * The mileage of the vehicle at event
   *
   * @var integer
   */
  protected int $mileage = 0;

  /**
   * The body text of the post
   *
   * @var string
   */
  protected string $text;

  public function __construct(array $data)
  {
    parent::__construct($data);
  }

	public function jsonSerialize() : array
  {
    return [
      "class_name" => $this->class_name,
      "client" => $this->client,
      "event_date" => ($this->event_date ?? new DateTime())->format("Y-m-d\TH:i:s.000\Z"),
      "locale" => $this->locale,
      "mileage" => $this->mileage,
      "text" => $this->text
    ];
	}
}
