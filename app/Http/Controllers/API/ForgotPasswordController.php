<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ResetCodePassword;
use App\Mail\SendCodeResetPassword;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Traits\commonTrait;
use Validator;

class ForgotPasswordController extends Controller
{
    public function __invoke(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            return response(['message' => 'Please enter the valid Email'], 422);
        } else {

            $data['email'] = $request->email;

            // Delete all old code that user send before.
            ResetCodePassword::where('email', $data['email'])->delete();

            // Generate random code
            $data['code'] = mt_rand(100000, 999999);

            // Create a new code
            $codeData = ResetCodePassword::create($data);

            // Send email to user
            Mail::to($data['email'])->send(new SendCodeResetPassword($codeData->code));

            return response(['userEmail' => $data['email'], 'message' => trans('passwords.sent')], 200);
        }
    }
}
