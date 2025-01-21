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

    public function update($id, Request $request)
    {
        $model = Package::where("id", $id)->first();
        if (!empty($request->name)) {
            $model->name = $request->name;
        }
        if (!empty($model->duration)) {
            $model->duration = $request->duration;
        }
        if (!empty($model->minimum_amount)) {
            $model->minimum_amount = $request->minimum_amount;
        }
        if (!empty($model->maximum_amount)) {
            $model->maximum_amount = $request->maximum_amount;
        }
        if (!empty($model->interest_rate)) {
            $model->interest_rate = $request->interest_rate;
        }

        if ($model->save()) {
            return response()->json([
                "status" => 'success',
                'message' => 'Package updated successfully',
                'data' => $model
            ], 200);
        }

        return response()->json([
            "status" => 'error',
            'message' => 'something went wrong'
        ], 400);
    }

    public function destroy($id)
    {
        $model = Package::where("id", $id)->first();

        if ($model->delete()) {
            return response()->json([
                "status" => 'success',
                'message' => 'Package deleted successfully',
                'data' => $model
            ], 200);
        }

        return response()->json([
            "status" => 'error',
            'message' => 'something went wrong'
        ], 400);
    }
}
