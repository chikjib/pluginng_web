<?php

namespace App\Http\Controllers\API;

use App\Models\Setting;

use Illuminate\Http\Request;
use App\Http\Resources\SettingResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserSettingResource;
use App\Traits\IntegrationsTrait;

use App\Http\Controllers\API\BaseController as BaseController;

class SettingController extends BaseController
{
    use IntegrationsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Setting::all();

        return $this->sendResponse(SettingResource::collection($products), 'Settings retrieved successfully.');
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
            'key' => 'required',
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $category = Setting::create($input);

        return $this->sendResponse(new SettingResource($category), 'Setting created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Setting::find($id);

        if (is_null($category)) {
            return $this->sendError('Category not found.');
        }

        return $this->sendResponse(new SettingResource($category), 'Setting retrieved successfully.');
    }
    
    public function updateOurToken()
    {
        $tokens = $this->getMtnToken();
        \Log::info("Tokens");
        \Log::info($tokens);
        $mtn_sponsor_num = $tokens['data'][0]['value'];
        $mtn_token = $tokens['data'][1]['value'];
        $mtn_sponsor_num_cg = $tokens['data'][2]['value'];
        $mtn_token_cg = $tokens['data'][3]['value'];
        $mtn_access_token = $tokens['data'][4]['value'];
        $mtn_refresh_token = $tokens['data'][5]['value'];
        $mtn_app_sponsor_num = $tokens['data'][6]['value'];
        $mtn_cookie = $tokens['data'][7]['value'];

        Setting::where('id',13)->update(['value' => $mtn_sponsor_num]);
        Setting::where('id',14)->update(['value' => $mtn_token]);
        //Setting::where('id',15)->update(['value' => $mtn_sponsor_num_cg]);
        //Setting::where('id',16)->update(['value' => $mtn_token_cg]);
        Setting::where('id',17)->update(['value' => $mtn_access_token]);
        Setting::where('id',18)->update(['value' => $mtn_refresh_token]);
        Setting::where('id',19)->update(['value' => $mtn_app_sponsor_num]);
        //Setting::where('id',20)->update(['value' => $mtn_cookie]);

        return $this->sendResponse("Setting retrieved successfully", 'Setting retrieved successfully.');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'key' => 'required',
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $category =  Setting::find($id);
        $category->key = $input['key'];
        $category->value = $input['value'];
        $category->save();

        return $this->sendResponse(new SettingResource($category), 'Setting updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category =  Setting::find($id);
        $category->delete();

        return $this->sendResponse([], 'Setting deleted successfully.');
    }
}
