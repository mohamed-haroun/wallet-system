<?php

namespace App\Exceptions;

use Exception;

class WalletException extends Exception
{
    /**
     * Default exception code
     *
     * @var int
     */
    protected $code = 400;

    /**
     * Create a new wallet exception instance
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     * @return void
     */
    public function __construct(
        string $message = 'Wallet operation failed',
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code ?: $this->code, $previous);
    }

    /**
     * Report the exception
     *
     * @return void
     */
    public function report()
    {
        // You can implement specific reporting logic here
        // For example, log to a special channel
    }

    /**
     * Render the exception into an HTTP response
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
        ], $this->getCode());
    }
}
