<?php

/*
 * NavigationPreferredEndOfPeriodTest.php
 * Copyright (c) 2023 Antonio Spinelli <https://github.com/tonicospinelli>
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

namespace Tests\unit\Support;

use Carbon\Carbon;
use FireflyIII\Support\Navigation;
use PHPUnit\Framework\TestCase;

/**
 * @group unit-test
 * @group support
 * @group navigation
 *
 * @internal
 *
 * @coversNothing
 */
final class NavigationPreferredEndOfPeriodTest extends TestCase
{
    private Navigation $navigation;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->navigation = new Navigation();
    }

    public static function providePeriods(): iterable
    {
        return [
            '1 week'    => ['start' => Carbon::now(), 'end' => Carbon::now()->addWeek(), 'expected' => 'endOfDay'],
            '1 month'   => ['start' => Carbon::now(), 'end' => Carbon::now()->addMonth(), 'expected' => 'endOfDay'],
            '2 months'  => ['start' => Carbon::now(), 'end' => Carbon::now()->addMonths(2), 'expected' => 'endOfMonth'],
            '3 months'  => ['start' => Carbon::now(), 'end' => Carbon::now()->addMonths(3), 'expected' => 'endOfMonth'],
            '6 months'  => ['start' => Carbon::now(), 'end' => Carbon::now()->addMonths(6), 'expected' => 'endOfMonth'],
            '7 months'  => ['start' => Carbon::now(), 'end' => Carbon::now()->addMonths(7), 'expected' => 'endOfMonth'],
            '11 months' => ['start' => Carbon::now(), 'end' => Carbon::now()->addMonths(11), 'expected' => 'endOfMonth'],
            '12 months' => ['start' => Carbon::now(), 'end' => Carbon::now()->addMonths(12), 'expected' => 'endOfMonth'],
            '13 months' => ['start' => Carbon::now(), 'end' => Carbon::now()->addMonths(13), 'expected' => 'endOfYear'],
            '16 months' => ['start' => Carbon::now(), 'end' => Carbon::now()->addMonths(16), 'expected' => 'endOfYear'],
            '1 year'    => ['start' => Carbon::now(), 'end' => Carbon::now()->addYear(), 'expected' => 'endOfMonth'],
            '2 years'   => ['start' => Carbon::now(), 'end' => Carbon::now()->addYears(2), 'expected' => 'endOfYear'],
        ];
    }

    /**
     * @dataProvider providePeriods
     */
    public function testGivenStartAndEndDatesWhenCallPreferredEndOfPeriodThenReturnsTheExpectedFormatSuccessful(Carbon $start, Carbon $end, string $expected): void
    {
        $formatPeriod = $this->navigation->preferredEndOfPeriod($start, $end);
        self::assertSame($expected, $formatPeriod);
    }
}
