<?php

/*
 * PiggyBankEventHandler.php
 * Copyright (c) 2023 james@firefly-iii.org
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

namespace FireflyIII\Handlers\Events\Model;

use FireflyIII\Events\Model\PiggyBank\ChangedAmount;
use FireflyIII\Models\PiggyBankEvent;

/**
 * Class PiggyBankEventHandler
 */
class PiggyBankEventHandler
{
    public function changePiggyAmount(ChangedAmount $event): void
    {
        // find journal if group is present.
        $journal = $event->transactionJournal;
        if (null !== $event->transactionGroup) {
            $journal = $event->transactionGroup->transactionJournals()->first();
        }
        $date = $journal?->date ?? today(config('app.timezone'));
        // sanity check: event must not already exist for this journal and piggy bank.
        if (null !== $journal) {
            $exists = PiggyBankEvent::where('piggy_bank_id', $event->piggyBank->id)
                ->where('transaction_journal_id', $journal->id)
                ->exists()
            ;
            if ($exists) {
                app('log')->warning('Already have event for this journal and piggy, will not create another.');

                return;
            }
        }

        PiggyBankEvent::create(
            [
                'piggy_bank_id'          => $event->piggyBank->id,
                'transaction_journal_id' => $journal?->id,
                'date'                   => $date->format('Y-m-d'),
                'amount'                 => $event->amount,
            ]
        );
    }
}
