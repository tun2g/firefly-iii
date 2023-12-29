<?php
/**
 * AccountValidator.php
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

namespace FireflyIII\Validation;

use FireflyIII\Models\Account;
use FireflyIII\Models\AccountType;
use FireflyIII\Models\TransactionType;
use FireflyIII\Models\UserGroup;
use FireflyIII\Repositories\Account\AccountRepositoryInterface;
use FireflyIII\Repositories\UserGroups\Account\AccountRepositoryInterface as UserGroupAccountRepositoryInterface;
use FireflyIII\User;
use FireflyIII\Validation\Account\DepositValidation;
use FireflyIII\Validation\Account\LiabilityValidation;
use FireflyIII\Validation\Account\OBValidation;
use FireflyIII\Validation\Account\ReconciliationValidation;
use FireflyIII\Validation\Account\TransferValidation;
use FireflyIII\Validation\Account\WithdrawalValidation;

/**
 * Class AccountValidator
 */
class AccountValidator
{
    use DepositValidation;
    use LiabilityValidation;
    use OBValidation;
    use ReconciliationValidation;
    use TransferValidation;
    use WithdrawalValidation;

    public bool                                 $createMode;
    public string                               $destError;
    public ?Account                             $destination;
    public ?Account                             $source;
    public string                               $sourceError;
    private AccountRepositoryInterface          $accountRepository;
    private array                               $combinations;
    private string                              $transactionType;
    private bool                                $useUserGroupRepository = false;
    private UserGroupAccountRepositoryInterface $userGroupAccountRepository;

    /**
     * AccountValidator constructor.
     */
    public function __construct()
    {
        $this->createMode                 = false;
        $this->destError                  = 'No error yet.';
        $this->sourceError                = 'No error yet.';
        $this->combinations               = config('firefly.source_dests');
        $this->source                     = null;
        $this->destination                = null;
        $this->accountRepository          = app(AccountRepositoryInterface::class);
        $this->userGroupAccountRepository = app(UserGroupAccountRepositoryInterface::class);
    }

    public function getSource(): ?Account
    {
        return $this->source;
    }

    public function setSource(?Account $account): void
    {
        if (null === $account) {
            app('log')->debug('AccountValidator source is set to NULL');
        }
        if (null !== $account) {
            app('log')->debug(sprintf('AccountValidator source is set to #%d: "%s" (%s)', $account->id, $account->name, $account->accountType->type));
        }
        $this->source = $account;
    }

    public function setDestination(?Account $account): void
    {
        if (null === $account) {
            app('log')->debug('AccountValidator destination is set to NULL');
        }
        if (null !== $account) {
            app('log')->debug(sprintf('AccountValidator destination is set to #%d: "%s" (%s)', $account->id, $account->name, $account->accountType->type));
        }
        $this->destination = $account;
    }

    public function setTransactionType(string $transactionType): void
    {
        app('log')->debug(sprintf('Transaction type for validator is now "%s".', ucfirst($transactionType)));
        $this->transactionType = ucfirst($transactionType);
    }

    public function setUser(User $user): void
    {
        $this->accountRepository->setUser($user);
        $this->useUserGroupRepository = false;
    }

    public function setUserGroup(UserGroup $userGroup): void
    {
        $this->userGroupAccountRepository->setUserGroup($userGroup);
        $this->useUserGroupRepository = true;
    }

    public function validateDestination(array $array): bool
    {
        app('log')->debug('Now in AccountValidator::validateDestination()', $array);
        if (null === $this->source) {
            app('log')->error('Source is NULL, always FALSE.');
            $this->destError = 'No source account validation has taken place yet. Please do this first or overrule the object.';

            return false;
        }

        switch ($this->transactionType) {
            default:
                $this->destError = sprintf('AccountValidator::validateDestination cannot handle "%s", so it will always return false.', $this->transactionType);
                app('log')->error(sprintf('AccountValidator::validateDestination cannot handle "%s", so it will always return false.', $this->transactionType));

                $result = false;

                break;

            case TransactionType::WITHDRAWAL:
                $result = $this->validateWithdrawalDestination($array);

                break;

            case TransactionType::DEPOSIT:
                $result = $this->validateDepositDestination($array);

                break;

            case TransactionType::TRANSFER:
                $result = $this->validateTransferDestination($array);

                break;

            case TransactionType::OPENING_BALANCE:
                $result = $this->validateOBDestination($array);

                break;

            case TransactionType::LIABILITY_CREDIT:
                $result = $this->validateLCDestination($array);

                break;

            case TransactionType::RECONCILIATION:
                $result = $this->validateReconciliationDestination($array);

                break;
        }

        return $result;
    }

