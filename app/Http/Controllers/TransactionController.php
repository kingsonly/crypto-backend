<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Earning;
use App\Models\Package;
use App\Mail\NotifyMail;
use App\Models\Withdrawal;
use App\Models\Investments;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\InvestmentResource;
use App\Http\Resources\TransactionResource;
use App\Mail\DepositConfirmationMail;
use App\Mail\WithdrawalConfirmationMail;
use App\Mail\WithdrawalRequestMail;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
        if (!$user->is_admin) {
            $data['user_id'] = $user->id;
        }

        $getAllWithFilters = $this->getAllWithFilters($data);
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
            $transactionUsersName = User::where("id", $model->user_id)->first();
            $details = [
                "user" => $transactionUsersName,
                "transaction" => $model,
            ];
            $admins = User::where('is_admin', 1)->get();
            // send email
            if ($admins->count() > 0) {
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new NotifyMail($details));
                }
            }

            return response()->json([
                "status" => 'success',
                'message' => 'Deposit successful',
                'data' => $model
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
        $transaction = Transaction::where('id', $id)->first();
        $user = auth()->id();
        $details = [
            "user" => User::where("id", $user)->first(),
            "transaction" => $transaction,
        ];
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
        DB::beginTransaction();
        try {
            // make a deposit request 
            $model = Transaction::where('id', $id)->first();
            // check if this deposit is the users first deposit 

            if ($model) {
                $model->status = 1;
                $getAllUserDeposits = Transaction::where('user_id', $model->user_id)->where('type', 'deposit')->get();
                if ($getAllUserDeposits->count() == 1) {
                    // check for users ref
                    $user = User::where('id', $model->user_id)->first();
                    if ($user->ref > 0) {
                        $ref = User::where('id', $user->ref)->first();
                        if ($ref) {
                            // make a deposit for ref
                            $refModel = new Transaction();
                            $refModel->user_id = $ref->id;
                            $refModel->amount = ($model->amount * 2) / 100;
                            $refModel->type = 'referral';
                            $refModel->group = 'credit';
                            $refModel->status = 1;

                            if ($refModel->save()) {
                                $refData = [
                                    "name" => $ref->name,
                                    "transaction" => $model
                                ];
                                Mail::to($ref->email)->send(new DepositConfirmationMail($refData));
                            }
                        }
                    }
                }
                if ($model->save()) {

                    DB::commit();
                    $transactionUsersName = User::where("id", $model->user_id)->first();
                    $data = [
                        "name" => $transactionUsersName->name,
                        "transaction" => $model
                    ];
                    Mail::to($transactionUsersName->email)->send(new DepositConfirmationMail($data));
                    return response()->json([
                        "status" => 'success',
                        'message' => 'Deposit confirmed',
                        'data' => $model,
                    ], 200);
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "status" => 'error',
                'message' => 'something went wrong',
                'error' => $th
            ], 400);
        }
    }
    public function depositToSpecificWallet($id, Request $request)
    {
        $user = $id;
        // make a deposit request 
        $model = new Transaction();
        $model->user_id = $user;
        $model->amount = $request->amount;
        $model->method = "Bitcoin";
        $model->type = 'deposit';
        $model->group = 'credit';
        $model->status = 1;

        if ($model->save()) {
            return response()->json([
                "status" => 'success',
                'message' => 'Deposit successful',
                'data' => $model
            ], 200);
        }

        return response()->json([
            "status" => 'error',
            'message' => 'something went wrong'
        ], 400);
    }

    public function withdrawal(Request $request)
    {
        $user = auth()->id();
        // check if the balance is enough
        $balance = $this->getUserWalletBalance($user);
        if ($balance < $request->amount) {
            return response()->json([
                "status" => 'error',
                'message' => 'Insufficient balance',
                'balance' => $balance,
            ], 400);
        }

        DB::beginTransaction();

        try {
            // make a deposit request 
            $model = new Transaction();
            $model->user_id = $user;
            $model->amount = $request->amount;
            $model->method = $request->method;
            $model->type = 'withdraw';
            $model->group = 'debit';

            if ($model->save()) {
                // create withdrawal
                $withdrawal = new Withdrawal();
                $withdrawal->user_id = $user;
                $withdrawal->amount = $request->amount;
                $withdrawal->method = $request->method;
                $withdrawal->transaction_id = $model->id;
                $withdrawal->wallet_address = $request->wallet_address;
                $withdrawal->status = 0;

                if ($withdrawal->save()) {
                    DB::commit();
                    $transactionUsersName = User::where("id", $model->user_id)->first();
                    $data = [
                        "name" => $transactionUsersName->name,
                        "transaction" => $model,
                        "walletAddress" => $withdrawal->wallet_address
                    ];
                    Mail::to($transactionUsersName->email)->send(new WithdrawalRequestMail($data));
                    $details = [
                        "user" => $transactionUsersName,
                        "transaction" => $model,
                    ];
                    $admins = User::where('is_admin', 1)->get();
                    // send email
                    if ($admins->count() > 0) {
                        foreach ($admins as $admin) {
                            Mail::to($admin->email)->send(new NotifyMail($details));
                        }
                    }

                    return (new TransactionResource($model))->additional([
                        'message' => 'success',
                        'status' => 'success'
                    ]);
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "status" => 'error',
                'message' => 'Withdrawal request Failed',
                'error' => $th,
            ], 400);
        }
    }
    public function createInvestment(Request $request)
    {
        $user = auth()->id();
        // check if the balance is enough
        $balance = $this->getUserWalletBalance($user);
        if ($balance < $request->amount) {
            return response()->json([
                "status" => 'error',
                'message' => 'Insufficient balance',
                'balance' => $balance,
            ], 400);
        }

        $package = Package::where('id', $request->package_id)->first();
        if (!$package) {
            return response()->json([
                "status" => 'error',
                'message' => 'Package not found',
            ], 400);
        }
        if ($package->minimum_amount > $request->amount) {
            return response()->json([
                "status" => 'error',
                'message' => 'Minimum amount not met',
            ], 400);
        }
        DB::beginTransaction();

        try {
            // make a deposit request 
            $model = new Transaction();
            $model->user_id = $user;
            $model->amount = $request->amount;
            $model->method = $request->method;
            $model->type = 'investment';
            $model->group = 'debit';
            $model->status = 1;

            if ($model->save()) {
                // create withdrawal
                $investmentModel = new Investments();
                $investmentModel->package_id = $request->package_id;
                $investmentModel->user_id = $user;
                $investmentModel->amount = $request->amount;
                $investmentModel->expected_earning = $request->amount + ($request->amount * $package->interest_rate / 100);
                $investmentModel->transaction_id = $model->id;
                $investmentModel->start_date = Carbon::now()->addDay();
                $investmentModel->last_run = Carbon::now();
                $investmentModel->end_date = Carbon::parse($investmentModel->start_date)->addDays($package->duration);
                $investmentModel->status = 0;

                if ($investmentModel->save()) {
                    DB::commit();
                    return (new TransactionResource($model))->additional([
                        'message' => 'success',
                        'status' => 'success'
                    ]);
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "status" => 'error',
                'message' => 'Withdrawal request Failed',
                'error' => $th,
            ], 400);
        }
    }

    public function approveWithdrawal($id)
    {
        try {
            DB::beginTransaction();
            // approve withdrawal request 
            $model = Transaction::where('id', $id)->first();
            $walletOwner = $model->user_id;
            // check if the balance is enough
            $balance = $this->getUserWalletBalance($walletOwner);
            if ($balance < $model->amount) {
                return response()->json([
                    "status" => 'error',
                    'message' => 'Insufficient balance',
                    'balance' => $balance,
                ], 400);
            }

            if ($model) {
                $model->status = 1;
                if ($model->save()) {
                    $withdrawalModel = Withdrawal::where('transaction_id', $id)->first();
                    if ($withdrawalModel) {
                        $withdrawalModel->status = 1;
                        if ($withdrawalModel->save()) {
                            DB::commit();
                            $transactionUsersName = User::where("id", $model->user_id)->first();
                            $data = [
                                "name" => $transactionUsersName->name,
                                "transaction" => $model,
                                "walletAddress" => $withdrawalModel->wallet_address
                            ];
                            Mail::to($transactionUsersName->email)->send(new WithdrawalConfirmationMail($data));
                            return response()->json([
                                "status" => 'success',
                                'message' => 'Withdrawal approved',
                                'data' => $model,
                            ], 200);
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "status" => 'error',
                'message' => 'something went wrong',
                'data' => $th,
            ], 400);
        }
    }

    public function activeInvestment()
    {
        $user = auth()->id();
        $model = Investments::where('user_id', $user)->where('status', 0)->count();
        return response()->json([
            "status" => 'success',
            'data' => $model,
        ], 200);
    }
    public function totalEarnings()
    {
        $user = auth()->id();
        $model = Earning::where('user_id', $user)->sum("amount");
        return response()->json([
            "status" => 'success',
            'data' => $model,
        ], 200);
    }

    public function investmentHistory()
    {
        $user = auth()->id();
        $model = Investments::where('user_id', $user)->orderBy('id', 'desc')->get();
        return InvestmentResource::collection($model);
    }


    //reusable methods
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
        return $query->orderBy('id', 'desc')->get();
    }

    private function getUserWalletBalance($userId)
    {
        $balance = Transaction::where('user_id', $userId)->where('status', 1)
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
