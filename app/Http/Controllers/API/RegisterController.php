<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Mail\VerifyEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use App\Http\Controllers\API\BaseController as BaseController;
use DB;

class RegisterController extends  BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'phone' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            //'role' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return $this->sendError($msg, $msg);
        }
        !is_null($request->referral_id) ? $input['referral_id'] = $request->referral_id : $input['referral_id'] = 0;

        $verifyRef = User::find($input['referral_id']);
        if (is_null($verifyRef) && $input['referral_id'] > 0) {
            return $this->sendError('Referral Info could not be found', 'Referral Info could not be found');
        }

        $input = $request->all();
        $password = bcrypt($input['password']);
        $newuser = User::create([
            "firstname" => $request->firstname,
            "lastname" => $request->lastname,
            "email" => $request->email,
            "password" => $password,
            "phone" => $request->phone,
        ]);
        $user = User::find($newuser->id);
        $success['token'] =  $user->createToken('pluginng')->accessToken;
        $success['firstname'] =  $user->firstname;
        $success['lastname'] =  $user->lastname;
        $success['email'] =  $user->email;
        $success['phone'] =  $user->phone;

        $success['wallet'] =  $user->wallet;
        $success['role'] = $user->role;
        $success['id'] =  $user->id;
        $success['userlevel'] =  $user->userlevel;
        $success['status'] = $user->status;
        $success['hear_about_us'] =  $user->hear_about_us;
        $success['commission'] =  $user->commission;
        $success['reserved_acct'] =  $user->reserved_acct;

        $success['userToken'] =  $user->userToken;
        $success['deviceToken'] =  $user->deviceToken;
        $success['bank_name'] =  $user->bank_name;
        $success['account_number'] =  $user->account_number;
        $success['referral_id'] =  $user->referral_id;
        $success['created_at'] =  $user->created_at;
        $success['updated_at'] =  $user->updated_at;
        Mail::send(new VerifyEmail($user));

        return $this->sendResponse($success, 'User registered successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {

        //check email
        $user = User::where('email', $request->email)->first();

        if (isset($user->status) && $user->status == 1) {

            return $this->sendError('Banned.', ['error' => 'User is Banned']);
        }
        // else if ($user->email_verified_at == null) {
        //     return $this->sendError('Please verify your email', 'Please verify your email address before signing in');
        // } 

        else {

            $remember_me = $request->remember_me;
            
            // Issue a token for this login without revoking other devices.
            // Failed password attempts must never invalidate existing logins.

            if (Auth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ],$remember_me)) {

                $rem = User::where("email", $user->email)->first();

                $success['token'] = $user->createToken('pluginng')->accessToken;

                $success['firstname'] =  $user->firstname;
                $success['lastname'] =  $user->lastname;
                $success['email'] =  $user->email;
                $success['phone'] =  $user->phone;
                $success['bvn'] =  $user->bvn;
                $success['nin'] =  $user->nin;
                $success['webhook_url'] =  $user->webhook_url;
                $success['remember_token'] =  $rem->remember_token;


                $success['wallet'] =  $user->wallet;
                $success['role'] = $user->role;
                $success['id'] =  $user->id;
                $success['userlevel'] =  $user->userlevel;
                $success['status'] = $user->status;
                $success['hear_about_us'] =  $user->hear_about_us;
                $success['commission'] =  $user->commission;
                $success['reserved_acct'] =  $user->reserved_acct;
                $success['wema_reserved_acct'] =  $user->wema_reserved_acct;
                $success['moniepoint_reserved_acct'] =  $user->moniepoint_reserved_acct;
                $success['gtbank_reserved_acct'] =  $user->gtbank_reserved_acct;
                $success['providus_reserved_acct'] =  $user->providus_reserved_acct;
                $success['rehoboth_reserved_acct'] =  $user->rehoboth_reserved_acct;
                $success['providus_account'] =  $user->providus_account;
                $success['userToken'] =  $user->userToken;
                $success['deviceToken'] =  $user->deviceToken;
                $success['bank_name'] =  $user->bank_name;
                $success['account_number'] =  $user->account_number;
                $success['referral_id'] =  $user->referral_id;
                $success['created_at'] =  $user->created_at;
                $success['updated_at'] =  $user->updated_at;


                return $this->sendResponse($success, 'User login successfully.');
            } else {
                return $this->sendError('Invalid Email/Password', 'Invalid Email/Password');
            }
        }
    }






    public function logout(Request $request)
    {
        $token = $request->user()->token();
        if ($token) {
            $token->revoke();
        }
        $success['message'] = 'Logged out';
        return $this->sendResponse($success, 'Logged out successfully.');
    }

    public function reset_password(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'email' => "required|email",
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->sendError(array("status" => 400, "message" => $validator->errors()->first(), "data" => array()));
        } else {
            try {

                $user = User::where('email', $request->input('email'))->first();
                //$credentials = $request->validate(['email' => 'required|email']);
                $credentials = ['email' => $user->email];
                $response = Password::broker()->sendResetLink($credentials);
                switch ($response) {
                    case Password::RESET_LINK_SENT:
                        return  $this->sendResponse(array("status" => 200, "message" => trans($response), "data" => array()), 'Password reset link has been sent');
                    case Password::INVALID_USER:
                        return  $this->sendError(array("status" => 400, "message" => trans($response), "data" => array()), 'Invalid User.');
                }
            } catch (TransportException $ex) {
                return $this->sendError(array("status" => 400, "message" => $ex->getMessage(), "data" => []));
            } catch (Exception $ex) {
                return $this->sendError(array("status" => 400, "message" => $ex->getMessage(), "data" => []));
            }
        }
        // return \Response::json($arr);
        //array("status" => 200, "message" => trans($response), "data" => array());
        //return $this->sendResponse($arr, 'User updated successfully.');
    }
}
