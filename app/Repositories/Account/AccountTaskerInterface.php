<?php

/**
 * AccountTaskerInterface.php
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

namespace FireflyIII\Repositories\Account;

use Carbon\Carbon;
use FireflyIII\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

/**
 * Interface AccountTaskerInterface.
 */
interface AccountTaskerInterface
{
    public function getAccountReport(Collection $accounts, Carbon $start, Carbon $end): array;

    public function getExpenseReport(Carbon $start, Carbon $end, Collection $accounts): array;

    public function getIncomeReport(Carbon $start, Carbon $end, Collection $accounts): array;

    public function setUser(null|Authenticatable|User $user): void;
}
