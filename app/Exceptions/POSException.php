<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base exception class for all POS-specific exceptions.
 *
 * Provides consistent error handling and JSON response formatting
 * for all business logic exceptions in the POS system.
 */
abstract class POSException extends Exception
{
    /**
     * HTTP status code for the exception.
     */
    protected int $statusCode = Response::HTTP_BAD_REQUEST;

    /**
     * Error code for client identification.
     */
    protected string $errorCode = 'POS_ERROR';

    /**
     * Additional context data.
     *
     * @var array<string, mixed>
     */
    protected array $context = [];

    /**
     * Create a new exception instance.
     *
     * @param  string  $message
     * @param  array<string, mixed>  $context
     * @param  \Throwable|null  $previous
     */
    public function __construct(string $message = '', array $context = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->context = $context;
    }

    /**
     * Get the HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the error code.
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Get the context data.
     *
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        $response = [
            'error' => [
                'code' => $this->errorCode,
                'message' => $this->getMessage(),
                'status' => $this->statusCode,
            ],
        ];

        if (!empty($this->context)) {
            $response['error']['context'] = $this->context;
        }

        if (config('app.debug')) {
            $response['error']['exception'] = get_class($this);
            $response['error']['file'] = $this->getFile();
            $response['error']['line'] = $this->getLine();
            $response['error']['trace'] = $this->getTrace();
        }

        return response()->json($response, $this->statusCode);
    }

    /**
     * Report the exception.
     */
    public function report(): bool
    {
        // Log the exception with context
        logger()->error($this->getMessage(), [
            'exception' => get_class($this),
            'code' => $this->errorCode,
            'context' => $this->context,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ]);

        return true;
    }
}
