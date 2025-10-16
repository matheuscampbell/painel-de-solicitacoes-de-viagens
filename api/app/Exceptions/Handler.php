<?php

namespace App\Exceptions;

use App\Exceptions\Base\BusinessException;
use App\Exceptions\Base\DomainException;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        $this->renderable(function (DomainException $exception, Request $request) {
            if ($request->expectsJson()) {
                return $this->error($exception->getMessage(), $exception->statusCode());
            }
        });
    }
}
