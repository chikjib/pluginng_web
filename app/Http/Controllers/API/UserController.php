<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Bucket;
use App\Models\BucketOrder;

use App\Models\Order;
use App\Models\Setting;
use App\Models\Withdrawal;
use App\Models\Commissions;
use App\Models\Subcategory;
use App\Traits\WalletTrait;
use Illuminate\Http\Request;
use App\Providers\UploadClass;
use App\Traits\ReferenceTrait;
use App\Providers\UploadImageClass;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Traits\IntegrationsTrait;

function array_recursive_search_key_map($needle,$haystack){
    foreach($haystack as $first_level_key=>$value){
        if($needle === $value){
            return array($first_level_key);
        }elseif(is_array($value)){
            $callback = array_recursive_search_key_map($needle,$value);
            if($callback){
                return array_merge(array($first_level_key),$callback);
            }
        }
    }
}

class UserController extends BaseController
{
    use WalletTrait;
    use ReferenceTrait;
    use IntegrationsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function loadHomePage()
    {

        $success['transactions'] =  Order::where('user_id', $this->user->id)->count();

        $success['balance'] =  $this->user->wallet;
        $success['userlevel'] =  $this->user->userlevel;
        $success['webhook_url'] = $this->user->webhook_url;

        $user =  User::find($this->user->id);
        $today = date("Y-m-d");
        $db_date = date("Y-m-d", strtotime($user->bvn_identify_date));

        if ($db_date != $today && $user->bvn_attempts_count == 2) {
            $user->bvn_attempts_count = 0;
            $user->save();
        }

        $success['bvn'] =  $this->user->bvn;
        $success['nin'] =  $this->user->nin;

        if(!is_null($user->reserved_acct)){
            $reserved = json_decode($user->reserved_acct,true);
            // extract($reserved);
            // if()

            $sterling_reserved_array = array_recursive_search_key_map("Sterling bank",$reserved);
            $wema_reserved_array = array_recursive_search_key_map("Wema bank",$reserved);
            $moniepoint_reserved_array = array_recursive_search_key_map("Moniepoint Microfinance Bank",$reserved);
            $gtbank_reserved_array = array_recursive_search_key_map("GT Bank",$reserved);
            // $providus_reserved_array = array_recursive_search_key_map("Providus Bank",$reserved);
            $rehoboth_reserved_array = array_recursive_search_key_map("Rehoboth Bank",$reserved);
            \Log::info($reserved);

            // if(is_array($wema_reserved_array)){
            //     \Log::info($wema_reserved_array[0]);
            //     $key1 = $wema_reserved_array[0];
            //     $sterling = $reserved[$key1];
            //     \Log::info($sterling);

            // }
            if(is_array($sterling_reserved_array)){
                \Log::info($sterling_reserved_array[0]);
                $key1 = $sterling_reserved_array[0];
                $sterling = $reserved[$key1];

            }else{
                $not_array = "not array";
                \Log::info($not_array);
            }

            if(is_array($wema_reserved_array)){
                \Log::info($wema_reserved_array[0]);
                $key2 = $wema_reserved_array[0];
                $wema = $reserved[$key2];
                $user->wema_reserved_acct = json_encode($wema);

            }else{
                $not_array = "not array";
                \Log::info($not_array);
            }

            if(is_array($moniepoint_reserved_array)){
                \Log::info($moniepoint_reserved_array[0]);
                $key3 = $moniepoint_reserved_array[0];
                $moniepoint = $reserved[$key3];
                $user->moniepoint_reserved_acct = json_encode($moniepoint);

            }else{
                $not_array = "not array";
                \Log::info($not_array);
            }

            if(is_array($gtbank_reserved_array)){
                \Log::info($gtbank_reserved_array[0]);
                $key4 = $gtbank_reserved_array[0];
                $gtbank = $reserved[$key4];
                $user->gtbank_reserved_acct = json_encode($gtbank);
            }else{
                $not_array = "not array";
                \Log::info($not_array);
            }

            // if(is_array($providus_reserved_array)){
            //     \Log::info($providus_reserved_array[0]);
            //     $key5 = $providus_reserved_array[0];
            //     $providus = $reserved[$key5];
            //     $user->providus_reserved_acct = json_encode($providus);

            // }else{
            //     $not_array = "not array";
            //     \Log::info($not_array);
            // }

            if(is_array($rehoboth_reserved_array)){
                \Log::info($rehoboth_reserved_array[0]);
                $key6 = $rehoboth_reserved_array[0];
                $rehoboth = $reserved[$key6];
                $user->rehoboth_reserved_acct = json_encode($rehoboth);

            }else{
                $not_array = "not array";
                \Log::info($not_array);
            }


            $user->reserved_acct = NULL;
            $user->save();

        }

        $success['wema_reserved_acct'] = json_decode($user->wema_reserved_acct);
        $success['moniepoint_reserved_acct'] = json_decode($user->moniepoint_reserved_acct);
        $success['gtbank_reserved_acct'] = json_decode($user->gtbank_reserved_acct);
        // $success['providus_reserved_acct'] = json_decode($user->providus_reserved_acct);
        $success['rehoboth_reserved_acct'] = json_decode($user->rehoboth_reserved_acct);
        $success['palmpay_reserved_acct'] = json_decode($user->palmpay_reserved_acct);
        
        $success['card_charge'] = Setting::where('key', 'MONNIFY_CARD_BANK_CHARGE')->first()->value;


        // $success['totalCommission'] =  Order::where('user_id', $this->user->id)->where('subcategory_id', 23)->sum('amount') ?? 0;
        $success['totalCommission'] =  $this->user->commission;

        $success['levelPackage'] = Subcategory::where('title', 'Upgrade')->first()->products;
        $success['vaccount_desc'] = Subcategory::where('title', 'ReservedAcct')->first()->description2;
        $success['ref_msg'] = 'Refer and Earn ' . $this->getUserLevel(json_decode(Subcategory::where('title', 'Bonus')->first()->products), $this->user->userlevel) . '% from every successful data transaction from your downline';

        $success['notice'] = Setting::where('key', 'NOTICE')->first()->value;
        $success['footer'] = Setting::where('key', 'FOOTER')->first()->value;

        return $this->sendResponse($success, 'User login successfully.');
    }


