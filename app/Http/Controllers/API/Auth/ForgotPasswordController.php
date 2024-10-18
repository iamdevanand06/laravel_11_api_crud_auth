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
        try{

            $input = $request->all();
            $validator = Validator::make($input, [
                'email' => 'required|email|exists:users',
                'code_type' => 'required|string|max:2'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $data['email'] = $request->email;

            // Delete all old code that user send before.
            // ResetCodePassword::where('email', $data['email'])->delete();

            // Generate random code
            $data['code'] = mt_rand(100000, 999999);

            //send code type
            $data['code_type'] = $request->code_type;

            $passwordReset = ResetCodePassword::where('email', $data['email'])->where('code_type', $data['code_type'])->first();

            if (isset($passwordReset)) {
                ResetCodePassword::where('email', $data['email'])->where('code_type', $data['code_type'])
                ->update(['code' => $data['code'], 'sent_recipt' => '0', 'verified' => '0', 'password_changed' => '0']);
            } else {

                // Create a new code
                $codeData = ResetCodePassword::create($data);
            }

            if ($data['code_type'] == 'pv') { //Password Reset

                $message = 'We have emailed your password reset passcode';
                $subject = 'Send Code Reset Password';
                $line_1 = 'We have received your request to reset your account password';
                $line_2 = 'You can use the following code to recover your account:';

            } elseif($data['code_type'] == 'ev'){ //Email verification

                $message = 'We have emailed your email verification passcode';
                $subject = 'Send Code Verification Email';
                $line_1 = 'We have received your request to email verification account';
                $line_2 = 'You can use the following code to verify your account:';

            }

            $emailContent = [
                'subject' => $subject,
                'body' => [
                    'line_1' => $line_1,
                    'line_2' => $line_2,
                    'code' => isset($codeData->code)?$codeData->code:$data['code'],
                    'line_3' => 'The allowed duration of the code is 10 minutes from the time the message was sent!'
                ]
            ];

            $data['sent_recipt'] = '0';

            if (env('APP_ENV') !== 'local') {

                // Send email to user
                Mail::to($data['email'])->send(new SendCodeResetPassword($emailContent));
                $emailContent = [];
            }

            ResetCodePassword::where('email', $data['email'])->where('code_type', $data['code_type'])->update(['sent_recipt' => '1']);

            return $this->sendResponse(['userEmail' => $data['email'], 'content' => $emailContent, 'message' => $message], 200);

        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
        }
    }
}