    public function validateSource(array $array): bool
    {
        app('log')->debug('Now in AccountValidator::validateSource()', $array);

        switch ($this->transactionType) {
            default:
                app('log')->error(sprintf('AccountValidator::validateSource cannot handle "%s", so it will do a generic check.', $this->transactionType));
                $result = $this->validateGenericSource($array);

                break;

            case TransactionType::WITHDRAWAL:
                $result = $this->validateWithdrawalSource($array);

                break;

            case TransactionType::DEPOSIT:
                $result = $this->validateDepositSource($array);

                break;

            case TransactionType::TRANSFER:
                $result = $this->validateTransferSource($array);

                break;

            case TransactionType::OPENING_BALANCE:
                $result = $this->validateOBSource($array);

                break;

            case TransactionType::LIABILITY_CREDIT:
                $result = $this->validateLCSource($array);

                break;

            case TransactionType::RECONCILIATION:
                app('log')->debug('Calling validateReconciliationSource');
                $result = $this->validateReconciliationSource($array);

                break;
        }

        return $result;
    }

    protected function canCreateTypes(array $accountTypes): bool
    {
        app('log')->debug('Can we create any of these types?', $accountTypes);

        /** @var string $accountType */
        foreach ($accountTypes as $accountType) {
            if ($this->canCreateType($accountType)) {
                app('log')->debug(sprintf('YES, we can create a %s', $accountType));

                return true;
            }
        }
        app('log')->debug('NO, we cant create any of those.');

        return false;
    }

    protected function canCreateType(string $accountType): bool
    {
        $canCreate = [AccountType::EXPENSE, AccountType::REVENUE, AccountType::INITIAL_BALANCE, AccountType::LIABILITY_CREDIT];
        if (in_array($accountType, $canCreate, true)) {
            return true;
        }

        return false;
    }

    /**
     * It's a long and fairly complex method, but I don't mind.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function findExistingAccount(array $validTypes, array $data, bool $inverse = false): ?Account
    {
        app('log')->debug('Now in findExistingAccount', $data);
        app('log')->debug('The search will be reversed!');
        $accountId     = array_key_exists('id', $data) ? $data['id'] : null;
        $accountIban   = array_key_exists('iban', $data) ? $data['iban'] : null;
        $accountNumber = array_key_exists('number', $data) ? $data['number'] : null;
        $accountName   = array_key_exists('name', $data) ? $data['name'] : null;

        // find by ID
        if (null !== $accountId && $accountId > 0) {
            $first       = $this->getRepository()->find($accountId);
            $accountType = null === $first ? 'invalid' : $first->accountType->type;
            $check       = in_array($accountType, $validTypes, true);
            $check       = $inverse ? !$check : $check; // reverse the validation check if necessary.
            if ((null !== $first) && $check) {
                app('log')->debug(sprintf('ID: Found %s account #%d ("%s", IBAN "%s")', $first->accountType->type, $first->id, $first->name, $first->iban ?? 'no iban'));

                return $first;
            }
        }

        // find by iban
        if (null !== $accountIban && '' !== (string)$accountIban) {
            $first       = $this->getRepository()->findByIbanNull($accountIban, $validTypes);
            $accountType = null === $first ? 'invalid' : $first->accountType->type;
            $check       = in_array($accountType, $validTypes, true);
            $check       = $inverse ? !$check : $check; // reverse the validation check if necessary.
            if ((null !== $first) && $check) {
                app('log')->debug(sprintf('Iban: Found %s account #%d ("%s", IBAN "%s")', $first->accountType->type, $first->id, $first->name, $first->iban ?? 'no iban'));

                return $first;
            }
        }

        // find by number
        if (null !== $accountNumber && '' !== (string)$accountNumber) {
            $first       = $this->getRepository()->findByAccountNumber($accountNumber, $validTypes);
            $accountType = null === $first ? 'invalid' : $first->accountType->type;
            $check       = in_array($accountType, $validTypes, true);
            $check       = $inverse ? !$check : $check; // reverse the validation check if necessary.
            if ((null !== $first) && $check) {
                app('log')->debug(sprintf('Number: Found %s account #%d ("%s", IBAN "%s")', $first->accountType->type, $first->id, $first->name, $first->iban ?? 'no iban'));

                return $first;
            }
        }

        // find by name:
        if ('' !== (string)$accountName) {
            $first = $this->getRepository()->findByName($accountName, $validTypes);
            if (null !== $first) {
                app('log')->debug(sprintf('Name: Found %s account #%d ("%s", IBAN "%s")', $first->accountType->type, $first->id, $first->name, $first->iban ?? 'no iban'));

                return $first;
            }
        }
        app('log')->debug('Found nothing!');

        return null;
    }

    private function getRepository(): AccountRepositoryInterface|UserGroupAccountRepositoryInterface
    {
        if ($this->useUserGroupRepository) {
            return $this->userGroupAccountRepository;
        }

        return $this->accountRepository;
    }
}