    public function usersettings()
    {

        // $result['rave_publickey'] = env('MIX_RAVE_PUBLICKEY');
        // $result['rave_enc'] = env('RAVE_ENC');
        // $result['rave_secretkey'] = env('RAVE_CLIENT_SECRET');
        $result['monnify_apikey'] = config('app.monnifyAPIKey');
        $result['monnify_contractCode'] = config('app.monnifyContractCode');
        $result['monnify_live'] = config('app.monnifyLive');
        $result['card_charge'] = Setting::where('key', 'MONNIFY_CARD_BANK_CHARGE')->first()->value;




        $result['bonus'] = $this->user->bonus();
        $result['fundmin'] = $this->user->fundmin();
        $result['fundmax'] = $this->user->fundmax();

        return $this->sendResponse($result, 'Settings retrieved successfully.');
    }



    public function index(Request $request)
    {

        $search = $request->input('search');
        $role = $request->input('role');
        
        $admin_user = User::find($this->user->id);
        if ($admin_user->role == 1) {

        if ($search) {
            $data =  User::where('firstname', 'like', '%' . $search . '%')
                ->orWhere('lastname', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->paginate(2);
        } else if (!is_null($role)) {
            $data = User::where('role', $role)->get();
        } else {
            $data = User::orderBy('created_at', 'desc')->paginate(20);
        }



        $products = UserResource::collection($data);

        return $products;
        }else{
            return $this->sendError("Processing failed!");
            
        }

        
        // return $this->sendResponse(UserResource::collection($products), 'Users retrieved successfully.');
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
            'firstname' => 'required',
            'lastname' => 'required',
            'password' => 'required',
            'email' => 'required',
            //     'address' => 'required',
            'phone' => 'required',
            'role' => 'required',
            //   'gender' => 'required',

        ]);


        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if ($request->file()) {
            $fileupload =  new UploadImageClass();
            $filename = $fileupload($request);
            $input['user_image'] = $filename;
        }
        $input['password'] = bcrypt($input['password']);

        $product = User::create($input);

