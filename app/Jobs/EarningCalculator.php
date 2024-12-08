<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EarningCalculator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->handleInvestmentEarning();
    }

    private function handleInvestmentEarning()
    {
        try {
            DB::beginTransaction();

            if (!empty($this->data)) {
                foreach ($this->data as $key => $value) {
                    $transaction = new \App\Models\Transaction();
                    $getPackage = \App\Models\Package::where('id', $value['package_id'])->first();
                    // if end_date is == today
                    if ($getPackage) {
                        $earningAmount = ($value["amount"] * $getPackage->interest_rate / 100) / $getPackage->duration;
                        // create a new transaction
                        $transaction->user_id = $value['user_id'];
                        $transaction->amount = $earningAmount;
                        $transaction->type = 'earning';
                        $transaction->group = 'credit';
                        $transaction->status = 0;
                        if ($transaction->save()) {
                            $earning = new \App\Models\Earning();
                            $earning->user_id = $value['user_id'];
                            $earning->amount = $earningAmount;
                            $earning->transaction_id = $transaction->id;
                            $earning->investment_id = $value['id'];
                            $earning->status = 1;
                            $earning->save();
                        }
                        if ($value["end_date"] == date('Y-m-d')) {
                            $investmentModel = \App\Models\Investments::where('id', $value['id'])->first();
                            $investmentModel->status = 1;
                            $investmentModel->save();

                            $transactionModel = new \App\Models\Transaction();
                            $transactionModel->user_id = $value['user_id'];
                            $transactionModel->amount = $value['amount'];
                            $transactionModel->type = 'deposit';
                            $transactionModel->group = 'credit';
                            $transactionModel->status = 1;
                            $transactionModel->save();

                            //update transaction status for each earning
                            $getAllEarnings = \App\Models\Earning::where('investment_id', $value['id'])->get();
                            foreach ($getAllEarnings as $earning) {
                                $getTransaction = \App\Models\Transaction::where('id', $earning->transaction_id)->first();
                                $getTransaction->status = 1;
                                $getTransaction->save();
                            }
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }
}
