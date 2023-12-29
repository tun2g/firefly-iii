<?php

/**
 * Controller.php
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

use FireflyIII\Support\Http\Controllers\RequestInformation;
use FireflyIII\Support\Http\Controllers\UserNavigation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class Controller.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use RequestInformation;
    use UserNavigation;
    use ValidatesRequests;

    protected string $dateTimeFormat;
    protected string $monthAndDayFormat;
    protected string $monthFormat;
    protected string $redirectUrl = '/';

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        // is site a demo site?
        $isDemoSiteConfig = app('fireflyconfig')->get('is_demo_site', config('firefly.configuration.is_demo_site', false));
        $isDemoSite       = (bool)$isDemoSiteConfig->data;
        app('view')->share('IS_DEMO_SITE', $isDemoSite);
        app('view')->share('DEMO_USERNAME', config('firefly.demo_username'));
        app('view')->share('DEMO_PASSWORD', config('firefly.demo_password'));
        app('view')->share('FF_VERSION', config('firefly.version'));

        // is webhooks enabled?
        app('view')->share('featuringWebhooks', true === config('firefly.feature_flags.webhooks') && true === config('firefly.allow_webhooks'));

        // share custom auth guard info.
        $authGuard = config('firefly.authentication_guard');
        $logoutUrl = config('firefly.custom_logout_url');

        app('view')->share('authGuard', $authGuard);
        app('view')->share('logoutUrl', $logoutUrl);

        // upload size
        $maxFileSize = app('steam')->phpBytes((string)ini_get('upload_max_filesize'));
        $maxPostSize = app('steam')->phpBytes((string)ini_get('post_max_size'));
        $uploadSize  = min($maxFileSize, $maxPostSize);
        app('view')->share('uploadSize', $uploadSize);

        // share is alpha, is beta
        $isAlpha = false;
        if (str_contains(config('firefly.version'), 'alpha')) {
            $isAlpha = true;
        }

        $isBeta = false;
        if (str_contains(config('firefly.version'), 'beta')) {
            $isBeta = true;
        }

        app('view')->share('FF_IS_ALPHA', $isAlpha);
        app('view')->share('FF_IS_BETA', $isBeta);

        $this->middleware(
            function ($request, $next): mixed {
                $locale = app('steam')->getLocale();
                // translations for specific strings:
                $this->monthFormat       = (string)trans('config.month_js', [], $locale);
                $this->monthAndDayFormat = (string)trans('config.month_and_day_js', [], $locale);
                $this->dateTimeFormat    = (string)trans('config.date_time_js', [], $locale);
                $darkMode                = 'browser';
                // get shown-intro-preference:
                if (auth()->check()) {
                    $language  = app('steam')->getLanguage();
                    $locale    = app('steam')->getLocale();
                    $darkMode  = app('preferences')->get('darkMode', 'browser')->data;
                    $page      = $this->getPageName();
                    $shownDemo = $this->hasSeenDemo();
                    app('view')->share('language', $language);
                    app('view')->share('locale', $locale);
                    app('view')->share('shownDemo', $shownDemo);
                    app('view')->share('current_route_name', $page);
                    app('view')->share('original_route_name', \Route::currentRouteName());
                }
                app('view')->share('darkMode', $darkMode);

                return $next($request);
            }
        );
    }
}
