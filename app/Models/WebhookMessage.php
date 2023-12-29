<?php
/*
 * WebhookMessage.php
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
use FireflyIII\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * FireflyIII\Models\WebhookMessage
 *
 * @property int         $id
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|string $deleted_at
 * @property int         $webhook_id
 * @property bool        $sent
 * @property bool        $errored
 * @property int         $attempts
 * @property string      $uuid
 * @property array       $message
 * @property null|array  $logs
 * @property Webhook     $webhook
 *
 * @method static Builder|WebhookMessage newModelQuery()
 * @method static Builder|WebhookMessage newQuery()
 * @method static Builder|WebhookMessage query()
 * @method static Builder|WebhookMessage whereAttempts($value)
 * @method static Builder|WebhookMessage whereCreatedAt($value)
 * @method static Builder|WebhookMessage whereDeletedAt($value)
 * @method static Builder|WebhookMessage whereErrored($value)
 * @method static Builder|WebhookMessage whereId($value)
 * @method static Builder|WebhookMessage whereLogs($value)
 * @method static Builder|WebhookMessage whereMessage($value)
 * @method static Builder|WebhookMessage whereSent($value)
 * @method static Builder|WebhookMessage whereUpdatedAt($value)
 * @method static Builder|WebhookMessage whereUuid($value)
 * @method static Builder|WebhookMessage whereWebhookId($value)
 *
 * @property Collection|WebhookAttempt[] $webhookAttempts
 * @property null|int                    $webhook_attempts_count
 *
 * @mixin Eloquent
 */
class WebhookMessage extends Model
{
    use ReturnsIntegerIdTrait;

    protected $casts
        = [
            'sent'    => 'boolean',
            'errored' => 'boolean',
            'uuid'    => 'string',
            'message' => 'json',
            'logs'    => 'json',
        ];

    /**
     * Route binder. Converts the key in the URL to the specified object (or throw 404).
     *
     * @throws NotFoundHttpException
     */
    public static function routeBinder(string $value): self
    {
        if (auth()->check()) {
            $messageId = (int)$value;

            /** @var User $user */
            $user = auth()->user();

            /** @var null|WebhookMessage $message */
            $message = self::find($messageId);
            if (null !== $message && $message->webhook->user_id === $user->id) {
                return $message;
            }
        }

        throw new NotFoundHttpException();
    }

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }

    public function webhookAttempts(): HasMany
    {
        return $this->hasMany(WebhookAttempt::class);
    }

    /**
     * Get the amount
     */
    protected function sent(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => (bool)$value,
        );
    }

    protected function webhookId(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => (int)$value,
        );
    }
}
