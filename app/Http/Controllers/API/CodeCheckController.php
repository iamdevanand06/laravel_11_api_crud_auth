<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ResetCodePassword;
use App\Traits\commonTrait;
use Validator;

class CodeCheckController extends Controller
{
    use commonTrait;
    /**
     * Check if the code is exist and vaild one (Setp 2)
     *
     * @param  mixed $request
     * @return void
     */
    public function __invoke(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|email|exists:users',
            'code' => 'required|min:6|numeric'
        ]);

        if ($validator->fails()) {
            return response(['message' => 'Please enter the valid otp'], 422);
        } else {
            $passwordReset = ResetCodePassword::firstWhere('email', $request->email);

            if (isset($passwordReset) && ($passwordReset->created_at > now()->addMinute(10) && (strcmp($passwordReset->code, $request->code) !== 0)) ) {
                return $this->sendError(['user_condition' => 'rejected', 'code' => $request->code], trans('Please enter the valid code'), 422);
            }

            return $this->sendResponse(['user_condition' => 'approved','code' => $passwordReset->code], trans('passwords.code_is_valid'));
        }
    }
}
