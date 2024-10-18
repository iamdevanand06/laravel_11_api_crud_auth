<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\ResetCodePassword;
use App\Models\User;
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
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'email' => 'required|email|exists:users',
                'code' => 'required|min:6|numeric',
                'code_type' => 'required|string|max:2'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $passwordReset = ResetCodePassword::where('email', $request->email)->where('code_type', $request->code_type)->first();

            if (isset($passwordReset)) {
                if ($passwordReset->verified == 1) {
                    return $this->sendError(['Passcode is already verified'], ['user_condition' => 'rejected'], 422);
                }

                if (strcmp($passwordReset->code, $request->code) == '1') {
                    return $this->sendError(['Passcode mis-match found'], ['user_condition' => 'rejected'], 422);
                }

                if ($passwordReset->created_at > now()->addMinute(-10) != 0) {
                    return $this->sendError(['Passcode time out'], ['user_condition' => 'rejected'], 422);
                }
            } else {
                return $this->sendError(['Passcode meta not retrieved'], ['user_condition' => 'rejected'], 422);
            }

            if ($request->code_type == 'pv'){
                $message = 'Your passcode is verified';
            }elseif($request->code_type == 'ev'){
                User::where('email', $request->email)->update(['email_verified_at' => now()]);
                $message = 'Your Email is Verified';
            }

            ResetCodePassword::where('email', $request->email)->update(['verified' => '1']);

            return $this->sendResponse(['user_condition' => 'approved'], $message);
        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
        }
    }
}
