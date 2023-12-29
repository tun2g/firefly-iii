<?php

/*
 * UserLogin.php
 * Copyright (c) 2022 james@firefly-iii.org
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

namespace FireflyIII\Notifications\User;

use FireflyIII\Exceptions\FireflyException;
use FireflyIII\Support\Notifications\UrlValidator;
use FireflyIII\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

/**
 * Class UserLogin
 */
class UserLogin extends Notification
{
    use Queueable;

    private string $ip;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($notifiable)
    {
        return [
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return MailMessage
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toMail($notifiable)
    {
        $time = now(config('app.timezone'))->isoFormat((string)trans('config.date_time_js'));
        $host = '';

        try {
            $hostName = app('steam')->getHostName($this->ip);
        } catch (FireflyException $e) {
            app('log')->error($e->getMessage());
            $hostName = $this->ip;
        }
        if ($hostName !== $this->ip) {
            $host = $hostName;
        }

        return (new MailMessage())
            ->markdown('emails.new-ip', ['time' => $time, 'ipAddress' => $this->ip, 'host' => $host])
            ->subject((string)trans('email.login_from_new_ip'))
        ;
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return SlackMessage
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toSlack($notifiable)
    {
        $host = '';

        try {
            $hostName = app('steam')->getHostName($this->ip);
        } catch (FireflyException $e) {
            app('log')->error($e->getMessage());
            $hostName = $this->ip;
        }
        if ($hostName !== $this->ip) {
            $host = $hostName;
        }

        return (new SlackMessage())->content((string)trans('email.slack_login_from_new_ip', ['host' => $host, 'ip' => $this->ip]));
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function via($notifiable)
    {
        /** @var null|User $user */
        $user     = auth()->user();
        $slackUrl = null === $user ? '' : app('preferences')->getForUser(auth()->user(), 'slack_webhook_url', '')->data;
        if (is_array($slackUrl)) {
            $slackUrl = '';
        }
        if (UrlValidator::isValidWebhookURL((string)$slackUrl)) {
            return ['mail', 'slack'];
        }

        return ['mail'];
    }
}
