<?php
/**
 * UniqueAccountNumber.php
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

namespace FireflyIII\Rules;

use FireflyIII\Models\Account;
use FireflyIII\Models\AccountMeta;
use FireflyIII\Models\AccountType;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class UniqueAccountNumber
 */
class UniqueAccountNumber implements ValidationRule
{
    private ?Account $account;
    private ?string  $expectedType;

    /**
     * Create a new rule instance.
     */
    public function __construct(?Account $account, ?string $expectedType)
    {
        app('log')->debug('Constructed UniqueAccountNumber');
        $this->account      = $account;
        $this->expectedType = $expectedType;
        // a very basic fix to make sure we get the correct account type:
        if ('expense' === $expectedType) {
            $this->expectedType = AccountType::EXPENSE;
        }
        if ('revenue' === $expectedType) {
            $this->expectedType = AccountType::REVENUE;
        }
        if ('asset' === $expectedType) {
            $this->expectedType = AccountType::ASSET;
        }
        app('log')->debug(sprintf('Expected type is "%s"', $this->expectedType));
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return (string)trans('validation.unique_account_number_for_user');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!auth()->check()) {
            return;
        }
        if (null === $this->expectedType) {
            return;
        }
        $maxCounts = $this->getMaxOccurrences();

        foreach ($maxCounts as $type => $max) {
            $count = $this->countHits($type, $value);
            app('log')->debug(sprintf('Count for "%s" and account number "%s" is %d', $type, $value, $count));
            if ($count > $max) {
                app('log')->debug(
                    sprintf(
                        'account number "%s" is in use with %d account(s) of type "%s", which is too much for expected type "%s"',
                        $value,
                        $count,
                        $type,
                        $this->expectedType
                    )
                );

                $fail('validation.unique_account_number_for_user')->translate();

                return;
            }
        }
        app('log')->debug('Account number is valid.');
    }

    private function getMaxOccurrences(): array
    {
        $maxCounts = [
            AccountType::ASSET   => 0,
            AccountType::EXPENSE => 0,
            AccountType::REVENUE => 0,
        ];

        if ('expense' === $this->expectedType || AccountType::EXPENSE === $this->expectedType) {
            // IBAN should be unique amongst expense and asset accounts.
            // may appear once in revenue accounts
            $maxCounts[AccountType::REVENUE] = 1;
        }
        if ('revenue' === $this->expectedType || AccountType::REVENUE === $this->expectedType) {
            // IBAN should be unique amongst revenue and asset accounts.
            // may appear once in expense accounts
            $maxCounts[AccountType::EXPENSE] = 1;
        }

        return $maxCounts;
    }

    private function countHits(string $type, string $accountNumber): int
    {
        $query = AccountMeta::leftJoin('accounts', 'accounts.id', '=', 'account_meta.account_id')
            ->leftJoin('account_types', 'account_types.id', '=', 'accounts.account_type_id')
            ->where('accounts.user_id', auth()->user()->id)
            ->where('account_types.type', $type)
            ->where('account_meta.name', '=', 'account_number')
            ->where('account_meta.data', json_encode($accountNumber))
        ;

        if (null !== $this->account) {
            $query->where('accounts.id', '!=', $this->account->id);
        }

        return $query->count();
    }
}
