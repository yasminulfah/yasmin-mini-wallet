<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WalletController extends Controller
{
    public function getBalance(): JsonResponse
    {
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'balance' => $user->balance,
            'user' => [
                'username' => $user->username,
                'email' => $user->email
            ]
        ], 200);
    }

    public function searchUser(Request $request): JsonResponse
    {
        $query = $request->get('query');

        // Mencari user berdasarkan email, tapi bukan diri sendiri
        $users = User::where('email', 'LIKE', "%{$query}%")
            ->where('id', '!=', auth()->id())
            ->select('id', 'username', 'email')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => $users->isEmpty() ? 'No users found' : 'User found',
            'data' => $users
        ], 200);
    }

    public function getStats(): JsonResponse
    {
        $user = auth()->user();

        // Hitung total uang masuk (Topup + Transfer In)
        $income = $user->transactions()
            ->whereIn('type', ['topup', 'transfer_in'])
            ->sum('amount');

        // Hitung total uang keluar (Transfer Out)
        $expense = $user->transactions()
            ->where('type', 'transfer_out')
            ->sum('amount');

        return response()->json([
            'success' => true,
            'message' => 'Wallet statistics retrieved',
            'data' => [
                'current_balance' => $user->balance,
                'total_income' => $income,
                'total_expense' => $expense,
            ]
        ], 200);
    }
}
