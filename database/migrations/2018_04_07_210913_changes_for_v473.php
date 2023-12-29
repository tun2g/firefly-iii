<?php

/**
 * 2018_04_07_210913_changes_for_v473.php
 * Copyright (c) 2019 james@firefly-iii.org.
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

use Doctrine\DBAL\Schema\Exception\ColumnDoesNotExist;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class ChangesForV473.
 *
 * @codeCoverageIgnore
 */
class ChangesForV473 extends Migration
{
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('bills', 'transaction_currency_id')) {
            try {
                Schema::table(
                    'bills',
                    static function (Blueprint $table): void {
                        // cannot drop foreign keys in SQLite:
                        if ('sqlite' !== config('database.default')) {
                            $table->dropForeign('bills_transaction_currency_id_foreign');
                        }
                        $table->dropColumn('transaction_currency_id');
                    }
                );
            } catch (ColumnDoesNotExist|QueryException $e) {
                app('log')->error(sprintf('Could not execute query: %s', $e->getMessage()));
                app('log')->error('If the column or index already exists (see error), this is not an problem. Otherwise, please open a GitHub discussion.');
            }
        }

        if (!Schema::hasColumn('rules', 'strict')) {
            try {
                Schema::table(
                    'rules',
                    static function (Blueprint $table): void {
                        $table->dropColumn('strict');
                    }
                );
            } catch (ColumnDoesNotExist|QueryException $e) {
                app('log')->error(sprintf('Could not execute query: %s', $e->getMessage()));
                app('log')->error('If the column or index already exists (see error), this is not an problem. Otherwise, please open a GitHub discussion.');
            }
        }
    }

    /**
     * Run the migrations.
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function up(): void
    {
        if (!Schema::hasColumn('bills', 'transaction_currency_id')) {
            try {
                Schema::table(
                    'bills',
                    static function (Blueprint $table): void {
                        $table->integer('transaction_currency_id', false, true)->nullable()->after('user_id');
                        $table->foreign('transaction_currency_id')->references('id')->on('transaction_currencies')->onDelete('set null');
                    }
                );
            } catch (QueryException $e) {
                app('log')->error(sprintf('Could not execute query: %s', $e->getMessage()));
                app('log')->error('If the column or index already exists (see error), this is not an problem. Otherwise, please open a GitHub discussion.');
            }
        }
        if (!Schema::hasColumn('rules', 'strict')) {
            try {
                Schema::table(
                    'rules',
                    static function (Blueprint $table): void {
                        $table->boolean('strict')->default(true);
                    }
                );
            } catch (QueryException $e) {
                app('log')->error(sprintf('Could not execute query: %s', $e->getMessage()));
                app('log')->error('If the column or index already exists (see error), this is not an problem. Otherwise, please open a GitHub discussion.');
            }
        }
    }
}
