<?php

namespace App\Http\Controllers;

use App\Models\IdentityDocument;
use Illuminate\Http\Request;

class IdentityDocumentController extends Controller
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
        ]);

        /**
         * ! Upload Images
         */
        $front_image = request()->file('front_image')->store('identity_documents');
        $back_image = request()->file('back_image')->store('identity_documents');

        /**
         * ! Create Driver License
         */
        $data = [
            'user_id' => auth()->user()->id,
            'front_image' => $front_image,
            'back_image' => $back_image,
        ];

        $identity_document = IdentityDocument::where('verified_at', null)->first();

        if ($identity_document) {
            // delete old images
            if (file_exists(storage_path('app/' . $identity_document->front_image))) {
                unlink(storage_path('app/' . $identity_document->front_image));
            }
            if (file_exists(storage_path('app/' . $identity_document->back_image))) {
                unlink(storage_path('app/' . $identity_document->back_image));
            }
            $identity_document->update($data);
        } else {
            IdentityDocument::create($data);
        }

        /**
         * ! Return Response
         */
        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $identity_document,
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
        $driver_license = IdentityDocument::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();
        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $driver_license,
            'error' => null,
        ]);
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

    public function devDelete()
    {
        $userId = auth()->user()->id;
        // find on going application
        if (app()->environment('local')) {
            return IdentityDocument::where('user_id', $userId)->delete();
        }
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
