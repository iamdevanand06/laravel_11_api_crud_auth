<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\ResetCodePassword;
use App\Traits\commonTrait;
use Illuminate\Http\Request;
use Validator;

class CodeCheckController extends Controller
{
    use commonTrait;

    /**
     * Check if the code is exist and vaild one (Setp 2)
     *
     * @param  mixed  $request
     * @return void
     */
    public function __invoke(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|email|exists:users',
            'code' => 'required|min:6|numeric',
            'code_type' => 'required|string|max:2'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $passwordReset = ResetCodePassword::firstWhere('email', $request->email);

        if (isset($passwordReset)) {
            if ($passwordReset->verified == 1) {
                return $this->sendError(['Passcode is already verified'], ['user_condition' => 'rejected'], 422);
            }

            if (strcmp($passwordReset->code, $request->code) == '1') {
                return $this->sendError(['Passcode mis-match found'], ['user_condition' => 'rejected'], 422);
            }

            if ($passwordReset->created_at > now()->addMinute(-10) != 1) {
                return $this->sendError(['Passcode time out'], ['user_condition' => 'rejected'], 422);
            }
        } else {
            return $this->sendError(['Passcode meta not retrieved'], ['user_condition' => 'rejected'], 422);
        }

        ResetCodePassword::where('email', $request->email)->update(['verified' => '1']);

        return $this->sendResponse(['user_condition' => 'approved'], trans('passwords.code_is_valid'));

    }
}
