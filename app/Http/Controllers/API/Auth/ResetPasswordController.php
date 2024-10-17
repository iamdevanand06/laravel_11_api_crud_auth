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

        $forgetPassword = ResetCodePassword::where('email', $request->email)->where('code_type', 'pv')->first();

        if (isset($forgetPassword)) {
            if ($forgetPassword->password_changed == 1){
                return $this->sendError(['Password is already changed'], ['user_condition' => 'rejected'], 422);
            }
        } else {
            return $this->sendError(['Passcode meta not retrieved'], ['user_condition' => 'rejected'], 422);
        }

        $user = User::firstWhere('email', $request->email);

        $user->update($request->only('password'));

        ResetCodePassword::where('email', $request->email)->where('code_type', 'pv')->update(['password_changed' => '1']);

        return $this->sendResponse(['redirect_url' => ''], 'The Password changed Successfully');
    }
}