        return $this->sendResponse(new UserResource($product), 'User created successfully.');
    }

    public function change_password(Request $request)
    {
        $input = $request->all();
        $userid = Auth::guard('api')->user()->id;
        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                if ((Hash::check(request('old_password'), Auth::user()->password)) == false) {
                    $arr = array("status" => 400, "message" => "Check your old password.", "data" => array());
                } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
                    $arr = array("status" => 400, "message" => "Please enter a password which is not similar then current password.", "data" => array());
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                    $arr = array("status" => 200, "message" => "Password updated successfully.", "data" => array());
                }
            } catch (\Exception $ex) {
                $msg = $ex->getMessage();

                $arr = array("status" => 400, "message" => $msg, "data" => array());
            }
        }
        // return \Response::json($arr);

        return $this->sendResponse($arr, 'User updated successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = User::find($id);

        if (is_null($product)) {
            return $this->sendError('User not found.');
        }

        return $this->sendResponse(new UserResource($product), 'User retrieved successfully.');
    }

    public function generateApiKey()
    {

        $user = User::find($this->user->id);

        $token =  $user->createToken('pluginng')->accessToken;

        $user->userToken =  $token;
        $user->save();
        return $this->sendResponse(new UserResource($user), 'User api generated successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function uploadImage($id, Request $request)
    {
        // if (!is_null($input['user_image'])) {
        if ($request->file()) {
            $product = User::find($id);
            $input = $request->all();
            $fileupload =  new UploadImageClass();
            $filename = $fileupload->upload($request);
            //}
            $product->user_image = $filename;

            $product->save();
            return $this->sendResponse(new UserResource($product), 'User updated successfully.');
        }
    }
    
    public function getTotalWallet(Request $request)
    { 
        $users = User::sum('wallet');
        return $this->sendResponse($users,"Retrieved successfully");
    }


    public function updateWallet(Request $request)
    {

        $subcategory =  Subcategory::where('title', 'Manual')->first();

        if (is_null($request->user_id)) {
            return $this->sendError('User not found.', 'User not found.');
        }

        $admin_user = User::find($this->user->id);
        \Log::info($admin_user);
        if($admin_user->role == 1){
             if ($request->type == "credit") {
                $prev = User::find($request->user_id)->wallet;

                if ($this->isCredited($request->amount, $request->user_id)) {

                    $bal = $prev + $request->amount;
                    $order = new Order();
                    $order->ref = $this->referenceCode();
                    $order->user_id = $request->user_id;
                    $order->subcategory_id = $subcategory->id;
                    $order->plan = $subcategory->title;
                    $order->amount = $request->amount;
                    $order->quantity = 1;
                    $order->channel = is_null($request->channel) ? 'Web' : 'App';

                    $order->subtotal = $request->amount;
                    $order->total = $request->amount;
                    $order->bal = $bal;
                    $order->prev_bal = $prev;
                    $order->description = $subcategory->title . " System Credited ";

                    $order->status = 1;
                    $order->save();
                    return $this->sendResponse("Wallet credited successfully.", 'Wallet credited successfully.');
                } else {
                    return $this->sendError('Invalid amount specified', 'Invalid amount specified');
                }
            }

            if ($request->type == "debit") {
                $prev = User::find($request->user_id)->wallet;
                if ($this->isDebited($request->amount, $request->user_id)) {
                    $bal = $prev - $request->amount;

                    $order = new Order();
                    $order->ref = $this->referenceCode();
                    $order->user_id = $request->user_id;
                    $order->subcategory_id = $subcategory->id;
                    $order->plan = $subcategory->title;
                    $order->amount = $request->amount;
                    $order->quantity = 1;
                    $order->subtotal = $request->amount;
                    $order->total = $request->amount;
                    $order->bal = $bal;
                    $order->channel = is_null($request->channel) ? 'Web' : 'App';

                    $order->prev_bal = $prev;
                    $order->description = $subcategory->title . " System Debited";
                    $order->status = 1;
                    $order->save();
                    return $this->sendResponse("Wallet debited successfully.", 'Wallet debited successfully.');
                } else {
                    return $this->sendError('Invalid amount specified', 'Invalid amount specified');
                }
            }

            if ($request->type == "credit_bucket") {
                $bucket = Bucket::findOrFail($request->bucket_id);


                $wallet_name = $this->getBucketTitle($bucket->id);

                $user = User::find($request->user_id);

                $data_bundles = collect(json_decode($bucket->price_per_gb, true));
                $data_size = $request->data_size;
                $matching_bundle = $data_bundles->first(function ($bundle) use ($data_size) {
                    \Log::info($bundle['lower_limit']);
                    return $data_size >= $bundle['lower_limit'] && $data_size <= $bundle['upper_limit'];
                });

                $prev_data_wallet_bal = $user->$wallet_name;
                $data_wallet_bal = $user->$wallet_name + $data_size;


                $amountActual = $data_size * $matching_bundle['price'];

                if ($this->IncrementBucket($wallet_name, $user->id, $data_size)) {
                    $bucket_order = new BucketOrder();
                    $bucket_order->ref = $this->referenceCode();
                    ;
                    // $order->custom_reference = $custom_reference;
                    $bucket_order->user_id = $user->id;
                    $bucket_order->bucket_id = $bucket->id;
                    $bucket_order->plan = $bucket->title . " Bucket Purchase of " . $data_size . "GB " . "System Credited";
                    $bucket_order->amount = $amountActual;
                    $bucket_order->quantity = 1;
                    $bucket_order->subtotal = $amountActual;
                    $bucket_order->total = $amountActual;
                    $bucket_order->channel = is_null($request->channel) ? 'Web' : 'App';
                    $bucket_order->data_size = $data_size * 1000;
                    $bucket_order->bucket_bal = $data_wallet_bal;
                    $bucket_order->prev_bucket_bal = $prev_data_wallet_bal;
                    $bucket_order->description = $bucket->title . " Bucket Purchase of " . $data_size . "GB " . "System Credited";
                    $bucket_order->status = 1;
                    $bucket_order->save();


                    $order = new Order();
                    $order->ref = $this->referenceCode();
                    // $order->custom_reference = $custom_reference;
                    $order->user_id = $user->id;
                    $order->subcategory_id = Subcategory::where('title', $bucket->title)->first()->id;
                    $order->plan = $bucket->title . " Bucket Purchase of " . $data_size . "GB " . "System Credited";
                    $order->amount = $amountActual;
                    $order->quantity = 1;
                    $order->subtotal = $amountActual;
                    $order->total = $amountActual;
                    $order->channel = is_null($request->channel) ? 'Web' : 'App';
                    $order->data_size = $data_size * 1000;
                    $order->bal = $user->wallet;
                    $order->prev_bal = $user->wallet;
                    $order->description = $bucket->title . " Bucket Purchase of " . $data_size . "GB " . "System Credited";
                    $order->status = 1;
                    $order->save();

                    return $this->sendResponse("Bucket Wallet credited successfully.", 'Bucket Wallet credited successfully.');

                }

            }

            if ($request->type == "debit_bucket") {
                $bucket = Bucket::findOrFail($request->bucket_id);


                $wallet_name = $this->getBucketTitle($bucket->id);
                $user = User::find($request->user_id);
                $data_size = $request->data_size;

                $prev_data_wallet_bal = $user->$wallet_name;
                $data_wallet_bal = $user->$wallet_name - $data_size;


                if ($this->DecrementBucket($wallet_name, $user->id, $data_size)) {
                    $bucket_order = new BucketOrder();
                    $bucket_order->ref = $this->referenceCode();
                    ;
                    // $order->custom_reference = $custom_reference;
                    $bucket_order->user_id = $user->id;
                    $bucket_order->bucket_id = $bucket->id;
                    $bucket_order->plan = $bucket->title . " of " . $data_size . "GB " . "System Debited";
                    $bucket_order->amount = $data_size;
                    $bucket_order->quantity = 1;
                    $bucket_order->subtotal = $data_size;
                    $bucket_order->total = $data_size;
                    $bucket_order->channel = is_null($request->channel) ? 'Web' : 'App';
                    $bucket_order->data_size = $data_size * 1000;
                    $bucket_order->bucket_bal = $data_wallet_bal;
                    $bucket_order->prev_bucket_bal = $prev_data_wallet_bal;
                    $bucket_order->description = $bucket->title . " of " . $data_size . "GB " . "System Debited";
                    $bucket_order->status = 1;
                    $bucket_order->save();


                    $order = new Order();
                    $order->ref = $this->referenceCode();
                    // $order->custom_reference = $custom_reference;
                    $order->user_id = $user->id;
                    $order->subcategory_id = Subcategory::where('title', $bucket->title)->first()->id;
                    $order->plan = $bucket->title . " " . $data_size . "GB " . "System Debited";
                    $order->amount = $data_size;
                    $order->quantity = 1;
                    $order->subtotal = $data_size;
                    $order->total = $data_size;
                    $order->channel = is_null($request->channel) ? 'Web' : 'App';
                    $order->data_size = $data_size * 1000;
                    $order->bal = $user->wallet;
                    $order->prev_bal = $user->wallet;
                    $order->description = $bucket->title . " " . $data_size . "GB " . "System Debited";
                    $order->status = 1;
                    $order->save();

                    return $this->sendResponse("Bucket Wallet debited successfully.", 'Bucket Wallet debited successfully.');

                }

            }
        }else {
            return $this->sendError("Processing failed!");
        }

        return $this->sendError('Type not defined', 'Type not defined');
    }



    public function transferBonus(Request $request)
    {

        $subcategory =  Subcategory::where('title', 'Bonus')->first();

        if (is_null($this->user->id)) {
            return $this->sendError('User not found.', 'User not found.');
        }
        $user = User::find($this->user->id);

        if ($user->commission <= 0) {
            return $this->sendError('No Bonus to transfer', 'No Bonus to transfer');
        }

        if ($user->commission < $request->amount) {
            return $this->sendError('You cannot transfer above N' . $user->commission, 'You cannot transfer above N' . $user->commission);
        }

        $prev = $user->wallet;

        if ($this->isCredited($request->amount, $this->user->id)) {

            $bal =  $prev + $request->amount;
            $order = new Order();
            $order->ref = $this->referenceCode();
            $order->user_id =  $this->user->id;
            $order->subcategory_id =  $subcategory->id;
            $order->plan =  $subcategory->title;
            $order->amount = $request->amount;
            $order->quantity =  1;
            $order->subtotal =  $request->amount;
            $order->total = $request->amount;
            $order->channel = is_null($request->channel) ? 'Web' : 'App';

            $order->bal = $bal;
            $order->prev_bal = $prev;
            $order->description = $subcategory->title . " Ref Bonus Transfer";

            $order->status = 1;
            $order->save();

            $user->commission =  $user->commission - $request->amount;
            $user->save();
            return $this->sendResponse("Wallet credited successfully.", 'Wallet credited successfully.');
        } else {
            return $this->sendError('Invalid amount specified', 'Invalid amount specified');
        }
    }


    public function updateLevel(Request $request)
    {

        $subcategory =  Subcategory::where('title', 'Upgrade')->first();

        if (is_null($this->user->id)) {
            return $this->sendError('User not found.', 'User not found.');
        }

        $user =  User::find($this->user->id);
        //$admin_user = User::find($this->user->id);
        //\Log::info($admin_user);
        //if($admin_user->role == 1){
            
        if ($user->userlevel ==  $request->userlevel) {
            return $this->sendError('User is already on this level.', 'User already on specified level and cannot be downgraded');
        }
        $prev = User::find($user->id)->wallet;


        $productCollection = collect(json_decode($subcategory->products));

        if ($request->has('levelName')) {
            $amount = $productCollection->where('title', $request->levelName)->first()->amount;
        } else {
            $amount = $productCollection->values()->get($request->userlevel)->amount;
        }


        if ($this->isDebited($amount, $user->id)) {
            $bal =  $prev - $amount;

            $order = new Order();
            $order->ref = $this->referenceCode();
            $order->user_id =  $user->id;
            $order->subcategory_id =  $subcategory->id;
            $order->plan =  $subcategory->title;
            $order->amount = $amount;
            $order->quantity =  1;
            $order->subtotal =  $amount;
            $order->total = $amount;
            $order->channel = is_null($request->channel) ? 'Web' : 'App';

            $order->bal = $bal;
            $order->prev_bal = $prev;
            $order->description = $subcategory->title . " System Debited";
            $order->status = 1;
            $order->save();

            $user->userlevel = $request->userlevel;
            $user->save();
            return $this->sendResponse("Level upgraded successfully.", 'Level upgraded successfully.');
        } else {
            return $this->sendError('insufficient funds in wallet', 'insufficient funds in wallet');
        }
        //}else {
        //    return $this->sendError("Processing failed!");
        //}
    }
    
    public function updateLevelAdmin(Request $request)
    {

        $subcategory =  Subcategory::where('title', 'Upgrade')->first();

        if (is_null($request->user_id)) {
            return $this->sendError('User not found.', 'User not found.');
        }

        $user =  User::find($request->user_id);
        
            
        if ($user->userlevel ==  $request->userlevel) {
            return $this->sendError('User is already on this level.', 'User already on specified level and cannot be downgraded');
        }
        $prev = User::find($request->user_id)->wallet;


        $productCollection = collect(json_decode($subcategory->products));

        if ($request->has('levelName')) {
            $amount = $productCollection->where('title', $request->levelName)->first()->amount;
        } else {
            $amount = $productCollection->values()->get($request->userlevel)->amount;
        }


        if ($this->isDebited($amount, $request->user_id)) {
            $bal =  $prev - $amount;

            $order = new Order();
            $order->ref = $this->referenceCode();
            $order->user_id =  $request->user_id;
            $order->subcategory_id =  $subcategory->id;
            $order->plan =  $subcategory->title;
            $order->amount = $amount;
            $order->quantity =  1;
            $order->subtotal =  $amount;
            $order->total = $amount;
            $order->channel = is_null($request->channel) ? 'Web' : 'App';

            $order->bal = $bal;
            $order->prev_bal = $prev;
            $order->description = $subcategory->title . " System Debited";
            $order->status = 1;
            $order->save();

            $user->userlevel = $request->userlevel;
            $user->save();
            return $this->sendResponse("Level upgraded successfully.", 'Level upgraded successfully.');
        } else {
            return $this->sendError('insufficient funds in wallet', 'insufficient funds in wallet');
        }
       
    }

    // public function update($id, Request $request)
    // {
    //     $admin_user = User::find($this->user->id);
    //     if ($admin_user->role == 1) {

    //         $product = User::find($id);
    //         $input = $request->all();
    //         $product->update($input);
    //         return $this->sendResponse(new UserResource($product), 'User updated successfully.');
    //     }else {
    //         return $this->sendError("Processing failed!");
    //     }
    // }
    
    public function update(Request $request, $id)
    {
        if ($id != auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
        ]);
    
        $user = auth()->user();
    
        $user->firstname = $data['firstname'];
        $user->lastname = $data['lastname'];
        $user->email = $data['email'];
        $user->bank_name = $data['bank_name'] ?? null;
        $user->account_number = $data['account_number'] ?? null;
    
        $user->save();
    
        return $this->sendResponse(new UserResource($user), 'User updated successfully.');
    } 
    
    

    public function getReservedAccount()
    {

        $result = $this->user->getReservedAccount();

        if ($result->requestSuccessful) {
            $user =  User::find($this->user->id);
            $user->reserved_acct = json_encode($result->responseBody->accounts);
            $user->save();
            return $user;
        }
    }

    public function createNewReservedAccount()
    {
        $result = $this->user->generate_reserved_account();

        if ($result->requestSuccessful) {
            $user =  User::find($this->user->id);
            $user->reserved_acct = json_encode($result->responseBody->accounts);

            $user->save();
            return $user;
        }
    }


    public function generateSquadCo($bvn)
    {
        $result = $this->squadcoGT($this->user->email, $this->user->firstname, $this->user->lastname, $this->user->phone,$bvn);
        //die(json_encode($result));
        \Log::info("Generate SquadCo");
        \Log::info($result);
        
        return $result;
    }
    
    
    public function generatePalmpay($licenseNumber, $identityType = 'personal')
    {
        $customer_name = $this->user->firstname . " " . $this->user->lastname;
        return $this->palmPay(
            $this->referenceCode(), $customer_name, $this->user->email,
            $licenseNumber, $identityType
        );
    }

    protected function createPalmpayReservedAccount($user)
    {
        if (!empty($user->palmpay_reserved_acct)) {
            return $this->sendResponse(new UserResource($user), 'User account already reserved.');
        }

        // Prefer BVN when both verified identifiers are available.
        $identityType = !empty($user->bvn) ? 'personal' : 'personal_nin';
        $encryptedIdentity = !empty($user->bvn) ? $user->bvn : $user->nin;
        if (empty($encryptedIdentity)) {
            return $this->sendError('Please verify your BVN or NIN before creating a PalmPay account.', [], 422);
        }

        $licenseNumber = openssl_decrypt(
            $encryptedIdentity, "AES-128-CTR", "SWIFtlInk1nG", 0, 'SWiftLiNknG89101'
        );
        if (!is_string($licenseNumber) || !preg_match('/^[0-9]{11}$/', $licenseNumber)) {
            return $this->sendError('Your saved BVN or NIN is invalid. Please verify it again.', [], 422);
        }

        try {
            $palmpay = $this->generatePalmpay($licenseNumber, $identityType);
        } catch (\Exception $exception) {
            return $this->sendError('PalmPay is unavailable at the moment. Please try again later.', [], 502);
        }

        if (($palmpay['respCode'] ?? null) !== '00000000'
            || ($palmpay['status'] ?? false) !== true
            || empty($palmpay['data']['virtualAccountNo'])
            || empty($palmpay['data']['virtualAccountName'])) {
            return $this->sendError('Error occurred creating your PalmPay account. Please try again later.', [], 502);
        }

        $user->palmpay_reserved_acct = json_encode([
            'bankCode' => '',
            'bankName' => 'Palmpay',
            'accountNumber' => $palmpay['data']['virtualAccountNo'],
            'accountName' => $palmpay['data']['virtualAccountName'],
        ]);
        $user->palmpay_account = $palmpay['data']['virtualAccountNo'];
        $user->save();

        return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');
    }

    protected function formatPhoneNumber($phone)
    {
        // Remove spaces
        $phone = str_replace(' ', '', $phone);

        // Replace starting 234 or +234 with 0
        if (strpos($phone, '234') === 0) {
            $phone = '0' . substr($phone, 3);
        } elseif (strpos($phone, '+234') === 0) {
            $phone = '0' . substr($phone, 4);
        }

        // Return the formatted phone number
        return $phone;
    }

        public function generate_reserved_account(Request $request)
    {
        $bank = $request->bank;
        // \Log::info($bank);
        $user =  User::find($this->user->id);

        if ($bank === 'palmpay') {
            return $this->createPalmpayReservedAccount($user);
        }

        if(!is_null($this->user->bvn) && is_null($this->user->nin)){
            $bvn=openssl_decrypt($this->user->bvn, "AES-128-CTR",
                "SWIFtlInk1nG", 0, 'SWiftLiNknG89101');
            \Log::info($bvn);

            if($bank == "gtbank"){
                $squadco = $this->generateSquadCo($bvn);
                if($squadco['status'] == 200){
                    $gtbank_account = array("bankCode" => $squadco['data']['bank_code'], "bankName" => "GT Bank", "accountNumber" => $squadco['data']['virtual_account_number'], "accountName" => $squadco['data']['first_name'] . ' ' . $squadco['data']['last_name']);
                    $user->gtbank_reserved_acct = json_encode($gtbank_account);
                    $user->save();

                    return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');

                }else {
                    return $this->sendError('Account not available at the moment, please try other banks','Account not available at the moment, please try other banks');
                }
            }elseif($bank == "rehoboth"){
                $rehoboth = $this->user->generateRehoboth($bvn);
                if ($rehoboth->error == false) {
                    $rehoboth_account = array("bankCode" => "", "bankName" => $rehoboth->Virtual->BankName, "accountNumber" => $rehoboth->Virtual->AccountNumber, "accountName" => $rehoboth->Virtual->AccountName);
                    $user->rehoboth_reserved_acct = json_encode($rehoboth_account);
                    $user->rehoboth_account = $rehoboth->Virtual->AccountNumber;
                    $user->save();
                    return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');
                }else{
                    return $this->sendError('Account not available at the moment, please try other banks','Account not available at the moment, please try other banks');
                }
            }elseif($bank == "wema"){
                $wema = $this->user->generate_reserved_account($bvn,"",["035"]);
                if($wema->responseCode == "99"){
                    $wema = $this->user->getReservedAccount();
                    $wema = json_decode(json_encode($wema->responseBody->accounts),true);
                    
                    $wema_reserved_array = array_recursive_search_key_map("Wema bank",$wema);

                    if(is_array($wema_reserved_array)){
                        \Log::info($wema_reserved_array[0]);
                        $key2 = $wema_reserved_array[0];
                        $wema = $wema[$key2];
                        $user1 =  User::find($this->user->id);
                        $user1->wema_reserved_acct = json_encode($wema);
                        $user1->save();
                    }
                    

                }else{
                    $user2 =  User::find($this->user->id);
                    $user2->wema_reserved_acct = json_encode($wema->responseBody->accounts);
                    $user2->save();
                }
                
                return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');

            }elseif($bank == "moniepoint"){
                $moniepoint = $this->user->generate_reserved_account($bvn,"",["50515"]);

                if($moniepoint->responseCode == "99"){
                    $moniepoint = $this->user->getReservedAccount();

                    $moniepoint = json_decode(json_encode($moniepoint->responseBody->accounts),true);

                    $moniepoint_reserved_array = array_recursive_search_key_map("Moniepoint Microfinance Bank",$moniepoint);

                    if(is_array($moniepoint_reserved_array)){
                        $key2 = $moniepoint_reserved_array[0];
                        $moniepoint = $moniepoint[$key2];
                        $user1 = User::find($this->user->id);
                        $user1->moniepoint_reserved_acct = json_encode($moniepoint);
                        $user1->save();
                    }
                    

                }else{
                    $user2 =  User::find($this->user->id);
                    $user2->moniepoint_reserved_acct = json_encode($moniepoint->responseBody->accounts);
                    $user2->save();
                }

                
                return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');
            }
        }elseif(!is_null($this->user->nin) && is_null($this->user->bvn)){
            $nin=openssl_decrypt($this->user->nin, "AES-128-CTR",
                "SWIFtlInk1nG", 0, 'SWiftLiNknG89101');

                if($bank == "wema"){
                    $wema = $this->user->generate_reserved_account("",$nin,["035"]);
                    if($wema->responseCode == "99"){
                        $wema = $this->user->getReservedAccount();
                        $wema = json_decode(json_encode($wema->responseBody->accounts),true);

                        $wema_reserved_array = array_recursive_search_key_map("Wema bank",$wema);

                        if(is_array($wema_reserved_array)){
                            \Log::info($wema_reserved_array[0]);
                            $key2 = $wema_reserved_array[0];
                            $wema = $wema[$key2];
                            $user1 =  User::find($this->user->id);
                            $user1->wema_reserved_acct = json_encode($wema);
                            $user1->save();
                        }
                        // $moniepoint_reserved_array = array_recursive_search_key_map("Moniepoint Microfinance Bank",$reserved);

                    }else{
                        $user2 =  User::find($this->user->id);
                        $user2->wema_reserved_acct = json_encode($wema->responseBody->accounts);
                        $user2->save();

                    }
                    
                    return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');

                }elseif($bank == "moniepoint"){
                    $moniepoint = $this->user->generate_reserved_account("",$nin,["50515"]);
                    if($moniepoint->responseCode == "99"){
                        $moniepoint = $this->user->getReservedAccount();

                        $moniepoint = json_decode(json_encode($moniepoint->responseBody->accounts),true);

                        $moniepoint_reserved_array = array_recursive_search_key_map("Moniepoint Microfinance Bank",$moniepoint);



                        if(is_array($moniepoint_reserved_array)){
                            $key2 = $moniepoint_reserved_array[0];
                            $moniepoint = $moniepoint[$key2];
                            
                            $user1 = User::find($this->user->id);
                            $user1->moniepoint_reserved_acct = json_encode($moniepoint);
                            $user1->save();

                        }

                    }else{
                        $user2 = User::find($this->user->id);
                        $user2->moniepoint_reserved_acct = json_encode($moniepoint->responseBody->accounts);
                        $user2->save();
                    }
                    
                    return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');

                }elseif($bank == "rehoboth"){
                    $rehoboth = $this->user->generateRehoboth($nin);
                    if ($rehoboth->error == false) {
                        $rehoboth_account = array("bankCode" => "", "bankName" => $rehoboth->Virtual->BankName, "accountNumber" => $rehoboth->Virtual->AccountNumber, "accountName" => $rehoboth->Virtual->AccountName);
                        $user->rehoboth_reserved_acct = json_encode($rehoboth_account);
                        $user->rehoboth_account = $rehoboth->Virtual->AccountNumber;
                        $user->save();
                        return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');
                    }else{
                        return $this->sendError('Account not available at the moment, please try other banks','Account not available at the moment, please try other banks');
                    }
                }
        }elseif(is_null($this->user->nin) && is_null($this->user->bvn)){

            if($bank == "rehoboth"){
                $rehoboth = $this->user->generateRehoboth("");
                if ($rehoboth->error == false) {
                    $rehoboth_account = array("bankCode" => "", "bankName" => $rehoboth->Virtual->BankName, "accountNumber" => $rehoboth->Virtual->AccountNumber, "accountName" => $rehoboth->Virtual->AccountName);
                    $user->rehoboth_reserved_acct = json_encode($rehoboth_account);
                    $user->rehoboth_account = $rehoboth->Virtual->AccountNumber;
                    $user->save();
                    return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');
                }else{
                    return $this->sendError('Account not available at the moment, please try other banks','Account not available at the moment, please try other banks');
                }
            }elseif ($bank == "wema") {
                $wema = $this->user->generate_reserved_account("", "", ["035"]);

                if ($wema->responseCode == "99") {
                    $wema = $this->user->getReservedAccount();
                    $wema = json_decode(json_encode($wema->responseBody->accounts), true);

                    $wema_reserved_array = array_recursive_search_key_map("Wema bank", $wema);

                    if (is_array($wema_reserved_array)) {
                        \Log::info($wema_reserved_array[0]);
                        $key2 = $wema_reserved_array[0];
                        $wema = $wema[$key2];
                        
                        $user1 =  User::find($this->user->id);
                        $user1->wema_reserved_acct = json_encode($wema);
                        $user1->save();

                    }
                    

                } else {
                    $user2 =  User::find($this->user->id);
                    $user2->wema_reserved_acct = json_encode($wema->responseBody->accounts);
                    $user2->save();

                }
                
                return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');

            }elseif ($bank == "moniepoint") {

                $moniepoint = $this->user->generate_reserved_account("", "", ["50515"]);

                if ($moniepoint->responseCode == "99") {
                    $moniepoint = $this->user->getReservedAccount();

                    $moniepoint = json_decode(json_encode($moniepoint->responseBody->accounts), true);

                    $moniepoint_reserved_array = array_recursive_search_key_map("Moniepoint Microfinance Bank", $moniepoint);



                    if (is_array($moniepoint_reserved_array)) {
                        $key2 = $moniepoint_reserved_array[0];
                        $moniepoint = $moniepoint[$key2];
                        
                        $user1 = User::find($this->user->id);
                        $user1->moniepoint_reserved_acct = json_encode($moniepoint);
                        $user1->save();

                    }

                } else {
                    $user2 = User::find($this->user->id);
                    $user2->moniepoint_reserved_acct = json_encode($moniepoint->responseBody->accounts);
                    $user2->save();
                }
                return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');

            }
        }elseif (!is_null($this->user->bvn) && !is_null($this->user->nin)) {
            $bvn = openssl_decrypt(
                $this->user->bvn,
                "AES-128-CTR",
                "SWIFtlInk1nG",
                0,
                'SWiftLiNknG89101'
            );
            if ($bank == "gtbank") {
                $squadco = $this->generateSquadCo($bvn);
                if ($squadco['status'] == 200) {
                    $gtbank_account = array("bankCode" => $squadco['data']['bank_code'], "bankName" => "GT Bank", "accountNumber" => $squadco['data']['virtual_account_number'], "accountName" => $squadco['data']['first_name'] . ' ' . $squadco['data']['last_name']);
                    $user->gtbank_reserved_acct = json_encode($gtbank_account);
                    $user->save();

                    return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');

                } else {
                    return $this->sendError('Account not available at the moment, please try other banks', 'Account not available at the moment, please try other banks');
                }
            } elseif ($bank == "providus") {
                $providus = $this->generateProvidus($bvn);

                if ($providus['requestSuccessful'] != false) {
                    $providus_account = array("bankCode" => "", "bankName" => "Providus Bank", "accountNumber" => $providus['account_number'], "accountName" => $providus['account_name']);
                    $user->providus_reserved_acct = json_encode($providus_account);
                    $user->providus_account = $providus['account_number'];
                    $user->save();
                    return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');
                } else {
                    return $this->sendError('Account not available at the moment, please try other banks', 'Account not available at the moment, please try other banks');
                }
            } elseif ($bank == "rehoboth") {
                $rehoboth = $this->user->generateRehoboth($bvn);
                if ($rehoboth->error == false) {
                    $rehoboth_account = array("bankCode" => "", "bankName" => $rehoboth->Virtual->BankName, "accountNumber" => $rehoboth->Virtual->AccountNumber, "accountName" => $rehoboth->Virtual->AccountName);
                    $user->rehoboth_reserved_acct = json_encode($rehoboth_account);
                    $user->rehoboth_account = $rehoboth->Virtual->AccountNumber;
                    $user->save();
                    return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');
                } else {
                    return $this->sendError('Account not available at the moment, please try other banks', 'Account not available at the moment, please try other banks');
                }
            } elseif ($bank == "wema") {
                $wema = $this->user->generate_reserved_account($bvn, "", ["035"]);
                if ($wema->responseCode == "99") {
                    $wema = $this->user->getReservedAccount();
                    $wema = json_decode(json_encode($wema->responseBody->accounts), true);

                    $wema_reserved_array = array_recursive_search_key_map("Wema bank", $wema);

                    if (is_array($wema_reserved_array)) {
                        \Log::info($wema_reserved_array[0]);
                        $key2 = $wema_reserved_array[0];
                        $wema = $wema[$key2];
                        $user1 =  User::find($this->user->id);
                        $user1->wema_reserved_acct = json_encode($wema);
                        $user1->save();

                    }
                   
                } else {
                    $user2 = User::find($this->user->id);
                    $user2->wema_reserved_acct = json_encode($wema->responseBody->accounts);
                    $user2->save();

                }
                
                return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');

            } elseif ($bank == "moniepoint") {
                $moniepoint = $this->user->generate_reserved_account($bvn, "", ["50515"]);

                if ($moniepoint->responseCode == "99") {
                    $moniepoint = $this->user->getReservedAccount();

                    $moniepoint = json_decode(json_encode($moniepoint->responseBody->accounts), true);

                    $moniepoint_reserved_array = array_recursive_search_key_map("Moniepoint Microfinance Bank", $moniepoint);



                    if (is_array($moniepoint_reserved_array)) {
                        $key2 = $moniepoint_reserved_array[0];
                        $moniepoint = $moniepoint[$key2];
                        
                        $user1 = User::find($this->user->id);
                        $user1->moniepoint_reserved_acct = json_encode($moniepoint);
                        $user1->save();

                    }

                } else {
                    $user2 = User::find($this->user->id);
                    $user2->moniepoint_reserved_acct = json_encode($moniepoint->responseBody->accounts);
                    $user2->save();
                }

               
                return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');
            }
        }
        

    }

    public function updateBvn(Request $request)
    {
        \Log::info($request);
        $user = User::find($this->user->id);
        // Restrict to once a day
        $today = date("Y-m-d");
        \Log::info($today);
        \Log::info($user->bvn_identify_date);
        $db_date = date("Y-m-d", strtotime($user->bvn_identify_date));
        \Log::info($db_date);

        $ref = $this->referenceCode();
        $prev = $this->user->wallet;
        $bal = $this->user->wallet;



        if ($db_date == $today) {
            return $this->sendError("Bvn verification limit for today is exceeded", "Bvn verification limit for today is exceeded");
        } else {

            $phone = $this->formatPhoneNumber($request->phone);


            $details = $this->user->getBvn($request->bvn, $request->name, $request->dob, $phone);
            $names = explode(" ", $request->name);
            $names_count = count($names);
            $first_name = $names[0];
            $last_name = $names[$names_count - 1];
            // $nn = $details->responseBody;
            \Log::info(print_r($details, true));

            $hashed_bvn = openssl_encrypt(
                $request->bvn,
                "AES-128-CTR",
                "SWIFtlInk1nG",
                0,
                'SWiftLiNknG89101'
            );

            // Use openssl_decrypt() function to decrypt the data
            // $decryption=openssl_decrypt ($encryption(bvn from db already hashed), "AES-128-CTR",
            //         "SWIFtlInk1nG", 0, 'SWiftLiNknG89101');

            $user->bvn_attempts_count = $user->bvn_attempts_count + 1;

            if($user->bvn_attempts_count == 2){
                $user->bvn_identify_date = $today;
            }

            $user->save();

            $subcategory = Subcategory::find(40);

            $order = new Order();
            $order->ref = $ref;
            $order->user_id = $this->user->id;
            $order->subcategory_id = $subcategory->id;
            $order->plan = $subcategory->title;
            $order->amount = 0;
            $order->quantity = 1;
            $order->subtotal = 0;
            $order->phone = $this->user->phone;
            $order->channel = is_null($request->channel) ? 'Web' : 'App';
            $order->bal = $bal;
            $order->prev_bal = $prev;

            $order->total = 0;
            $order->description = "BVN VERIFICATION";
            $order->status = 0;
            $order->save();



            if ($details->responseCode == 0 && $details->responseMessage == "success") {
                if ($details->responseBody->name->matchStatus == "PARTIAL_MATCH" || $details->responseBody->name->matchStatus == "FULL_MATCH") {

                    $user->firstname = $first_name;
                    $user->lastname = $last_name;
                    $user->bvn = $hashed_bvn;
                    $user->save();


                    $unhashed_bvn = openssl_decrypt(
                        $user->bvn,
                        "AES-128-CTR",
                        "SWIFtlInk1nG",
                        0,
                        'SWiftLiNknG89101'
                    );
                    \Log::info($user->reserved_acct);
                    if (!is_null($user->wema_reserved_acct) || !is_null($user->moniepoint_reserved_acct)) {

                        // UPDATE MONNIFY
                        $this->user->updateMonnify($this->user->email, $unhashed_bvn, "");

                    }

                    if (!is_null($user->gtbank_reserved_acct)) {
                        // UPDATE GTBANK
                        $this->updateSquadcoGT($this->user->email, $this->user->phone, $unhashed_bvn);
                    }

                    $updOrder = Order::where('ref', $ref)->first();
                    $updOrder->status = 1;
                    $updOrder->save();

                    return $this->sendResponse("Verified successfully", "Verified successfully");
                }



            } else {
                return $this->sendError($details->responseMessage, $details->responseMessage);
            }
        }
        return $this->sendError("An error occurred, please try again", "An error occurred, please try again");
    }

    public function updateNin(Request $request)
    {
        \Log::info($request);
        $user = User::find($this->user->id);

        $today = date("Y-m-d");
        $db_date = date("Y-m-d", strtotime($user->nin_identify_date));

        $ref = $this->referenceCode();
        $prev = $this->user->wallet;
        $bal = $this->user->wallet;


        if ($db_date == $today) {
            return $this->sendError("Nin verification limit for today is exceeded", "Nin verification limit for today is exceeded");
        } else {

            $details = $this->user->getNin($request->nin);

            // $nn = $details->responseBody;
            \Log::info(print_r($details, true));



            $hashed_nin = openssl_encrypt(
                $request->nin,
                "AES-128-CTR",
                "SWIFtlInk1nG",
                0,
                'SWiftLiNknG89101'
            );



            // Use openssl_decrypt() function to decrypt the data
            // $decryption=openssl_decrypt ($encryption(bvn from db already hashed), "AES-128-CTR",
            //         "SWIFtlInk1nG", 0, 'SWiftLiNknG89101');
            $user->nin_identify_date = $today;
            $user->save();

            $subcategory = Subcategory::find(40);

            $order = new Order();
            $order->ref = $ref;
            $order->user_id = $this->user->id;
            $order->subcategory_id = $subcategory->id;
            $order->plan = $subcategory->title;
            $order->amount = 0;
            $order->quantity = 1;
            $order->subtotal = 0;
            $order->phone = $this->user->phone;
            $order->channel = is_null($request->channel) ? 'Web' : 'App';
            $order->bal = $bal;
            $order->prev_bal = $prev;

            $order->total = 0;
            $order->description = "NIN VERIFICATION";
            $order->status = 0;
            $order->save();

            if ($details->responseCode == 0 && $details->responseMessage == "success") {
                $first_name = $details->responseBody->firstName;
                $last_name = $details->responseBody->lastName;


                $user->firstname = $first_name;
                $user->lastname = $last_name;
                $user->nin = $hashed_nin;
                $user->save();

                $unhashed_nin = openssl_decrypt(
                    $user->nin,
                    "AES-128-CTR",
                    "SWIFtlInk1nG",
                    0,
                    'SWiftLiNknG89101'
                );

                if (!is_null($user->wema_reserved_acct) || !is_null($user->moniepoint_reserved_acct)) {
                    // UPDATE MONNIFY
                    $this->user->updateMonnify($this->user->email, "", $unhashed_nin);

                    // UPDATE PROVIDUS
                }

                $updOrder = Order::where('ref', $ref)->first();
                $updOrder->status = 1;
                $updOrder->save();

                return $this->sendResponse("Verified successfully", "Verified successfully");

            } else {
                return $this->sendError($details->responseMessage, $details->responseMessage);
            }
        }
    }
    
    public function InitializeMonnifyPay(Request $request){
        $customer_name = $this->user->first_name ." ". $this->user->last_name;
        $customer_email = $this->user->email;
        $paymentReference = $this->referenceCode();
        $amount = $request->amount;
        $response = $this->user->initializeMonnifyPayment($customer_name,$customer_email,$paymentReference,$amount);
        return $this->sendResponse($response, "initilization successful");
    }

    public function VerifyPayment(Request $request, $transaction_reference){
        $response = $this->user->VerifyMonnify($transaction_reference);
        return $this->sendResponse($response, "verification retrieved");
    }
    
    
     public function UpdateWebhook(Request $request)

    {
        $user = User::find($this->user->id);
        $user->webhook_url = $request->webhook_url;
        $user->save();
        return $this->sendResponse("Updated successfully", "Updated successfully");
    }

    // public function generate_reserved_account()
    // {

    //     // die(json_encode($this->user));
    //     if ($this->user->hasReservedAccount()) {
    //         return $this->sendError('Account already reserved for user', 'Account already reserved for user');
    //     }

    //     $result = $this->user->generate_reserved_account();

    //     if ($result->requestSuccessful) {
    //         $user =  User::find($this->user->id);
    //         $user->reserved_acct = json_encode($result->responseBody->accounts);

    //         $user->save();
    //     } else {
    //         $result = $this->user->getReservedAccount();

    //         if ($result->requestSuccessful) {
    //             $user =  User::find($this->user->id);
    //             $user->reserved_acct = json_encode($result->responseBody->accounts);
    //             $user->save();
    //         }
    //     }

    //     return $this->sendResponse(new UserResource($user), 'User account reserved successfully.');
    // }





    // public function update(Request $request, User $product)
    // {
    //     $input = $request->all();

    //     $validator = Validator::make($input, [
    //         'firstname' => 'required',
    //         'lastname' => 'required',
    //         'email' => 'required',
    //         //   'phone' => 'required',

    //     ]);


    //     if ($validator->fails()) {
    //         return $this->sendError('Validation Error.', $validator->errors());
    //     }

    //     $product->firstname = $input['firstname'];
    //     $product->lastname = $input['lastname'];
    //     $product->email = $input['email'];
    //     $product->address = $input['address'];
    //     $product->phone = $input['phone'];
    //     $product->gender = $input['gender'];
    //     $product->wallet = $input['wallet'];
    //     $product->city = $input['city'];
    //     $product->state = $input['state'];
    //     $product->country = $input['country'];

    //     $product->save();

    //     return $this->sendResponse(new UserResource($product), 'User updated successfully.');
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admin_user = User::find($this->user->id);
        \Log::info($admin_user);
        if($admin_user->role == 1){
            $payment = User::find($id);
            $payment->delete();
            return $this->sendResponse([], 'User deleted successfully.');
        }else {
            return $this->sendError("Processing failed!");
        }
    }
}
