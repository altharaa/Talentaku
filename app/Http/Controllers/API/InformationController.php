<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InfoList;
use App\Models\Information;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function show()
    {
        $information = Information::select('id', 'title', 'desc')->get();

        if ($information) {
            return response()->json([
                'message' => 'Information retrieved successfully',
                'information' => $information,
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to retrieve information',
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        $information = Information::findOrFail($id);
        $validatedData = $request->all();

        $information->update($validatedData);

        if ($information) {
            return response()->json([
                'message' => 'Information updated successfully',
                'information' => $information,
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to update information',
            ], 500);
        }
    }

    public function store(Request $request) 
    {

        $request->validate([
            'title' => 'required',
            'desc' => 'required',
        ]);

        $information = Information::create([
            'title' => $request->title,
            'desc' => $request->desc,
        ]);

        if ($information) {
            return response()->json([
                'message' => 'Information added successfully',
                'information' => $information,
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to add information',
            ], 500);
        }
    }

    public function destroy($id)
    {
        $information = Information::findOrFail($id);
        if ($information->delete()) {
            return response()->json([
                'message' => 'Information deleted successfully',
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to delete information',
            ], 500);
        }
    }

    public function get() 
    {
        $informationLists = InfoList::with('pivotLists.listDesc')->get();

        if ($informationLists->isEmpty()) {
            return response()->json([
                'message' => 'No information available',
            ]);
        } else {
            $response = $informationLists->map(function ($information) {
                return [
                    'information_title' => $information->title,
                    'information_data' => $information->pivotLists->map(function ($pivotList) {
                        return [
                            'title' => $pivotList->listDesc->title,
                            'desc' => explode(PHP_EOL,$pivotList->listDesc->desc),
                        ];
                    })->toArray(),
                ];
            });
            return response()->json($response);
        }
    }
}