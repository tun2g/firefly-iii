<?php
/**
 * RecurrenceTransactionMeta.php
 * Copyright (c) 2019 james@firefly-iii.org
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
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * FireflyIII\Models\RecurrenceTransactionMeta
 *
 * @property int                   $id
 * @property null|Carbon           $created_at
 * @property null|Carbon           $updated_at
 * @property null|Carbon           $deleted_at
 * @property int|string            $rt_id
 * @property string                $name
 * @property mixed                 $value
 * @property RecurrenceTransaction $recurrenceTransaction
 *
 * @method static \Illuminate\Database\Eloquent\Builder|RecurrenceTransactionMeta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurrenceTransactionMeta newQuery()
 * @method static Builder|RecurrenceTransactionMeta                               onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurrenceTransactionMeta query()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurrenceTransactionMeta whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurrenceTransactionMeta whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurrenceTransactionMeta whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurrenceTransactionMeta whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurrenceTransactionMeta whereRtId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurrenceTransactionMeta whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurrenceTransactionMeta whereValue($value)
 * @method static Builder|RecurrenceTransactionMeta                               withTrashed()
 * @method static Builder|RecurrenceTransactionMeta                               withoutTrashed()
 *
 * @mixin Eloquent
 */
class RecurrenceTransactionMeta extends Model
{
    use ReturnsIntegerIdTrait;
    use SoftDeletes;

    protected $casts
        = [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'name'       => 'string',
            'value'      => 'string',
        ];

    protected $fillable = ['rt_id', 'name', 'value'];

    /** @var string The table to store the data in */
    protected $table = 'rt_meta';

    public function recurrenceTransaction(): BelongsTo
    {
        return $this->belongsTo(RecurrenceTransaction::class, 'rt_id');
    }

    protected function rtId(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => (int)$value,
        );
    }
}
