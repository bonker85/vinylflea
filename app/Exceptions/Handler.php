<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Mail;
use Throwable;
use VK\Exceptions\VKApiException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if ($e instanceof VKApiException) {
                $reportingMessage = "";
                $reportingMessage .= "Message: <b>" . $e->getMessage() . "</b><br/>";
                $reportingMessage .= "Trace:<br/>";
                foreach ($e->getTrace() as $trace) {
                    $reportingMessage .= "File: " . $trace['file'] . " Line: " . $trace['line'] . "<br/>";
                }
                $details = [
                    'subject' => 'VK SDK',
                    'message' => $reportingMessage
                ];

                Mail::to(env('MAIL_FROM_ADDRESS'))->send(new \App\Mail\ErrorReporting($details));
            }
        });
    }
}
