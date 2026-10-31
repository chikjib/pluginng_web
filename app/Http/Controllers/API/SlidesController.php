<?php

namespace App\Http\Controllers\API;

use App\Models\Slides;
use Illuminate\Http\Request;
use App\Providers\UploadImageClass;
use App\Http\Resources\SlidesResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Storage;

class SlidesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Slides::all();

        return $this->sendResponse(SlidesResource::collection($products), 'Slides retrieved successfully.');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $category = Slides::create($input);

        return $this->sendResponse(new SlidesResource($category), 'Slides created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Slides  $slides
     * @return \Illuminate\Http\Response
     */
    public function show(Slides $slides)
    {
        //
    }

    public function update($id, Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'description' => 'required',

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $category =  Slides::find($id);
        $category->description = $input['description'];
        $category->save();

        return $this->sendResponse(new SlidesResource($category), 'slides updated successfully.');
    }



    public function uploadImage(Request $request)
    {
        // if (!is_null($input['user_image'])) {
        if ($request->file()) {
            $slides = new Slides();
            //$input = $request->all();
            $fileupload =  new UploadImageClass();
            $filename = $fileupload->uploadFile($request);
            //}
            $slides->cat_image = $filename;
            $slides->description = $request->description;

            $slides->save();
            return $this->sendResponse(new SlidesResource($slides), 'Slides updated successfully.');
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
        $category =  Slides::find($id);


        if (Storage::disk('public')->exists('category/' . $category->cat_image)) {
            Storage::disk('public')->delete('category/' . $category->cat_image);
            $category->delete();
            return $this->sendResponse([], 'Slides deleted successfully.');
        } else {
            return $this->sendError('Error', 'File not found');
        }
    }
}
