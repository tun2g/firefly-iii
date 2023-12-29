<?php

/**
 * Preferences.php
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

namespace FireflyIII\Support;

use FireflyIII\Exceptions\FireflyException;
use FireflyIII\Models\Preference;
use FireflyIII\User;
use Illuminate\Support\Collection;

/**
 * Class Preferences.
 */
class Preferences
{
    public function all(): Collection
    {
        $user = auth()->user();
        if (null === $user) {
            return new Collection();
        }

        return Preference::where('user_id', $user->id)->get();
    }

    /**
     * @param mixed $default
     *
     * @throws FireflyException
     */
    public function get(string $name, $default = null): ?Preference
    {
        if ('currencyPreference' === $name) {
            throw new FireflyException('No longer supports "currencyPreference", please refactor me.');
        }

        /** @var null|User $user */
        $user = auth()->user();
        if (null === $user) {
            $preference       = new Preference();
            $preference->data = $default;

            return $preference;
        }

        return $this->getForUser($user, $name, $default);
    }

    /**
     * @throws FireflyException
     */
    public function getForUser(User $user, string $name, null|array|bool|int|string $default = null): ?Preference
    {
        if ('currencyPreference' === $name) {
            throw new FireflyException('No longer supports "currencyPreference", please refactor me.');
        }
        $preference = Preference::where('user_id', $user->id)->where('name', $name)->first(['id', 'user_id', 'name', 'data', 'updated_at', 'created_at']);
        if (null !== $preference && null === $preference->data) {
            $preference->delete();
            $preference = null;
        }

        if (null !== $preference) {
            return $preference;
        }
        // no preference found and default is null:
        if (null === $default) {
            // return NULL
            return null;
        }

        return $this->setForUser($user, $name, $default);
    }

    /**
     * @throws FireflyException
     */
    public function delete(string $name): bool
    {
        if ('currencyPreference' === $name) {
            throw new FireflyException('No longer supports "currencyPreference", please refactor me.');
        }
        $fullName = sprintf('preference%s%s', auth()->user()->id, $name);
        if (\Cache::has($fullName)) {
            \Cache::forget($fullName);
        }
        Preference::where('user_id', auth()->user()->id)->where('name', $name)->delete();

        return true;
    }

    public function forget(User $user, string $name): void
    {
        if ('currencyPreference' === $name) {
            throw new FireflyException('No longer supports "currencyPreference", please refactor me.');
        }
        $key = sprintf('preference%s%s', $user->id, $name);
        \Cache::forget($key);
        \Cache::put($key, '', 5);
    }

    /**
     * @param mixed $value
     *
     * @throws FireflyException
     */
    public function setForUser(User $user, string $name, $value): Preference
    {
        if ('currencyPreference' === $name) {
            throw new FireflyException('No longer supports "currencyPreference", please refactor me.');
        }
        $fullName = sprintf('preference%s%s', $user->id, $name);
        \Cache::forget($fullName);

        /** @var null|Preference $pref */
        $pref = Preference::where('user_id', $user->id)->where('name', $name)->first(['id', 'name', 'data', 'updated_at', 'created_at']);

        if (null !== $pref && null === $value) {
            $pref->delete();

            return new Preference();
        }
        if (null === $value) {
            return new Preference();
        }
        if (null === $pref) {
            $pref          = new Preference();
            $pref->user_id = (int)$user->id;
            $pref->name    = $name;
        }
        $pref->data = $value;

        try {
            $pref->save();
        } catch (\PDOException $e) {
            throw new FireflyException(sprintf('Could not save preference: %s', $e->getMessage()), 0, $e);
        }
        \Cache::forever($fullName, $pref);

        return $pref;
    }

    public function beginsWith(User $user, string $search): Collection
    {
        return Preference::where('user_id', $user->id)->where('name', 'LIKE', $search.'%')->get();
    }

    public function findByName(string $name): Collection
    {
        if ('currencyPreference' === $name) {
            throw new FireflyException('No longer supports "currencyPreference", please refactor me.');
        }

        return Preference::where('name', $name)->get();
    }

    public function getArrayForUser(User $user, array $list): array
    {
        $result      = [];
        $preferences = Preference::where('user_id', $user->id)->whereIn('name', $list)->get(['id', 'name', 'data']);

        /** @var Preference $preference */
        foreach ($preferences as $preference) {
            $result[$preference->name] = $preference->data;
        }
        foreach ($list as $name) {
            if (!array_key_exists($name, $result)) {
                $result[$name] = null;
            }
        }

        return $result;
    }

    /**
     * @param mixed $default
     *
     * @throws FireflyException
     */
    public function getFresh(string $name, $default = null): ?Preference
    {
        if ('currencyPreference' === $name) {
            throw new FireflyException('No longer supports "currencyPreference", please refactor me.');
        }

        /** @var null|User $user */
        $user = auth()->user();
        if (null === $user) {
            $preference       = new Preference();
            $preference->data = $default;

            return $preference;
        }

        return $this->getFreshForUser($user, $name, $default);
    }

    /**
     * @param null $default
     *
     * @return null|preference
     *                         TODO remove me
     *
     * @throws FireflyException
     */
    public function getFreshForUser(User $user, string $name, $default = null): ?Preference
    {
        if ('currencyPreference' === $name) {
            throw new FireflyException('No longer supports "currencyPreference", please refactor me.');
        }

        return $this->getForUser($user, $name, $default);
    }

    /**
     * @throws FireflyException
     */
    public function lastActivity(): string
    {
        $lastActivity = microtime();
        $preference   = $this->get('lastActivity', microtime());

        if (null !== $preference && null !== $preference->data) {
            $lastActivity = $preference->data;
        }
        if (is_array($lastActivity)) {
            $lastActivity = implode(',', $lastActivity);
        }

        return hash('sha256', (string)$lastActivity);
    }

    public function mark(): void
    {
        $this->set('lastActivity', microtime());
        \Session::forget('first');
    }

    /**
     * @param mixed $value
     *
     * @throws FireflyException
     */
    public function set(string $name, $value): Preference
    {
        if ('currencyPreference' === $name) {
            throw new FireflyException('No longer supports "currencyPreference", please refactor me.');
        }
        $user = auth()->user();
        if (null === $user) {
            // make new preference, return it:
            $pref       = new Preference();
            $pref->name = $name;
            $pref->data = $value;

            return $pref;
        }

        return $this->setForUser(auth()->user(), $name, $value);
    }
}
