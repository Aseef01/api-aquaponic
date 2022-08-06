<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Sensor;
use App\Models\ButtonSetup;
use App\Models\DeviceInput;
use Illuminate\Http\Request;
use App\Helpers\SimpleValidate;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Device;

class DeviceController extends Controller
{
    public function changeButtonSetup(Request $request)
    {
        $apikey = $request->apikey;
        $device = $request->device;

        $id = SimpleValidate::checkApikeyAndDevice($apikey, $device);

        if(!$id) {
            return ResponseFormatter::error(
                null,
                'Unauthorized - Please check the apikey or accesstoken!',
                401
            );
        }

        $button_setups_id = $request->button_id;
        $timer = $request->timer;
        $jam_berapa = $request->jam_berapa;
        
        $button_setup_update = ButtonSetup::where('id', '=', $button_setups_id)->first();

        ButtonSetup::where('id', '=', $button_setups_id)->update(['timer' => $timer, 'jam_berapa' => $jam_berapa]);

        $button_setup_update = DB::table('device_inputs')
                ->join('button_setups', 'device_inputs.id', '=', 'button_setups.device_inputs_id')
                ->select('device_inputs.name', 'button_setups.*')
                ->where('button_setups.id', '=', $button_setups_id)
                ->first();

        return ResponseFormatter::success(
            $button_setup_update,
            'Device berjaya diubah.'
        ); 
    }

    public function clickButton(Request $request)
    {
        $apikey = $request->apikey;
        $device = $request->device;

        $id = SimpleValidate::checkApikeyAndDevice($apikey, $device);

        if(!$id) {
            return ResponseFormatter::error(
                null,
                'Unauthorized - Please check the apikey or accesstoken!',
                401
            );
        }

        $button_setups_id = $request->button_id;
        
        $button_setup_update = ButtonSetup::where('id', '=', $button_setups_id)->first();

        if($button_setup_update['status'] == 0) {
            ButtonSetup::where('id', '=', $button_setups_id)
            ->update(['status' => 1]);
        } else {
            ButtonSetup::where('id', '=', $button_setups_id)
            ->update(['status' => 0]);
        }

        $button_setup_update = DB::table('device_inputs')
                ->join('button_setups', 'device_inputs.id', '=', 'button_setups.device_inputs_id')
                ->select('device_inputs.name', 'button_setups.*')
                ->where('button_setups.id', '=', $button_setups_id)
                ->first();

        return ResponseFormatter::success(
            $button_setup_update,
            'Device berjaya diubah.'
        ); 
    }

    public function getAllDevice(Request $request)
    {
        $apikey = $request->apikey;

        $id = SimpleValidate::checkApikeyAndDevice($apikey);

        if(!$id) {
            return ResponseFormatter::error(
                null,
                'Unauthorized - Please check the apikey or accesstoken!',
                401
            );
        }

        $allDevice = Device::where('users_apikey', '=', $apikey)->get();

        if(count($allDevice) < 1) {
            return ResponseFormatter::error(
                null,
                'Device tidak ada',
                404
            );
        }

        return ResponseFormatter::success(
            $allDevice,
            'Device berjaya diambil.'
        ); 
    }

    public function createDevice(Request $request)
    {
        $apikey = $request->apikey;
        $device_name = $request->device_name;

        $id = SimpleValidate::checkApikeyAndDevice($apikey);

        if(!$id) {
            return ResponseFormatter::error(
                null,
                'Unauthorized - Please check the apikey or accesstoken!',
                401
            );
        }

        Device::create([
            'users_apikey' => $apikey,
            'name' => $device_name
        ]);

        $device = Device::where('users_apikey', '=', $apikey)
        ->where('name', '=', $device_name)
        ->get();

        return ResponseFormatter::success(
            $device,
            'Device berjaya dibuat.'
        );   
    }

    public function getAllDataSensor(Request $request)
    {
        $apikey = $request->input('apikey');
        $device = $request->input('device');
        $data = [];

        $id = SimpleValidate::checkApikeyAndDevice($apikey);
        
        if(!$id) {
            return ResponseFormatter::error(
                null,
                'Unauthorized - Please check the apikey or accesstoken!',
                401
            );
        }

        if($apikey && $device) {
            $id = SimpleValidate::checkApikeyAndDevice($apikey, $device);
            
            if(!$id) {
                return ResponseFormatter::error(
                    null,
                    'Unauthorized - Please check the device_id',
                    401
                );
            }

            $data_per_device = DB::table('devices')
            ->join('sensors', 'devices.id', '=', 'sensors.devices_id')
            ->select('devices.name', 'sensors.data', 'sensors.created_at')
            ->where('devices.users_apikey', '=', $apikey)
            ->where('sensors.devices_id', '=', $id)
            ->get();

            if(count($data_per_device) < 1) {
                return ResponseFormatter::error(
                    null,
                    'Data device tidak ada',
                    404
                );
            }

            $all_data = json_decode(json_encode($data_per_device), true);

            foreach($all_data as $d) {
                $post = [
                    'device_name' => $d['name'],
                    'data' => json_decode($d['data']),
                    'created_at' => $d['created_at']
                ];
                $data[] = $post;
            }

            return ResponseFormatter::success(
                $data,
                'Data device berhasil diambil'
            );            
        }

        $all_data_device = DB::table('devices')
        ->join('sensors', 'devices.id', '=', 'sensors.devices_id')
        ->select('devices.name', 'sensors.data', 'sensors.created_at')
        ->where('devices.users_apikey', '=', $apikey)
        ->get();

        if(count($all_data_device) < 1) {
            return ResponseFormatter::error(
                null,
                'Data device tidak ada',
                404
            );
        }

        $all_data = json_decode(json_encode($all_data_device), true);

        foreach($all_data as $d) {
            $post = [
                'device_name' => $d['name'],
                'data' => json_decode($d['data']),
                'created_at' => $d['created_at']
            ];
            $data[] = $post;
        }

        return ResponseFormatter::success(
            $data,
            'Data device berhasil diambil'
        );  
    }

