<?php

namespace App\Http\Controllers;

use App\Models\DriverLicense;
use Illuminate\Http\Request;

class DriverLicenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        /**
         * ! Validate Request
         */
        request()->validate([
            'front_image' => 'required|image|mimes:jpeg,png,jpg|max:20480',
            'back_image' => 'required|image|mimes:jpeg,png,jpg|max:20480',
            'expire_at' => 'sometimes|date',
        ]);

        /**
         * ! Upload Images
         */
        $front_image = request()->file('front_image')->store('driver_licenses');
        $back_image = request()->file('back_image')->store('driver_licenses');

        /**
         * ! Create Driver License
         */
        $data = [
            'user_id' => auth()->user()->id,
            'front_image' => $front_image,
            'back_image' => $back_image,
            'expire_at' => request()->expire_at,
        ];

        $driver_license = DriverLicense::where('verified_at', null)->first();

        if ($driver_license) {
            // delete old images
            if (file_exists(storage_path('app/' . $driver_license->front_image))) {
                unlink(storage_path('app/' . $driver_license->front_image));
            }
            if (file_exists(storage_path('app/' . $driver_license->back_image))) {
                unlink(storage_path('app/' . $driver_license->back_image));
            }
            $driver_license->update($data);
        } else {
            DriverLicense::create($data);
        }

        /**
         * ! Return Response
         */
        return response()->json([
            'message' => __('messages.success.identity'),
            'data' => $driver_license,
            'error' => null,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
        $driver_license = DriverLicense::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();
        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $driver_license,
            'error' => null,
        ]);
    }

    public function devDelete()
    {
        $userId = auth()->user()->id;
        // find on going application
        if (app()->environment('local')) {
            return DriverLicense::where('user_id', $userId)->delete();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
