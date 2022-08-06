<?php

namespace App\Http\Controllers\API;

use App\Helpers\SimpleValidate;
use App\Models\Tree;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TreeHistory;

class TreeController extends Controller
{
    public function getAllDataTree(Request $request)
    {
        $apikey = $request->apikey;
        // $device = $request->device;

        $id = SimpleValidate::checkApikeyAndDevice($apikey);

        $all_tree = DB::table('trees')
        ->select('name', 'type', 'tree_image', 'created_at')
        ->where('users_id', '=', $id)
        ->get();

        return ResponseFormatter::success(
            $all_tree,
            'Data Tree berjaya ditambahkan.'
        ); 
    }

    public function getTreeHistory(Request $request)
    {
        // $apikey = $request->apikey;
        $tree_id = $request->tree_id;

        $tree = DB::table('trees')
        ->join('tree_histories', 'trees.id', '=', 'tree_histories.trees_id')
        ->select('trees.name', 'trees.type', 'trees.tree_image', 'trees.created_at', 'tree_histories.id', 'tree_histories.trees_id', 'tree_histories.description', 'tree_histories.tree_image', 'tree_histories.created_at', 'tree_histories.updated_at')
        ->where('tree_histories.trees_id', '=', $tree_id)
        ->orderBy('tree_histories.id', 'desc')
        ->get();

        return ResponseFormatter::success(
            $tree,
            'Data Tree berjaya diambil.'
        );  
    }

    public function insertTree(Request $request)
    {
        $apikey = $request->apikey;

        $id = SimpleValidate::checkApikeyAndDevice($apikey);

        Tree::create([
            'users_id' => $id['id'],
            'name' => $request->name,
            'type' => $request->type,
            'tree_image' => $request->tree_image
        ]);

        $tree = DB::table('trees')
        ->select('trees.*')
        ->where('users_id', '=', $id)
        ->orderBy('id', 'desc')
        ->first();

        return ResponseFormatter::success(
            $tree,
            'Data Tree berjaya ditambahkan.'
        );   
    }

    public function insertTreeHistory(Request $request)
    {
        $tree_id = $request->tree_id;

        TreeHistory::create([
            'trees_id' => $tree_id,
            'description' => $request->description,
            'tree_image' => $request->tree_image
        ]);

        $tree = DB::table('trees')
        ->join('tree_histories', 'trees.id', '=', 'tree_histories.trees_id')
        ->select('trees.name', 'trees.type', 'trees.tree_image', 'trees.created_at', 'tree_histories.trees_id', 'tree_histories.description', 'tree_histories.tree_image', 'tree_histories.created_at', 'tree_histories.updated_at')
        ->where('tree_histories.trees_id', '=', $tree_id)
        ->orderBy('tree_histories.id', 'desc')
        ->first();

        return ResponseFormatter::success(
            $tree,
            'Data Tree History berjaya ditambahkan.'
        );   
    }

    public function updateTreeHistory(Request $request)
    {
        $tree_history_id = $request->id;
        $description = $request->description;
        $tree_image = $request->tree_image;

        DB::table('tree_histories')
        ->where('id', '=', $tree_history_id)
        ->update(['description' => $description, 'tree_image' => $tree_image]);

        $new_data_tree_history = TreeHistory::where('id', '=', $tree_history_id)
        ->get();

        return ResponseFormatter::success(
            $new_data_tree_history,
            'Data Tree History berjaya diupdate.'
        );  
    }
}
