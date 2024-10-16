<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendCodeResetPassword;
use App\Models\ResetCodePassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;
use App\Traits\commonTrait;

class ForgotPasswordController extends Controller
{
    use commonTrait;

    public function __invoke(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $data['email'] = $request->email;

        // Delete all old code that user send before.
        ResetCodePassword::where('email', $data['email'])->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::create($data);

        if (env('APP_ENV') !== 'local') {
            // Send email to user
            Mail::to($data['email'])->send(new SendCodeResetPassword($codeData->code));

            return $this->sendResponse(['userEmail' => $data['email'], 'message' => 'We have emailed your password reset passcode'], 200);
        } else {
            return $this->sendResponse(['userEmail' => $data['email'], 'passcode' => $data['code'], 'message' => 'We have emailed your password reset passcode'], 200);
        }
    }
}
