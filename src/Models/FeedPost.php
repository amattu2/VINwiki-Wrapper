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
 * A VINwiki Post from Feed model
 */
class FeedPost extends BaseModel
{
  protected string $client;
  protected string $comment_count;
  protected mixed $data;
  protected string $dest_url;
  protected string $event_date;
  protected string $event_time;
  protected int $id;
  protected ?int $mileage;
  protected PostImage $image;
  protected mixed $locale;
  protected Person $person;
  protected string $post_date;
  protected string $post_date_ago;
  protected string $post_text;
  protected string $post_time;
  protected ?string $subject_uuid;
  protected string $type;
  protected string $uuid;
  protected Vehicle $vehicle;
  public function __construct(array $data)
  {
    parent::__construct($data);
  }
}
