<?php

/**
 * Handler.php
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

namespace FireflyIII\Exceptions;

use FireflyIII\Jobs\MailError;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Laravel\Passport\Exceptions\OAuthServerException as LaravelOAuthException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Handler
 */
class Handler extends ExceptionHandler
{
    /**
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport
        = [
            AuthenticationException::class,
            LaravelValidationException::class,
            NotFoundHttpException::class,
            OAuthServerException::class,
            LaravelOAuthException::class,
            TokenMismatchException::class,
            HttpException::class,
            SuspiciousOperationException::class,
            BadHttpHeaderException::class,
        ];

    /**
     * Render an exception into an HTTP response. It's complex but lucky for us, we never use it because
     * Firefly III never crashes.
     *
     * @param Request $request
     *
     * @throws \Throwable
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function render($request, \Throwable $e): Response
    {
        $expectsJson = $request->expectsJson();
        // if the user requests anything /api/, assume the user wants to see JSON.
        if (str_starts_with($request->getRequestUri(), '/api/')) {
            app('log')->debug('API endpoint, always assume user wants JSON.');
            $expectsJson = true;
        }

        app('log')->debug('Now in Handler::render()');
        if ($e instanceof LaravelValidationException && $expectsJson) {
            // ignore it: controller will handle it.
            app('log')->debug(sprintf('Return to parent to handle LaravelValidationException(%d)', $e->status));

            return parent::render($request, $e);
        }
        if ($e instanceof NotFoundHttpException && $expectsJson) {
            // JSON error:
            app('log')->debug('Return JSON not found error.');

            return response()->json(['message' => 'Resource not found', 'exception' => 'NotFoundHttpException'], 404);
        }

        if ($e instanceof AuthenticationException && $expectsJson) {
            // somehow Laravel handler does not catch this:
            app('log')->debug('Return JSON unauthenticated error.');

            return response()->json(['message' => 'Unauthenticated', 'exception' => 'AuthenticationException'], 401);
        }

        if ($e instanceof OAuthServerException && $expectsJson) {
            app('log')->debug('Return JSON OAuthServerException.');

            // somehow Laravel handler does not catch this:
            return response()->json(['message' => $e->getMessage(), 'exception' => 'OAuthServerException'], 401);
        }
        if ($e instanceof BadRequestHttpException) {
            app('log')->debug('Return JSON BadRequestHttpException.');

            return response()->json(['message' => $e->getMessage(), 'exception' => 'BadRequestHttpException'], 400);
        }

        if ($e instanceof BadHttpHeaderException) {
            // is always API exception.
            app('log')->debug('Return JSON BadHttpHeaderException.');

            return response()->json(['message' => $e->getMessage(), 'exception' => 'BadHttpHeaderException'], $e->statusCode);
        }

        if ($expectsJson) {
            $errorCode = 500;
            $errorCode = $e instanceof MethodNotAllowedHttpException ? 405 : $errorCode;

            $isDebug = (bool) config('app.debug', false);
            if ($isDebug) {
                app('log')->debug(sprintf('Return JSON %s with debug.', get_class($e)));

                return response()->json(
                    [
                        'message'   => $e->getMessage(),
                        'exception' => get_class($e),
                        'line'      => $e->getLine(),
                        'file'      => $e->getFile(),
                        'trace'     => $e->getTrace(),
                    ],
                    $errorCode
                );
            }
            app('log')->debug(sprintf('Return JSON %s.', get_class($e)));

            return response()->json(
                ['message' => sprintf('Internal Firefly III Exception: %s', $e->getMessage()), 'exception' => get_class($e)],
                $errorCode
            );
        }

        if ($e instanceof NotFoundHttpException) {
            app('log')->debug('Refer to GracefulNotFoundHandler');
            $handler = app(GracefulNotFoundHandler::class);

            return $handler->render($request, $e);
        }

        // special view for database errors with extra instructions
        if ($e instanceof QueryException) {
            app('log')->debug('Return Firefly III database exception view.');
            $isDebug = config('app.debug');

            return response()->view('errors.DatabaseException', ['exception' => $e, 'debug' => $isDebug], 500);
        }

        if ($e instanceof FireflyException || $e instanceof \ErrorException || $e instanceof OAuthServerException) {
            app('log')->debug('Return Firefly III error view.');
            $isDebug = config('app.debug');

            return response()->view('errors.FireflyException', ['exception' => $e, 'debug' => $isDebug], 500);
        }

        app('log')->debug(sprintf('Error "%s" has no Firefly III treatment, parent will handle.', get_class($e)));

        return parent::render($request, $e);
    }

    /**
     * Report or log an exception.
     *
     * @throws \Throwable
     */
    public function report(\Throwable $e): void
    {
        $doMailError = (bool) config('firefly.send_error_message');
        if ($this->shouldntReportLocal($e) || !$doMailError) {
            parent::report($e);

            return;
        }
        $userData = [
            'id'    => 0,
            'email' => 'unknown@example.com',
        ];
        if (auth()->check()) {
            $userData['id']    = auth()->user()->id;
            $userData['email'] = auth()->user()->email;
        }

        $headers = request()->headers->all();

        $data = [
            'class'        => get_class($e),
            'errorMessage' => $e->getMessage(),
            'time'         => date('r'),
            'stackTrace'   => $e->getTraceAsString(),
            'file'         => $e->getFile(),
            'line'         => $e->getLine(),
            'code'         => $e->getCode(),
            'version'      => config('firefly.version'),
            'url'          => request()->fullUrl(),
            'userAgent'    => request()->userAgent(),
            'json'         => request()->acceptsJson(),
            'method'       => request()->method(),
            'headers'      => $headers,
        ];

        // create job that will mail.
        $ipAddress = request()->ip() ?? '0.0.0.0';
        $job       = new MailError($userData, (string) config('firefly.site_owner'), $ipAddress, $data);
        dispatch($job);

        parent::report($e);
    }

    /**
     * Convert a validation exception into a response.
     *
     * @param Request $request
     */
    protected function invalid($request, LaravelValidationException $exception): \Illuminate\Http\Response|JsonResponse|RedirectResponse
    {
        // protect against open redirect when submitting invalid forms.
        $previous = app('steam')->getSafePreviousUrl();
        $redirect = $this->getRedirectUrl($exception);

        return redirect($redirect ?? $previous)
            ->withInput(Arr::except($request->input(), $this->dontFlash))
            ->withErrors($exception->errors(), $request->input('_error_bag', $exception->errorBag))
        ;
    }

    private function shouldntReportLocal(\Throwable $e): bool
    {
        return null !== Arr::first(
            $this->dontReport,
            static function ($type) use ($e) {
                return $e instanceof $type;
            }
        );
    }

    /**
     * Only return the redirectTo property from the exception if it is a valid URL. Return NULL otherwise.
     */
    private function getRedirectUrl(LaravelValidationException $exception): ?string
    {
        if (null === $exception->redirectTo) {
            return null;
        }
        $safe         = route('index');
        $previous     = $exception->redirectTo;
        $previousHost = parse_url($previous, PHP_URL_HOST);
        $safeHost     = parse_url($safe, PHP_URL_HOST);

        return null !== $previousHost && $previousHost === $safeHost ? $previous : $safe;
    }
}
