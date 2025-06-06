<?php

namespace App\Providers;

use App\Contracts\ReferralServiceInterface;
use App\Contracts\RequestServiceInterface;
use App\Contracts\TransactionServiceInterface;
use App\Contracts\WalletServiceInterface;
use App\Repositories\ReferralRepository;
use App\Repositories\RequestRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;
use App\Services\Wallet\ReferralService;
use App\Services\Wallet\RequestService;
use App\Services\Wallet\TransactionService;
use App\Services\Wallet\WalletService;
use Illuminate\Support\ServiceProvider;

class WalletServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(WalletServiceInterface::class, function ($app) {
            return new WalletService(
                $app->make(WalletRepository::class)
            );
        });

        $this->app->bind(TransactionServiceInterface::class, function ($app) {
            return new TransactionService(
                $app->make(TransactionRepository::class),
                $app->make(WalletRepository::class)
            );
        });

        $this->app->bind(RequestServiceInterface::class, function ($app) {
            return new RequestService(
                $app->make(RequestRepository::class),
                $app->make(TransactionRepository::class),
                $app->make(WalletRepository::class)
            );
        });

        $this->app->bind(ReferralServiceInterface::class, function ($app) {
            return new ReferralService(
                $app->make(ReferralRepository::class),
                $app->make(TransactionRepository::class),
                $app->make(WalletRepository::class)
            );
        });
    }

    public function boot()
    {
        //
    }
}
