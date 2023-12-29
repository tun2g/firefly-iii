<?php

/**
 * BudgetFormRequest.php
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

namespace FireflyIII\Http\Requests;

use FireflyIII\Models\Budget;
use FireflyIII\Support\Request\ChecksLogin;
use FireflyIII\Support\Request\ConvertsDataTypes;
use FireflyIII\Validation\AutoBudget\ValidatesAutoBudgetRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Class BudgetFormUpdateRequest
 */
class BudgetFormUpdateRequest extends FormRequest
{
    use ChecksLogin;
    use ConvertsDataTypes;
    use ValidatesAutoBudgetRequest;

    /**
     * Returns the data required by the controller.
     */
    public function getBudgetData(): array
    {
        return [
            'name'               => $this->convertString('name'),
            'active'             => $this->boolean('active'),
            'auto_budget_type'   => $this->convertInteger('auto_budget_type'),
            'currency_id'        => $this->convertInteger('auto_budget_currency_id'),
            'auto_budget_amount' => $this->convertString('auto_budget_amount'),
            'auto_budget_period' => $this->convertString('auto_budget_period'),
        ];
    }

    /**
     * Rules for this request.
     */
    public function rules(): array
    {
        $nameRule = 'required|between:1,100|uniqueObjectForUser:budgets,name';

        /** @var null|Budget $budget */
        $budget = $this->route()->parameter('budget');

        if (null !== $budget) {
            $nameRule = 'required|between:1,100|uniqueObjectForUser:budgets,name,'.$budget->id;
        }

        return [
            'name'                    => $nameRule,
            'active'                  => 'numeric|between:0,1',
            'auto_budget_type'        => 'numeric|integer|gte:0|lte:31',
            'auto_budget_currency_id' => 'exists:transaction_currencies,id',
            'auto_budget_amount'      => 'min:0|max:1000000000|required_if:auto_budget_type,1|required_if:auto_budget_type,2|numeric',
            'auto_budget_period'      => 'in:daily,weekly,monthly,quarterly,half_year,yearly',
        ];
    }

    /**
     * Configure the validator instance with special rules for after the basic validation rules.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(
            function (Validator $validator): void {
                // validate all account info
                $this->validateAutoBudgetAmount($validator);
            }
        );
    }
}
