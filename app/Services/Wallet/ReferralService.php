<?php

namespace App\Services\Wallet;

use App\Contracts\ReferralServiceInterface;
use App\Repositories\ReferralRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;
use App\Models\Referral;
use App\Models\TransactionType;
use App\Models\TransactionStatus;
use App\Exceptions\WalletException;
use App\Models\WalletType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralService implements ReferralServiceInterface
{
    protected $referralRepository;
    protected $transactionRepository;
    protected $walletRepository;

    public function __construct(
        ReferralRepository $referralRepository,
        TransactionRepository $transactionRepository,
        WalletRepository $walletRepository
    ) {
        $this->referralRepository = $referralRepository;
        $this->transactionRepository = $transactionRepository;
        $this->walletRepository = $walletRepository;
    }

    public function getReferralProgram(string $programId): array
    {
        $program = $this->referralRepository->findProgram($programId);

        return [
            'id' => $program->id,
            'name' => $program->name,
            'code' => $program->code,
            'referrer_reward' => $program->referrer_reward,
            'referee_reward' => $program->referee_reward,
            'reward_type' => $program->reward_type,
            'start_date' => $program->start_date,
            'end_date' => $program->end_date,
            'is_active' => $program->is_active,
            'total_referrals' => $program->referrals()->count(),
            'completed_referrals' => $program->referrals()->where('status', 'completed')->count(),
            'created_at' => $program->created_at,
            'updated_at' => $program->updated_at,
        ];
    }

    public function getUserReferrals(string $userId): array
    {
        $referrals = $this->referralRepository->getUserReferrals($userId);

        return $referrals->map(function ($referral) {
            return [
                'id' => $referral->id,
                'program_name' => $referral->program->name,
                'referee_email' => $referral->referee->email,
                'referrer_reward' => $referral->referrer_reward,
                'referee_reward' => $referral->referee_reward,
                'status' => $referral->status,
                'completed_at' => $referral->status === 'completed'
                    ? $referral->updated_at
                    : null,
                'reward_transaction_id' => $referral->referrer_transaction_id,
            ];
        })->toArray();
    }

    public function createReferralProgram(array $data): array
    {
        $program = $this->referralRepository->createProgram([
            'name' => $data['name'],
            'code' => $data['code'],
            'referrer_reward' => $data['referrer_reward'],
            'referee_reward' => $data['referee_reward'],
            'reward_type' => $data['reward_type'] ?? 'fixed',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return $program->toArray();
    }

    public function processReferral(string $referralCode, string $referrerId, string $refereeId): array
    {
        $program = $this->referralRepository->findActiveProgramByCode($referralCode);

        if (!$program) {
            throw new WalletException('Invalid or inactive referral program');
        }

        if ($this->referralRepository->referralExists($program->id, $referrerId, $refereeId)) {
            throw new WalletException('Referral relationship already exists');
        }

        $referral = $this->referralRepository->create([
            'program_id' => $program->id,
            'referrer_id' => $referrerId,
            'referee_id' => $refereeId,
            'referrer_reward' => $program->referrer_reward,
            'referee_reward' => $program->referee_reward,
            'status' => 'pending',
        ]);

        return $referral->toArray();
    }

    public function completeReferral(string $referralId): array
    {
        return DB::transaction(function () use ($referralId) {
            $referral = $this->referralRepository->find($referralId);

            if ($referral->status !== 'pending') {
                throw new WalletException('Referral is not in pending state');
            }

            $program = $referral->program;

            // Get or create wallets for both users
            $referrerWallet = $this->getOrCreateRewardWallet($referral->referrer_id);
            $refereeWallet = $this->getOrCreateRewardWallet($referral->referee_id);

            // Process referrer reward
            $referrerTransaction = $this->createRewardTransaction(
                $referrerWallet->id,
                $program->referrer_reward,
                "Referral reward for referring {$referral->referee->email}",
                $referral->id
            );

            // Process referee reward
            $refereeTransaction = $this->createRewardTransaction(
                $refereeWallet->id,
                $program->referee_reward,
                "Referral reward for signing up with {$referral->referrer->email}",
                $referral->id
            );

            // Update referral with transaction IDs
            $referral = $this->referralRepository->update($referral->id, [
                'referrer_transaction_id' => $referrerTransaction->id,
                'referee_transaction_id' => $refereeTransaction->id,
                'status' => 'completed',
            ]);

            return $referral->toArray();
        });
    }

    protected function getOrCreateRewardWallet(string $userId)
    {
        $walletType = WalletType::where('name', 'bonus')->first();

        $wallet = $this->walletRepository->getUserWalletByType($userId, $walletType->id);

        if (!$wallet) {
            $wallet = $this->walletRepository->create([
                'user_id' => $userId,
                'wallet_type_id' => $walletType->id,
                'currency_code' => 'EGP',
            ]);
        }

        return $wallet;
    }

    protected function createRewardTransaction(string $walletId, float $amount, string $narration, string $referralId)
    {
        $transaction = $this->transactionRepository->create([
            'wallet_id' => $walletId,
            'transaction_type_id' => TransactionType::where('code', 'RF')->first()->id, // RF = Referral
            'status_id' => TransactionStatus::where('name', 'pending')->first()->id,
            'amount' => $amount,
            'narration' => $narration,
            'reference' => 'REF-' . Str::upper(Str::random(10)),
            'meta' => ['referral_id' => $referralId],
        ]);

        return $this->transactionRepository->processTransaction($transaction->id);
    }
}
