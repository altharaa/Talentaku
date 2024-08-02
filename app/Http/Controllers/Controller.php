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
            'message' => 'success',
            'data' => $data
        ], 201);
    }

    public function resUpdateData($data)
    {
        return response(['message' => $data], 200);
    }

    public function resDeleteData($data)
    {
        return response(['message' => $data . ' Deleted'], 200);
    }

    public function resError($data, $code)
    {
        return response([
            'message' => 'failed',
            'data' => $data
        ], $code);
    }
}
