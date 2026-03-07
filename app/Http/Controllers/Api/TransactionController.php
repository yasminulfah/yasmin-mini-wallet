<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Http\Requests\Wallet\TopUpRequest;
use App\Http\Requests\Wallet\TransferRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Fitur Top Up Saldo
    public function topup(TopUpRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();

            DB::transaction(function () use ($user, $request) {
                // Lock row user
                $user->lockForUpdate();

                // Tambah saldo user
                $user->increment('balance', $request->amount);

                // Catat mutasi
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'topup',
                    'amount' => $request->amount,
                    'description' => 'Top Up Saldo'
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Top Up Successful',
                'balance' => $user->fresh()->balance 
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Top Up failed, please try again later.'
            ], 500);
        }
    }

    public function getTransactions(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            $query = Transaction::where('user_id', $user->id)
                ->with('relatedUser'); 

            // Logika Filter Berdasarkan Range
            $range = $request->query('range', 'today');

            if ($range === 'today') {
                $query->whereDate('created_at', today());
            } elseif ($range === 'weekly') {
                $query->whereDate('created_at', '>=', now()->subDays(7));
            } elseif ($range === 'monthly') {
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
            }

            // Pagination
            $transactions = $query->latest()->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $transactions
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display a specific transaction detail
     */
    public function show($id): JsonResponse
    {
        $transaction = auth()->user()->transactions()
            ->with('relatedUser:id,username,email')
            ->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found or unauthorized'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaction detail retrieved',
            'data' => $transaction
        ], 200);
    }

    // Fitur Transfer Saldo (Inter-User)
    public function transfer(TransferRequest $request): JsonResponse
    {
        $sender = auth()->user();
        $receiver = User::where('email', $request->email)->first();

        // Database Transaction 
        DB::beginTransaction();

        try {
            // 1. Potong saldo pengirim & tambah saldo penerima
            $sender->decrement('balance', $request->amount);
            $receiver->increment('balance', $request->amount);

            // 2. Simpan record transaksi untuk pengirim (transfer_out)
            $transaction = Transaction::create([
                'user_id' => $sender->id,
                'related_user_id' => $receiver->id,
                'type' => 'transfer_out',
                'amount' => $request->amount,
                'description' => "Transfer to " . $receiver->email . " was successful."
            ]);

            // 3. Simpan record transaksi untuk penerima (transfer_in)
            Transaction::create([
                'user_id' => $receiver->id,
                'related_user_id' => $sender->id,
                'type' => 'transfer_in',
                'amount' => $request->amount,
                'description' => "Received transfer from " . $sender->email
            ]);

            // 4. Load relasi untuk Resi
            $transaction->load('relatedUser:id,username,email');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transfer successful',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'recipient' => $transaction->relatedUser->username,
                    'recipient_email' => $transaction->relatedUser->email,
                    'description' => $transaction->description,
                    'date' => $transaction->created_at->format('d M Y H:i'),
                    'reference_number' => 'YW-' . strtoupper(uniqid()) 
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transfer failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
