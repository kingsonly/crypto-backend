<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $model = Package::get();
        return response()->json([
            "status" => 'success',
            'message' => 'success',
            'data' => $model
        ], 200);
    }
    public function store(Request $request)
    {
        $model = new Package();
        $model->name = $request->name;
        $model->duration = $request->duration;
        $model->minimum_amount = $request->minimum_amount;
        $model->maximum_amount = $request->maximum_amount;
        $model->interest_rate = $request->interest_rate;
        $model->status = 1;

        if ($model->save()) {
            return response()->json([
                "status" => 'success',
                'message' => 'Package created successfully',
                'data' => $model
            ], 200);
        }

        return response()->json([
            "status" => 'error',
            'message' => 'something went wrong'
        ], 400);
    }
}
