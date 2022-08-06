<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $apikey = rand(1000000000,9999999999);

        try {
            User::create([
                'fullname' => $request->fullname,
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
                'phone_number' => $request->phone_number,
                'apikey' => $apikey
            ]);

            $user = DB::table('users')
            ->select('users.*')
            ->where('apikey', '=', $apikey)
            ->first();

            return ResponseFormatter::success(
                $user,
                'User berjaya di daftarkan.'
            );


        } catch(Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'Something went wrong',
                    'error' => $error,
                ],
                'Authentication Failed',
                500
            );
        }
    }

    public function login(Request $request)
    {
        $username = $request->username;
        $email = $request->email;
        $password = $request->password;

        if($username && $password) {
            $user = User::where('username', '=', $username)
            ->where('password', '=', $password)
            ->first();
        } else {
            $user = User::where('email', '=', $email)
            ->where('password', '=', $password)
            ->first();
        }

        if(!$user) {
            return ResponseFormatter::error(
                [
                    'message' => 'Something went wrong',
                    // 'error' => $error,
                ],
                'Authentication Failed',
                500
            );
        }

        return ResponseFormatter::success(
            $user,
            'User berjaya login.'
        );
    }

    public function editDataUser(Request $request)
    {
        $apikey = $request->apikey;
        $fullname = $request->fullname;
        $username = $request->username;
        $email = $request->email;
        $gender = $request->gender;
        $age = $request->age;
        $phone_number = $request->phone_number;

        User::where('apikey', '=', $apikey)
        ->update(['fullname' => $fullname, 'username' => $username, 'email' => $email, 'gender' => $gender, 'age' => $age, 'phone_number' => $phone_number]);
        
        $user = User::where('apikey', '=', $apikey)
        ->first();

        if(!$user) {
            return ResponseFormatter::error(
                [
                    'message' => 'Something went wrong',
                    // 'error' => $error,
                ],
                'Authentication Failed',
                500
            );
        }

        return ResponseFormatter::success(
            $user,
            'Data Anda Berhasil diubah.'
        );
    }
}
