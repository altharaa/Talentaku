<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function resStoreData($data)
    {
        return response([
            'success' => true,
            'message' => 'Created Succesfully',
            'data' => $data
        ], 201);
    }

    public function resUpdateData($data)
    {
        return response([
            'success' => true,
            'message' => 'Updated Succesfully',
            'data' => $data
        ], 200);
    }

    public function resDeleteData($data)
    {
        return response([
            'success' => true,
            'message' => 'Deleted Succesfully',
            'data' => $data
        ], 200);
    }

    public function resError($data, $code)
    {
        return response([
            'success' => false,
            'message' => $data,
        ], $code);
    }
}