    public function insertSensor(Request $request)
    {
        $apikey = $request->input('apikey');
        $device = $request->input('device');

        if(!$apikey || !$device) {
            return ResponseFormatter::error(
                null,
                'Unauthorized - Please check the apikey or device_id!',
                401
            );
        }

        $id = SimpleValidate::checkApikeyAndDevice($apikey, $device);

        try {

            Sensor::create([
                'devices_id' => $id['id'],
                'data' => $request->data
            ]);

            $data_sensor = Sensor::where('devices_id', $id['id'])
            ->orderBy('id', 'desc')->limit(1)->get();

            return ResponseFormatter::success(
                $data_sensor,
                'Data berhasil ditambahkan'
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

    public function createInputs(Request $request)
    {
        $apikey = $request->input('apikey');
        $device = $request->input('device');

        if(!$apikey || !$device) {
            return ResponseFormatter::error(
                null,
                'Unauthorized - Please check the apikey or device_id!',
                401
            );
        }

        $devices_id = SimpleValidate::checkApikeyAndDevice($apikey, $device);
        $inputs_id = (int) $request->input;
        $name = $request->name;

        try {
            
            DeviceInput::create([
                'devices_id' => $devices_id['id'],
                'inputs_id' => $inputs_id,
                'name' => $name
            ]);

            $device_inputs_id = json_decode(json_encode(DB::table('device_inputs')
            ->select('id')
            ->where('devices_id', '=', $devices_id)
            ->where('inputs_id', '=', $inputs_id)
            ->where('name', '=', $name)
            ->first()), true)['id'];

            if($inputs_id === 1) {
                ButtonSetup::create([
                    'device_inputs_id' => $device_inputs_id
                ]);

                $input = DB::table('device_inputs')
                ->join('button_setups', 'device_inputs.id', '=', 'button_setups.device_inputs_id')
                ->select('device_inputs.name', 'button_setups.*')
                ->where('device_inputs.id', '=', $device_inputs_id)
                ->first();
            }

            return ResponseFormatter::success(
                $input,
                'Data berhasil ditambahkan'
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

    public function getAllInput(Request $request)
    {
        $apikey = $request->input('apikey');
        $device = $request->input('device');

        if(!$apikey || !$device) {
            return ResponseFormatter::error(
                null,
                'Unauthorized - Please check the apikey or device_id!',
                401
            );
        }

        $devices_id = SimpleValidate::checkApikeyAndDevice($apikey, $device);

        $data_input = DB::table('device_inputs')
                ->join('button_setups', 'device_inputs.id', '=', 'button_setups.device_inputs_id')
                ->select('button_setups.device_inputs_id', 'button_setups.id', 'device_inputs.name', 'button_setups.timer', 'button_setups.jam_berapa', 'button_setups.status')
                ->where('device_inputs.devices_id', '=', $devices_id)
                ->get();

        if(count($data_input) < 1) {
            return ResponseFormatter::error(
                null,
                'Data device tidak ada',
                404
            );
        }

        date_default_timezone_set("Asia/Kuala_Lumpur");

        $all_time = json_decode(json_encode($data_input), true);
        
        $all_data = [

        ];

        // return $all_time;

        // return json_decode($all_time, true)['first'];
        foreach($all_time as $time) {
            $new_time = json_decode($time["jam_berapa"], true);
            $timer = json_decode($time["timer"], true);

            // return $timer;
            $current_data = [
                "device_inputs_id" => $time["device_inputs_id"],
                "id" => $time["id"],
                "name" => $time["name"],
                "timer" => $timer,
                "jam_berapa" => $new_time,
                "status" => $time["status"]
            ];

            array_push($all_data, $current_data);

            // return $new_time;
            foreach($new_time as $t) {
                if($t === date("H:i")) {
                    ButtonSetup::where('id', '=', $time["id"])->update(['status' => 1]);
                    // echo "yes";
                }
            }
        }

        // return $all_data;

        return ResponseFormatter::success(
            $all_data,
            'Data device berhasil diambil'
        );   
    }
}
