<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ResetCodePassword;
use App\Traits\commonTrait;
use Validator;

class ResetPasswordController extends Controller
{
    use commonTrait;

    public function __invoke(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'password' => 'required|min:6|string',
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            return response(['message' => 'Please enter the password'], 422);
        } else {

            $forgetPassword = ResetCodePassword::firstWhere('email', $request->email);


            if (isset($forgetPassword)) {
                return $this->sendError(['user_condition' => 'rejected'], trans('Passcode meta not retrieved'), 422);
            }


            $user = User::firstWhere('email', $request->email);

            $user->update($request->only('password'));

            ResetCodePassword::where('email', $request->email)->delete();

            return $this->sendResponse(['email' => $request->email], trans('site.password_has_been_successfully_reset'));
        }
    }
}
