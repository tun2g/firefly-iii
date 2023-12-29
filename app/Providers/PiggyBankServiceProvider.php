<?php

/**
 * PiggyBankServiceProvider.php
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

namespace FireflyIII\Providers;

use FireflyIII\Repositories\PiggyBank\PiggyBankRepository;
use FireflyIII\Repositories\PiggyBank\PiggyBankRepositoryInterface;
use FireflyIII\Repositories\UserGroups\PiggyBank\PiggyBankRepository as AdminPiggyBankRepository;
use FireflyIII\Repositories\UserGroups\PiggyBank\PiggyBankRepositoryInterface as AdminPiggyBankRepositoryInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * Class PiggyBankServiceProvider.
 */
class PiggyBankServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void {}

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PiggyBankRepositoryInterface::class,
            static function (Application $app) {
                /** @var PiggyBankRepository $repository */
                $repository = app(PiggyBankRepository::class);
                if ($app->auth->check()) { // @phpstan-ignore-line (phpstan does not understand the reference to auth)
                    $repository->setUser(auth()->user());
                }

                return $repository;
            }
        );

        $this->app->bind(
            AdminPiggyBankRepositoryInterface::class,
            static function (Application $app) {
                /** @var AdminPiggyBankRepository $repository */
                $repository = app(AdminPiggyBankRepository::class);
                if ($app->auth->check()) { // @phpstan-ignore-line (phpstan does not understand the reference to auth)
                    $repository->setUser(auth()->user());
                }

                return $repository;
            }
        );
    }
}
