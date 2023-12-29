<?php

/**
 * AdminEventHandler.php
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

namespace FireflyIII\Handlers\Events;

use FireflyIII\Events\Admin\InvitationCreated;
use FireflyIII\Events\AdminRequestedTestMessage;
use FireflyIII\Events\NewVersionAvailable;
use FireflyIII\Notifications\Admin\TestNotification;
use FireflyIII\Notifications\Admin\UserInvitation;
use FireflyIII\Notifications\Admin\VersionCheckResult;
use FireflyIII\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Notification;

/**
 * Class AdminEventHandler.
 */
class AdminEventHandler
{
    public function sendInvitationNotification(InvitationCreated $event): void
    {
        $sendMail = app('fireflyconfig')->get('notification_invite_created', true)->data;
        if (false === $sendMail) {
            return;
        }

        /** @var UserRepositoryInterface $repository */
        $repository = app(UserRepositoryInterface::class);
        $all        = $repository->all();
        foreach ($all as $user) {
            if ($repository->hasRole($user, 'owner')) {
                try {
                    Notification::send($user, new UserInvitation($event->invitee));
                } catch (\Exception $e) { // @phpstan-ignore-line
                    $message = $e->getMessage();
                    if (str_contains($message, 'Bcc')) {
                        app('log')->warning('[Bcc] Could not send notification. Please validate your email settings, use the .env.example file as a guide.');

                        return;
                    }
                    if (str_contains($message, 'RFC 2822')) {
                        app('log')->warning('[RFC] Could not send notification. Please validate your email settings, use the .env.example file as a guide.');

                        return;
                    }
                    app('log')->error($e->getMessage());
                    app('log')->error($e->getTraceAsString());
                }
            }
        }
    }

    /**
     * Send new version message to admin.
     */
    public function sendNewVersion(NewVersionAvailable $event): void
    {
        $sendMail = app('fireflyconfig')->get('notification_new_version', true)->data;
        if (false === $sendMail) {
            return;
        }

        /** @var UserRepositoryInterface $repository */
        $repository = app(UserRepositoryInterface::class);
        $all        = $repository->all();
        foreach ($all as $user) {
            if ($repository->hasRole($user, 'owner')) {
                try {
                    Notification::send($user, new VersionCheckResult($event->message));
                } catch (\Exception $e) {// @phpstan-ignore-line
                    $message = $e->getMessage();
                    if (str_contains($message, 'Bcc')) {
                        app('log')->warning('[Bcc] Could not send notification. Please validate your email settings, use the .env.example file as a guide.');

                        return;
                    }
                    if (str_contains($message, 'RFC 2822')) {
                        app('log')->warning('[RFC] Could not send notification. Please validate your email settings, use the .env.example file as a guide.');

                        return;
                    }
                    app('log')->error($e->getMessage());
                    app('log')->error($e->getTraceAsString());
                }
            }
        }
    }

    /**
     * Sends a test message to an administrator.
     */
    public function sendTestMessage(AdminRequestedTestMessage $event): void
    {
        /** @var UserRepositoryInterface $repository */
        $repository = app(UserRepositoryInterface::class);

        if (!$repository->hasRole($event->user, 'owner')) {
            return;
        }

        try {
            Notification::send($event->user, new TestNotification($event->user->email));
        } catch (\Exception $e) { // @phpstan-ignore-line
            $message = $e->getMessage();
            if (str_contains($message, 'Bcc')) {
                app('log')->warning('[Bcc] Could not send notification. Please validate your email settings, use the .env.example file as a guide.');

                return;
            }
            if (str_contains($message, 'RFC 2822')) {
                app('log')->warning('[RFC] Could not send notification. Please validate your email settings, use the .env.example file as a guide.');

                return;
            }
            app('log')->error($e->getMessage());
            app('log')->error($e->getTraceAsString());
        }
    }
}
