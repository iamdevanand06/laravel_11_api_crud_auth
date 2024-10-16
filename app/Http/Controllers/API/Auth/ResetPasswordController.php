<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\ResetCodePassword;
use App\Models\User;
use App\Traits\commonTrait;
use Illuminate\Http\Request;
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
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $forgetPassword = ResetCodePassword::firstWhere('email', $request->email);

        if (isset($forgetPassword)) {
            if ($forgetPassword->verified !== 1) {
                return $this->sendError(['Passcode is already verified'], ['user_condition' => 'rejected'], 422);
            }
        } else {
            return $this->sendError(['Passcode meta not retrieved'], ['user_condition' => 'rejected'], 422);
        }

        $user = User::firstWhere('email', $request->email);

        $user->update($request->only('password'));

        ResetCodePassword::where('email', $request->email)->delete();

        return $this->sendResponse(['redirect_url' => ''], 'The Password changed Successfully');
    }
}
