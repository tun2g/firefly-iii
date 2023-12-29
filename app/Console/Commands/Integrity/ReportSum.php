<?php
/**
 * ReportSum.php
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

namespace FireflyIII\Console\Commands\Integrity;

use FireflyIII\Console\Commands\ShowsFriendlyMessages;
use FireflyIII\Repositories\User\UserRepositoryInterface;
use FireflyIII\User;
use Illuminate\Console\Command;

/**
 * Class ReportSkeleton
 */
class ReportSum extends Command
{
    use ShowsFriendlyMessages;

    protected $description = 'Report on the total sum of transactions. Must be 0.';
    protected $signature   = 'firefly-iii:report-sum';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->reportSum();

        return 0;
    }

    /**
     * Reports for each user when the sum of their transactions is not zero.
     */
    private function reportSum(): void
    {
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = app(UserRepositoryInterface::class);

        /** @var User $user */
        foreach ($userRepository->all() as $user) {
            $sum = (string)$user->transactions()->sum('amount');
            if (!is_numeric($sum)) {
                $message = sprintf('Error: Transactions for user #%d (%s) have an invalid sum ("%s").', $user->id, $user->email, $sum);
                $this->friendlyError($message);

                continue;
            }
            if (0 !== bccomp($sum, '0')) {
                $message = sprintf('Error: Transactions for user #%d (%s) are off by %s!', $user->id, $user->email, $sum);
                $this->friendlyError($message);
            }
            if (0 === bccomp($sum, '0')) {
                $this->friendlyPositive(sprintf('Amount integrity OK for user #%d', $user->id));
            }
        }
    }
}
