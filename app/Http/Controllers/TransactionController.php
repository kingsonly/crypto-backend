<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\NotifyMail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\TransactionResource;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
        if (!$user->is_admin) {
            $data['user_id'] = $user->id;
        }

        $getAllWithFilters = $this->getAllWithFilters($request->all());
        return TransactionResource::collection($getAllWithFilters)->additional([
            'message' => 'success',
            'status' => 'success'
        ]);
    }
    public function deposit(Request $request)
    {
        $user = auth()->id();
        // make a deposit request 
        $model = new Transaction();
        $model->user_id = $user;
        $model->amount = $request->amount;
        $model->method = $request->method;
        $model->type = 'deposit';
        $model->group = 'credit';

        if ($model->save()) {
            return response()->json([
                "status" => 'success',
                'message' => 'Deposit successful'
            ], 200);
        }

        return response()->json([
            "status" => 'error',
            'message' => 'something went wrong'
        ], 400);
    }

    public function wallet()
    {
        $balance = $this->getUserWalletBalance(auth()->id());
        return response()->json([
            "status" => 'success',
            'message' => 'Deposit successful',
            'data' => $balance
        ], 200);
    }

    public function show($id)
    {
        $details = Transaction::where('id', $id)->first();
        return (new TransactionResource($details))->additional([
            'message' => 'success',
            'status' => 'success'
        ]);
    }


    public function notifyAdminOfTransaction($id)
    {

        $details = Transaction::where('id', $id)->first();
        $admins = User::where('is_admin', 1)->get();
        // send email
        if ($admins->count() > 0) {
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new NotifyMail($details));
            }
        }
        return response()->json([
            "status" => 'success',
        ]);
    }
    public function confirmDeposit($id)
    {

        // make a deposit request 
        $model = Transaction::where('id', $id)->first();
        if ($model) {
            $model->status = 1;
            if ($model->save()) {
                return response()->json([
                    "status" => 'success',
                    'message' => 'Deposit confirmed',
                    'data' => $model,
                ], 200);
            }
        }

        return response()->json([
            "status" => 'error',
            'message' => 'something went wrong'
        ], 400);
    }

    private function getAllWithFilters(array $filters = [])
    {
        $query = Transaction::query();

        // Apply dynamic filters
        foreach ($filters as $key => $value) {

            if (
                $key !== 'amount' &&
                $key !== 'user_id' &&
                $key !== 'method' &&
                $key !== 'type' &&
                $key !== 'group' &&
                $key !== 'status'
            ) {
                continue;
            }
            $query->where($key, $value);
        }
        return $query->get();
    }

    private function getUserWalletBalance($userId)
    {
        $balance = Transaction::where('user_id', $userId)
            ->selectRaw("
            SUM(CASE 
                WHEN `type` IN ('deposit', 'earning', 'referral') THEN amount
                WHEN `type` IN ('withdraw', 'transfer', 'investment') THEN -amount
                ELSE 0 
            END) as balance
        ")
            ->value('balance'); // Fetch the calculated balance

        return $balance ?? 0; // Return 0 if no transactions found
    }
}
