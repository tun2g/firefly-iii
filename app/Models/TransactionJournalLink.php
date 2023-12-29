<?php

/**
 * TransactionJournalLink.php
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * FireflyIII\Models\TransactionJournalLink
 *
 * @property int                $id
 * @property null|Carbon        $created_at
 * @property null|Carbon        $updated_at
 * @property int                $link_type_id
 * @property int                $source_id
 * @property int                $destination_id
 * @property null|string        $comment
 * @property TransactionJournal $destination
 * @property LinkType           $linkType
 * @property Collection|Note[]  $notes
 * @property null|int           $notes_count
 * @property TransactionJournal $source
 * @property string             $inward
 * @property string             $outward
 *
 * @method static Builder|TransactionJournalLink newModelQuery()
 * @method static Builder|TransactionJournalLink newQuery()
 * @method static Builder|TransactionJournalLink query()
 * @method static Builder|TransactionJournalLink whereComment($value)
 * @method static Builder|TransactionJournalLink whereCreatedAt($value)
 * @method static Builder|TransactionJournalLink whereDestinationId($value)
 * @method static Builder|TransactionJournalLink whereId($value)
 * @method static Builder|TransactionJournalLink whereLinkTypeId($value)
 * @method static Builder|TransactionJournalLink whereSourceId($value)
 * @method static Builder|TransactionJournalLink whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class TransactionJournalLink extends Model
{
    use ReturnsIntegerIdTrait;

    protected $casts
        = [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];

    /** @var string The table to store the data in */
    protected $table = 'journal_links';

    /**
     * Route binder. Converts the key in the URL to the specified object (or throw 404).
     *
     * @throws NotFoundHttpException
     */
    public static function routeBinder(string $value): self
    {
        if (auth()->check()) {
            $linkId = (int)$value;
            $link   = self::where('journal_links.id', $linkId)
                ->leftJoin('transaction_journals as t_a', 't_a.id', '=', 'source_id')
                ->leftJoin('transaction_journals as t_b', 't_b.id', '=', 'destination_id')
                ->where('t_a.user_id', auth()->user()->id)
                ->where('t_b.user_id', auth()->user()->id)
                ->first(['journal_links.*'])
            ;
            if (null !== $link) {
                return $link;
            }
        }

        throw new NotFoundHttpException();
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(TransactionJournal::class, 'destination_id');
    }

    public function linkType(): BelongsTo
    {
        return $this->belongsTo(LinkType::class);
    }

    /**
     * Get all of the notes.
     */
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(TransactionJournal::class, 'source_id');
    }

    protected function destinationId(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => (int)$value,
        );
    }

    protected function linkTypeId(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => (int)$value,
        );
    }

    protected function sourceId(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => (int)$value,
        );
    }
}
