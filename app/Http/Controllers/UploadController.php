<?php

namespace App\Http\Controllers;

use App\Models\TempFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploadController extends Controller
{
    public function image()
    {
        // validate the request
        $this->validate(request(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,svg|max:20480',
            'width' => 'numeric|required_with:height,quality',
            'height' => 'numeric|required_with:width,quality',
            'quality' => 'numeric|required_with:width,height|min:70|max:100',
        ]);
        
        $file = request()->file('file');

        if (request('width') !== null and request('height') !== null) {
            $image = Image::make($file)->encode('jpg');
            $image->fit(request('width'), request('height'), function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->orientate()->encode('jpg', request('quality'));
            $path = 'temp/' . $file->hashName();
            Storage::put($path, $image);
        } else {
            $path = $file->store('temp');
        }
        $data = [
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'uploaded_at' => Carbon::now(),
        ];
        if ($path) {
            $upload = TempFile::create($data);
            return response()->json([
                'message' => __('messages.success.image_uploaded'),
                'data' => $upload,
                'error' => null,
            ]);
        } else {
            return response()->json([
                'message' => __('messages.error.image_uploaded'),
                'data' => null,
                'error' => true,
            ], 400);
        }
    }
}
