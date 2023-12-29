<?php

/**
 * PiggyBankRepository.php
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

namespace FireflyIII\Repositories\PiggyBank;

use Carbon\Carbon;
use FireflyIII\Exceptions\FireflyException;
use FireflyIII\Models\Attachment;
use FireflyIII\Models\Note;
use FireflyIII\Models\PiggyBank;
use FireflyIII\Models\PiggyBankRepetition;
use FireflyIII\Models\Transaction;
use FireflyIII\Models\TransactionJournal;
use FireflyIII\Repositories\Account\AccountRepositoryInterface;
use FireflyIII\Repositories\Journal\JournalRepositoryInterface;
use FireflyIII\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

/**
 * Class PiggyBankRepository.
 */
class PiggyBankRepository implements PiggyBankRepositoryInterface
{
    use ModifiesPiggyBanks;

    private User $user;

    public function destroyAll(): void
    {
        $this->user->piggyBanks()->delete();
    }

    public function findPiggyBank(?int $piggyBankId, ?string $piggyBankName): ?PiggyBank
    {
        app('log')->debug('Searching for piggy information.');

        if (null !== $piggyBankId) {
            $searchResult = $this->find($piggyBankId);
            if (null !== $searchResult) {
                app('log')->debug(sprintf('Found piggy based on #%d, will return it.', $piggyBankId));

                return $searchResult;
            }
        }
        if (null !== $piggyBankName) {
            $searchResult = $this->findByName($piggyBankName);
            if (null !== $searchResult) {
                app('log')->debug(sprintf('Found piggy based on "%s", will return it.', $piggyBankName));

                return $searchResult;
            }
        }
        app('log')->debug('Found nothing');

        return null;
    }

    public function find(int $piggyBankId): ?PiggyBank
    {
        // phpstan doesn't get the Model.
        return $this->user->piggyBanks()->find($piggyBankId); // @phpstan-ignore-line
    }

    /**
     * Find by name or return NULL.
     */
    public function findByName(string $name): ?PiggyBank
    {
        return $this->user->piggyBanks()->where('piggy_banks.name', $name)->first(['piggy_banks.*']);
    }

    public function getAttachments(PiggyBank $piggyBank): Collection
    {
        $set = $piggyBank->attachments()->get();

        /** @var \Storage $disk */
        $disk = \Storage::disk('upload');

        return $set->each(
            static function (Attachment $attachment) use ($disk) {
                $notes                   = $attachment->notes()->first();
                $attachment->file_exists = $disk->exists($attachment->fileName());
                $attachment->notes_text  = null !== $notes ? $notes->text : '';

                return $attachment;
            }
        );
    }

    /**
     * Get current amount saved in piggy bank.
     */
    public function getCurrentAmount(PiggyBank $piggyBank): string
    {
        $rep = $this->getRepetition($piggyBank);
        if (null === $rep) {
            return '0';
        }

        return $rep->currentamount;
    }

    public function getRepetition(PiggyBank $piggyBank): ?PiggyBankRepetition
    {
        return $piggyBank->piggyBankRepetitions()->first();
    }

    public function getEvents(PiggyBank $piggyBank): Collection
    {
        return $piggyBank->piggyBankEvents()->orderBy('date', 'DESC')->orderBy('id', 'DESC')->get();
    }

    /**
     * Used for connecting to a piggy bank.
     *
     * @throws FireflyException
     */
    public function getExactAmount(PiggyBank $piggyBank, PiggyBankRepetition $repetition, TransactionJournal $journal): string
    {
        app('log')->debug(sprintf('Now in getExactAmount(%d, %d, %d)', $piggyBank->id, $repetition->id, $journal->id));

        $operator = null;
        $currency = null;

        /** @var JournalRepositoryInterface $journalRepost */
        $journalRepost = app(JournalRepositoryInterface::class);
        $journalRepost->setUser($this->user);

        /** @var AccountRepositoryInterface $accountRepos */
        $accountRepos = app(AccountRepositoryInterface::class);
        $accountRepos->setUser($this->user);

        $defaultCurrency   = app('amount')->getDefaultCurrencyByUserGroup($this->user->userGroup);
        $piggyBankCurrency = $accountRepos->getAccountCurrency($piggyBank->account) ?? $defaultCurrency;

        app('log')->debug(sprintf('Piggy bank #%d currency is %s', $piggyBank->id, $piggyBankCurrency->code));

        /** @var Transaction $source */
        $source = $journal->transactions()->with(['account'])->where('amount', '<', 0)->first();

        /** @var Transaction $destination */
        $destination = $journal->transactions()->with(['account'])->where('amount', '>', 0)->first();

        // matches source, which means amount will be removed from piggy:
        if ($source->account_id === $piggyBank->account_id) {
            $operator = 'negative';
            $currency = $accountRepos->getAccountCurrency($source->account) ?? $defaultCurrency;
            app('log')->debug(sprintf('Currency will draw money out of piggy bank. Source currency is %s', $currency->code));
        }

        // matches destination, which means amount will be added to piggy.
        if ($destination->account_id === $piggyBank->account_id) {
            $operator = 'positive';
            $currency = $accountRepos->getAccountCurrency($destination->account) ?? $defaultCurrency;
            app('log')->debug(sprintf('Currency will add money to piggy bank. Destination currency is %s', $currency->code));
        }
        if (null === $operator || null === $currency) {
            app('log')->debug('Currency is NULL and operator is NULL, return "0".');

            return '0';
        }
        // currency of the account + the piggy bank currency are almost the same.
        // which amount from the transaction matches?
        $amount = null;
        if ((int)$source->transaction_currency_id === $currency->id) {
            app('log')->debug('Use normal amount');
            $amount = app('steam')->{$operator}($source->amount); // @phpstan-ignore-line
        }
        if ((int)$source->foreign_currency_id === $currency->id) {
            app('log')->debug('Use foreign amount');
            $amount = app('steam')->{$operator}($source->foreign_amount); // @phpstan-ignore-line
        }
        if (null === $amount) {
            app('log')->debug('No match on currency, so amount remains null, return "0".');

            return '0';
        }

        app('log')->debug(sprintf('The currency is %s and the amount is %s', $currency->code, $amount));
        $room    = bcsub($piggyBank->targetamount, $repetition->currentamount);
        $compare = bcmul($repetition->currentamount, '-1');

        if (0 === bccomp($piggyBank->targetamount, '0')) {
            // amount is zero? then the "room" is positive amount of we wish to add or remove.
            $room = app('steam')->positive($amount);
            app('log')->debug(sprintf('Room is now %s', $room));
        }

        app('log')->debug(sprintf('Will add/remove %f to piggy bank #%d ("%s")', $amount, $piggyBank->id, $piggyBank->name));

        // if the amount is positive, make sure it fits in piggy bank:
        if (1 === bccomp($amount, '0') && -1 === bccomp($room, $amount)) {
            // amount is positive and $room is smaller than $amount
            app('log')->debug(sprintf('Room in piggy bank for extra money is %f', $room));
            app('log')->debug(sprintf('There is NO room to add %f to piggy bank #%d ("%s")', $amount, $piggyBank->id, $piggyBank->name));
            app('log')->debug(sprintf('New amount is %f', $room));

            return $room;
        }

        // amount is negative and $currentamount is smaller than $amount
        if (-1 === bccomp($amount, '0') && 1 === bccomp($compare, $amount)) {
            app('log')->debug(sprintf('Max amount to remove is %f', $repetition->currentamount));
            app('log')->debug(sprintf('Cannot remove %f from piggy bank #%d ("%s")', $amount, $piggyBank->id, $piggyBank->name));
            app('log')->debug(sprintf('New amount is %f', $compare));

            return $compare;
        }

        return (string)$amount;
    }

