<?php

namespace App\Exceptions;

class TransactionException extends WalletException
{
    /**
     * Create a new transaction exception instance
     *
     * @param string $message
     * @param int $code
     * @return void
     */
    public function __construct(
        string $message = 'Transaction processing failed',
        int $code = 422 // Unprocessable Entity
    ) {
        parent::__construct($message, $code);
    }
}
