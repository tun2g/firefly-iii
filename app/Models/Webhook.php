<?php
/*
 * Webhook.php
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
use FireflyIII\Enums\WebhookDelivery;
use FireflyIII\Enums\WebhookResponse;
use FireflyIII\Enums\WebhookTrigger;
use FireflyIII\Support\Models\ReturnsIntegerIdTrait;
use FireflyIII\Support\Models\ReturnsIntegerUserIdTrait;
use FireflyIII\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * FireflyIII\Models\Webhook
 *
 * @property int                         $id
 * @property null|Carbon                 $created_at
 * @property null|Carbon                 $updated_at
 * @property null|Carbon                 $deleted_at
 * @property int                         $user_id
 * @property bool                        $active
 * @property int                         $trigger
 * @property int                         $response
 * @property int                         $delivery
 * @property string                      $url
 * @property User                        $user
 * @property Collection|WebhookMessage[] $webhookMessages
 * @property null|int                    $webhook_messages_count
 *
 * @method static Builder|Webhook                            newModelQuery()
 * @method static Builder|Webhook                            newQuery()
 * @method static \Illuminate\Database\Query\Builder|Webhook onlyTrashed()
 * @method static Builder|Webhook                            query()
 * @method static Builder|Webhook                            whereActive($value)
 * @method static Builder|Webhook                            whereCreatedAt($value)
 * @method static Builder|Webhook                            whereDeletedAt($value)
 * @method static Builder|Webhook                            whereDelivery($value)
 * @method static Builder|Webhook                            whereId($value)
 * @method static Builder|Webhook                            whereResponse($value)
 * @method static Builder|Webhook                            whereTrigger($value)
 * @method static Builder|Webhook                            whereUpdatedAt($value)
 * @method static Builder|Webhook                            whereUrl($value)
 * @method static Builder|Webhook                            whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Webhook withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Webhook withoutTrashed()
 *
 * @property string $title
 * @property string $secret
 *
 * @method static Builder|Webhook whereSecret($value)
 * @method static Builder|Webhook whereTitle($value)
 *
 * @property int $user_group_id
 *
 * @method static Builder|Webhook whereUserGroupId($value)
 *
 * @mixin Eloquent
 */
class Webhook extends Model
{
    use ReturnsIntegerIdTrait;
    use ReturnsIntegerUserIdTrait;
    use SoftDeletes;

    protected $casts
                        = [
                            'active'   => 'boolean',
                            'trigger'  => 'integer',
                            'response' => 'integer',
                            'delivery' => 'integer',
                        ];
    protected $fillable = ['active', 'trigger', 'response', 'delivery', 'user_id', 'user_group_id', 'url', 'title', 'secret'];

    public static function getDeliveries(): array
    {
        $array = [];
        $set   = WebhookDelivery::cases();
        foreach ($set as $item) {
            $array[$item->value] = $item->name;
        }

        return $array;
    }

    public static function getDeliveriesForValidation(): array
    {
        $array = [];
        $set   = WebhookDelivery::cases();
        foreach ($set as $item) {
            $array[$item->name]  = $item->value;
            $array[$item->value] = $item->value;
        }

        return $array;
    }

    public static function getResponses(): array
    {
        $array = [];
        $set   = WebhookResponse::cases();
        foreach ($set as $item) {
            $array[$item->value] = $item->name;
        }

        return $array;
    }

    public static function getResponsesForValidation(): array
    {
        $array = [];
        $set   = WebhookResponse::cases();
        foreach ($set as $item) {
            $array[$item->name]  = $item->value;
            $array[$item->value] = $item->value;
        }

        return $array;
    }

    public static function getTriggers(): array
    {
        $array = [];
        $set   = WebhookTrigger::cases();
        foreach ($set as $item) {
            $array[$item->value] = $item->name;
        }

        return $array;
    }

    public static function getTriggersForValidation(): array
    {
        $array = [];
        $set   = WebhookTrigger::cases();
        foreach ($set as $item) {
            $array[$item->name]  = $item->value;
            $array[$item->value] = $item->value;
        }

        return $array;
    }

    /**
     * Route binder. Converts the key in the URL to the specified object (or throw 404).
     *
     * @throws NotFoundHttpException
     */
    public static function routeBinder(string $value): self
    {
        if (auth()->check()) {
            $webhookId = (int)$value;

            /** @var User $user */
            $user = auth()->user();

            /** @var null|Webhook $webhook */
            $webhook = $user->webhooks()->find($webhookId);
            if (null !== $webhook) {
                return $webhook;
            }
        }

        throw new NotFoundHttpException();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function webhookMessages(): HasMany
    {
        return $this->hasMany(WebhookMessage::class);
    }
}
