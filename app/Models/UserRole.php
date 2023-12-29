<?php

/*
 * UserRole.php
 * Copyright (c) 2021 james@firefly-iii.org
 *
 * This file is part of Firefly III (https://github.com/firefly-iii).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace FireflyIII\Models;

use Carbon\Carbon;
use Eloquent;
use FireflyIII\Support\Models\ReturnsIntegerIdTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class UserRole
 *
 * @property int                          $id
 * @property null|Carbon                  $created_at
 * @property null|Carbon                  $updated_at
 * @property null|string                  $deleted_at
 * @property string                       $title
 * @property Collection|GroupMembership[] $groupMemberships
 * @property null|int                     $group_memberships_count
 *
 * @method static Builder|UserRole newModelQuery()
 * @method static Builder|UserRole newQuery()
 * @method static Builder|UserRole query()
 * @method static Builder|UserRole whereCreatedAt($value)
 * @method static Builder|UserRole whereDeletedAt($value)
 * @method static Builder|UserRole whereId($value)
 * @method static Builder|UserRole whereTitle($value)
 * @method static Builder|UserRole whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class UserRole extends Model
{
    use ReturnsIntegerIdTrait;

    protected $fillable = ['title'];

    public function groupMemberships(): HasMany
    {
        return $this->hasMany(GroupMembership::class);
    }
}
