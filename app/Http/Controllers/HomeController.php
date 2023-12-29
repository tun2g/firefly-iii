<?php

/**
 * HomeController.php
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

namespace FireflyIII\Http\Controllers;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use FireflyIII\Events\RequestedVersionCheckStatus;
use FireflyIII\Exceptions\FireflyException;
use FireflyIII\Helpers\Collector\GroupCollectorInterface;
use FireflyIII\Http\Middleware\Installer;
use FireflyIII\Models\AccountType;
use FireflyIII\Repositories\Account\AccountRepositoryInterface;
use FireflyIII\Repositories\Bill\BillRepositoryInterface;
use FireflyIII\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class HomeController.
 */
class HomeController extends Controller
{
    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        app('view')->share('title', 'Firefly III');
        app('view')->share('mainTitleIcon', 'fa-fire');
        $this->middleware(Installer::class);
    }

    /**
     * Change index date range.
     *
     * @throws \Exception
     */
    public function dateRange(Request $request): JsonResponse
    {
        $stringStart = '';
        $stringEnd   = '';

        try {
            $stringStart = e((string)$request->get('start'));
            $start       = Carbon::createFromFormat('Y-m-d', $stringStart);
        } catch (InvalidFormatException $e) {
            app('log')->error(sprintf('Start: could not parse date string "%s" so ignore it.', $stringStart));
            $start = Carbon::now()->startOfMonth();
        }

        try {
            $stringEnd = e((string)$request->get('end'));
            $end       = Carbon::createFromFormat('Y-m-d', $stringEnd);
        } catch (InvalidFormatException $e) {
            app('log')->error(sprintf('End could not parse date string "%s" so ignore it.', $stringEnd));
            $end = Carbon::now()->endOfMonth();
        }
        if (false === $start) {
            $start = Carbon::now()->startOfMonth();
        }
        if (false === $end) {
            $end = Carbon::now()->endOfMonth();
        }

        $label         = $request->get('label');
        $isCustomRange = false;

        app('log')->debug('Received dateRange', ['start' => $stringStart, 'end' => $stringEnd, 'label' => $request->get('label')]);
        // check if the label is "everything" or "Custom range" which will betray
        // a possible problem with the budgets.
        if ($label === (string)trans('firefly.everything') || $label === (string)trans('firefly.customRange')) {
            $isCustomRange = true;
            app('log')->debug('Range is now marked as "custom".');
        }

        $diff = $start->diffInDays($end) + 1;

        if ($diff > 50) {
            $request->session()->flash('warning', (string)trans('firefly.warning_much_data', ['days' => $diff]));
        }

        $request->session()->put('is_custom_range', $isCustomRange);
        app('log')->debug(sprintf('Set is_custom_range to %s', var_export($isCustomRange, true)));
        $request->session()->put('start', $start);
        app('log')->debug(sprintf('Set start to %s', $start->format('Y-m-d H:i:s')));
        $request->session()->put('end', $end);
        app('log')->debug(sprintf('Set end to %s', $end->format('Y-m-d H:i:s')));

        return response()->json(['ok' => 'ok']);
    }

    /**
     * Show index.
     *
     * @throws FireflyException
     */
    public function index(AccountRepositoryInterface $repository): mixed
    {
        $types = config('firefly.accountTypesByIdentifier.asset');
        $count = $repository->count($types);
        Log::channel('audit')->info('User visits homepage.');

        if (0 === $count) {
            return redirect(route('new-user.index'));
        }
        $subTitle       = (string)trans('firefly.welcome_back');
        $transactions   = [];
        $frontPage      = app('preferences')->getFresh('frontPageAccounts', $repository->getAccountsByType([AccountType::ASSET])->pluck('id')->toArray());
        $frontPageArray = $frontPage->data;
        if (!is_array($frontPageArray)) {
            $frontPageArray = [];
        }

        /** @var Carbon $start */
        $start = session('start', today(config('app.timezone'))->startOfMonth());

        /** @var Carbon $end */
        $end      = session('end', today(config('app.timezone'))->endOfMonth());
        $accounts = $repository->getAccountsById($frontPageArray);
        $today    = today(config('app.timezone'));

        // sort frontpage accounts by order
        $accounts = $accounts->sortBy('order');

        app('log')->debug('Frontpage accounts are ', $frontPageArray);

        /** @var BillRepositoryInterface $billRepository */
        $billRepository = app(BillRepositoryInterface::class);
        $billCount      = $billRepository->getBills()->count();
        // collect groups for each transaction.
        foreach ($accounts as $account) {
            /** @var GroupCollectorInterface $collector */
            $collector = app(GroupCollectorInterface::class);
            $collector->setAccounts(new Collection([$account]))->withAccountInformation()->setRange($start, $end)->setLimit(10)->setPage(1);
            $set            = $collector->getExtractedJournals();
            $transactions[] = ['transactions' => $set, 'account' => $account];
        }

        /** @var User $user */
        $user = auth()->user();
        event(new RequestedVersionCheckStatus($user));

        return view('index', compact('count', 'subTitle', 'transactions', 'billCount', 'start', 'end', 'today'));
    }
}
