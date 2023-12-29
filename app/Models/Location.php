<?php

/**
 * Location.php
 * Copyright (c) 2020 james@firefly-iii.org
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
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * FireflyIII\Models\Location
 *
 * @property int                  $id
 * @property null|Carbon          $created_at
 * @property null|Carbon          $updated_at
 * @property null|Carbon          $deleted_at
 * @property int                  $locatable_id
 * @property string               $locatable_type
 * @property null|float           $latitude
 * @property null|float           $longitude
 * @property null|int             $zoom_level
 * @property Account[]|Collection $accounts
 * @property null|int             $accounts_count
 * @property \Eloquent|Model      $locatable
 *
 * @method static Builder|Location newModelQuery()
 * @method static Builder|Location newQuery()
 * @method static Builder|Location query()
 * @method static Builder|Location whereCreatedAt($value)
 * @method static Builder|Location whereDeletedAt($value)
 * @method static Builder|Location whereId($value)
 * @method static Builder|Location whereLatitude($value)
 * @method static Builder|Location whereLocatableId($value)
 * @method static Builder|Location whereLocatableType($value)
 * @method static Builder|Location whereLongitude($value)
 * @method static Builder|Location whereUpdatedAt($value)
 * @method static Builder|Location whereZoomLevel($value)
 *
 * @mixin Eloquent
 */
class Location extends Model
{
    use ReturnsIntegerIdTrait;

    protected $casts
        = [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'zoomLevel'  => 'int',
            'latitude'   => 'float',
            'longitude'  => 'float',
        ];

    protected $fillable = ['locatable_id', 'locatable_type', 'latitude', 'longitude', 'zoom_level'];

    /**
     * Add rules for locations.
     */
    public static function requestRules(array $rules): array
    {
        $rules['latitude']   = 'numeric|min:-90|max:90|nullable|required_with:longitude';
        $rules['longitude']  = 'numeric|min:-180|max:180|nullable|required_with:latitude';
        $rules['zoom_level'] = 'numeric|min:0|max:80|nullable|required_with:latitude';

        return $rules;
    }

    /**
     * Get all the accounts.
     */
    public function accounts(): MorphMany
    {
        return $this->morphMany(Account::class, 'noteable');
    }

    /**
     * Get all the owning attachable models.
     */
    public function locatable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function locatableId(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => (int)$value,
        );
    }
}