    public function setUser(null|Authenticatable|User $user): void
    {
        if ($user instanceof User) {
            $this->user = $user;
        }
    }

    public function getMaxOrder(): int
    {
        return (int)$this->user->piggyBanks()->max('piggy_banks.order');
    }

    /**
     * Return note for piggy bank.
     */
    public function getNoteText(PiggyBank $piggyBank): string
    {
        /** @var null|Note $note */
        $note = $piggyBank->notes()->first();

        return (string)$note?->text;
    }

    /**
     * Also add amount in name.
     */
    public function getPiggyBanksWithAmount(): Collection
    {
        $currency = app('amount')->getDefaultCurrency();

        $set = $this->getPiggyBanks();

        /** @var PiggyBank $piggy */
        foreach ($set as $piggy) {
            $currentAmount = $this->getRepetition($piggy)->currentamount ?? '0';
            $piggy->name   = $piggy->name.' ('.app('amount')->formatAnything($currency, $currentAmount, false).')';
        }

        return $set;
    }

    public function getPiggyBanks(): Collection
    {
        return $this->user  // @phpstan-ignore-line (phpstan does not recognize objectGroups)
            ->piggyBanks()
            ->with(
                [
                    'account',
                    'objectGroups',
                ]
            )
            ->orderBy('order', 'ASC')->get()
        ;
    }

    /**
     * Returns the suggested amount the user should save per month, or "".
     */
    public function getSuggestedMonthlyAmount(PiggyBank $piggyBank): string
    {
        $savePerMonth = '0';
        $repetition   = $this->getRepetition($piggyBank);
        if (null === $repetition) {
            return $savePerMonth;
        }
        if (null !== $piggyBank->targetdate && $repetition->currentamount < $piggyBank->targetamount) {
            $now             = today(config('app.timezone'));
            $startDate       = null !== $piggyBank->startdate && $piggyBank->startdate->gte($now) ? $piggyBank->startdate : $now;
            $diffInMonths    = $startDate->diffInMonths($piggyBank->targetdate, false);
            $remainingAmount = bcsub($piggyBank->targetamount, $repetition->currentamount);

            // more than 1 month to go and still need money to save:
            if ($diffInMonths > 0 && 1 === bccomp($remainingAmount, '0')) {
                $savePerMonth = bcdiv($remainingAmount, (string)$diffInMonths);
            }

            // less than 1 month to go but still need money to save:
            if (0 === $diffInMonths && 1 === bccomp($remainingAmount, '0')) {
                $savePerMonth = $remainingAmount;
            }
        }

        return $savePerMonth;
    }

    /**
     * Get for piggy account what is left to put in piggies.
     */
    public function leftOnAccount(PiggyBank $piggyBank, Carbon $date): string
    {
        $balance = app('steam')->balanceIgnoreVirtual($piggyBank->account, $date);

        /** @var Collection $piggies */
        $piggies = $piggyBank->account->piggyBanks;

        /** @var PiggyBank $current */
        foreach ($piggies as $current) {
            $repetition = $this->getRepetition($current);
            if (null !== $repetition) {
                $balance = bcsub($balance, $repetition->currentamount);
            }
        }

        return $balance;
    }

    public function searchPiggyBank(string $query, int $limit): Collection
    {
        $search = $this->user->piggyBanks();
        if ('' !== $query) {
            $search->where('piggy_banks.name', 'LIKE', sprintf('%%%s%%', $query));
        }
        $search->orderBy('piggy_banks.order', 'ASC')
            ->orderBy('piggy_banks.name', 'ASC')
        ;

        return $search->take($limit)->get();
    }
}
