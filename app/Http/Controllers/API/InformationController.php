<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Information;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function show()
    {
        $information = Information::with('list_desc')->get();
        $data = [];

        foreach ($information as $info) {
            $descArray = [];

            if ($info->list_desc->isNotEmpty()) {
                $descList = $info->list_desc->map(function ($desc) {
                    return [
                        'title' => $desc->title,
                        'desc' => explode(PHP_EOL, $desc->desc)
                    ];
                })->groupBy('title');

                foreach ($descList as $title => $descs) {
                    $descArray[] = [
                        'title' => $title,
                        'desc' => $descs->pluck('desc')->flatten()->toArray()
                    ];
                }
            } else {
                $descArray = [
                    'title' => '',
                    'desc' => explode(PHP_EOL, $info->desc)
                ];
            }

            $dataItem = [
                'title' => $info->title,
                'desc' => $descArray
            ];

            $data[] = $dataItem;
        }

        return response()->json($data);
    }
}