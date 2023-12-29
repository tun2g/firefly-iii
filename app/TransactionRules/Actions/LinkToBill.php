<?php

/**
 * LinkToBill.php
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

namespace FireflyIII\TransactionRules\Actions;

use FireflyIII\Events\Model\Rule\RuleActionFailedOnArray;
use FireflyIII\Events\TriggeredAuditLog;
use FireflyIII\Models\RuleAction;
use FireflyIII\Models\TransactionJournal;
use FireflyIII\Models\TransactionType;
use FireflyIII\Repositories\Bill\BillRepositoryInterface;
use FireflyIII\User;

/**
 * Class LinkToBill.
 */
class LinkToBill implements ActionInterface
{
    private RuleAction $action;

    /**
     * TriggerInterface constructor.
     */
    public function __construct(RuleAction $action)
    {
        $this->action = $action;
    }

    public function actOnArray(array $journal): bool
    {
        /** @var User $user */
        $user = User::find($journal['user_id']);

        /** @var BillRepositoryInterface $repository */
        $repository = app(BillRepositoryInterface::class);
        $repository->setUser($user);
        $billName = (string)$this->action->action_value;
        $bill     = $repository->findByName($billName);

        if (null !== $bill && TransactionType::WITHDRAWAL === $journal['transaction_type_type']) {
            $count = \DB::table('transaction_journals')->where('id', '=', $journal['transaction_journal_id'])
                ->where('bill_id', $bill->id)->count()
            ;
            if (0 !== $count) {
                app('log')->error(
                    sprintf(
                        'RuleAction LinkToBill could not set the bill of journal #%d to bill "%s": already set.',
                        $journal['transaction_journal_id'],
                        $billName
                    )
                );
                event(new RuleActionFailedOnArray($this->action, $journal, trans('rules.already_linked_to_subscription', ['name' => $billName])));

                return false;
            }

            \DB::table('transaction_journals')
                ->where('id', '=', $journal['transaction_journal_id'])
                ->update(['bill_id' => $bill->id])
            ;
            app('log')->debug(
                sprintf('RuleAction LinkToBill set the bill of journal #%d to bill #%d ("%s").', $journal['transaction_journal_id'], $bill->id, $bill->name)
            );

            /** @var TransactionJournal $object */
            $object = TransactionJournal::where('user_id', $journal['user_id'])->find($journal['transaction_journal_id']);
            event(new TriggeredAuditLog($this->action->rule, $object, 'set_bill', null, $bill->name));

            return true;
        }

        app('log')->error(
            sprintf(
                'RuleAction LinkToBill could not set the bill of journal #%d to bill "%s": no such bill found or not a withdrawal.',
                $journal['transaction_journal_id'],
                $billName
            )
        );
        event(new RuleActionFailedOnArray($this->action, $journal, trans('rules.cannot_find_subscription', ['name' => $billName])));

        return false;
    }
}
