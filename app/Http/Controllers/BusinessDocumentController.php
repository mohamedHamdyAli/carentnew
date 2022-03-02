<?php

namespace App\Http\Controllers;

use App\Models\BusinessDocument;
use App\Models\TempFile;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BusinessDocumentController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
        $driver_license = BusinessDocument::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();
        return response()->json([
            'message' => __('messages.r_success'),
            'data' => $driver_license,
            'error' => null,
        ]);
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
            'name' => 'required|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
            'legal_documents' => 'nullable|array',
            'legal_documents.*' => 'sometimes|string|exists:temp_files,id',
        ]);

        $business_document = BusinessDocument::where('user_id', Auth::user()->id)->first();

        /**
         * ! Store Business Document
         */
        $data = [
            'name' => request('name'),
        ];

        // save logo if exists
        if (request('logo')) {
            $bg = Image::canvas(250, 250, array(0, 0, 0, 0))->encode('png');
            // load file from storage
            $file = request()->file('logo');
            // resize image
            $logo = Image::make($file)->resize(250, 250, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('png');
            $bg->insert($logo, 'center')->encode('png');
            $filename = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file->hashName());
            $filename .= '.png';
            Storage::disk('public')->put('agencies/logos/' . $filename, $bg);
            $data['logo'] = 'agencies/logos/' . $filename;
        }
        /**
         * ! Store Business Document Legal Documents
         */
        $legal_documents = request('legal_documents');
        $legal_documents_paths = [];

        if (request()->has('legal_documents')) {
            foreach ($legal_documents as $legal_document) {
                // move file from temp to legal documents
                $legalDocumentImage = TempFile::where('id', $legal_document)->first();
                // move the file secure business folder
                $newLegalDocumentPath = 'secure/business/' . Auth::user()->id . '/legal_documents/' . Carbon::now()->timestamp . '_' . $legalDocumentImage->name;
                Storage::copy($legalDocumentImage->path, $newLegalDocumentPath);
                $legal_documents_paths[] = $newLegalDocumentPath;
            }
        }

        if ($business_document) {
            // delete old logo
            if ($business_document->logo) {
                try {
                    unlink(storage_path('app/public/' . $business_document->logo));
                } catch (\Exception $e) {
                    // do nothing
                }
            }
            // add new legal documents & update business document
            if (is_array($business_document->legal_documents)) {
                $business_document->legal_documents = $business_document->legal_documents;
            } else {
                $business_document->legal_documents = json_decode($business_document->legal_documents);
            }

            $legal_documents_paths = array_merge($business_document->legal_documents, $legal_documents_paths) ?? $legal_documents_paths;
            $data['legal_documents'] = $legal_documents_paths;
            $business_document->update($data);
        } else {
            // add legal documents & create new business document
            $data['user_id'] = Auth::user()->id;
            $data['legal_documents'] = $legal_documents_paths;
            return $data;
            $business_document = BusinessDocument::create($data);
        }

        /**
         * ! Return Response
         */
        return response()->json([
            'message' => __('messages.created'),
            'data' => BusinessDocument::find($business_document->id),
            'error' => null,
        ]);
    }

    public function devDelete()
    {
        $userId = auth()->user()->id;
        // find on going applications and delete them
        if (app()->environment('local')) {
            return BusinessDocument::where('user_id', $userId)->delete();
        }
    }
}
