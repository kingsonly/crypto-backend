<?php

namespace App\Http\Controllers;

use App\Models\CryptoWallet;
use Illuminate\Http\Request;

class CryptoWalletController extends Controller
{
    public function index()
    {
        $model = CryptoWallet::get();
        return response()->json([
            "status" => 'success',
            'message' => 'success',
            'data' => $model
        ], 200);
    }

    public function update($id, Request $request)
    {
        $model = CryptoWallet::where("id", $id)->first();
        if (!$model) {
            return response()->json([
                "status" => 'error',
                'message' => 'no wallet address with this wallet id',
            ], 400);
        }
        $model->address = $request->address;
        $model->save();
        if ($model->save()) {
            return response()->json([
                "status" => 'success',
                'message' => 'success',
                'data' => $model
            ], 200);
        }
        return response()->json([
            "status" => 'error',
            'message' => 'could not update wallet',
        ], 400);
    }
}
