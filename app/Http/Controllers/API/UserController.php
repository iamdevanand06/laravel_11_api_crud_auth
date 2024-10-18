<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\commonTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
    use commonTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        try{
            $user = User::paginate(10);
            return $this->sendResponse(UserResource::collection($user)->response()->getData(), 'User retrieved successfully.');
        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        try{
            $input = $request->all();

            $validator = Validator::make($input, [
                'name' => 'required|max:20|min:4|regex:/^[a-zA-Z ]+$/u',
                'mobile_number' => 'required|min:10|max:10|regex:/^[6-9]{1}[0-9]{9}+$',
                'email' => 'required|email|unique:users',
                'password' => 'required_with:c_password|min:6|alpha_num|same:c_password',
                'c_password' => 'min:6|alpha_num',
                'is_locked' => 'required|mix:0|max:1',
                'status' => 'required|max:1|min:0',
                'role_id' => 'required|max:100|min:1'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $user = User::create($input);

            return $this->sendResponse(new UserResource($user), 'User created successfully.');
        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        try{
            $user = User::find($id);

            if (is_null($user)) {
                return $this->sendError('User not found.');
            }

            return $this->sendResponse(new UserResource($user), 'User retrieved successfully.');
        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user): JsonResponse
    {
        try{
            $input = $request->all();

            $validator = Validator::make($input, [
                'name' => 'required|max:20|min:4|regex:/^[a-zA-Z ]+$/u',
                'email' => 'required|email|unique:users',
                'mobile_number' => 'required|min:10|max:10|regex:/^[6-9]{1}[0-9]{9}+$',
                'password' => 'required_with:c_password|min:6|alpha_num|same:c_password',
                'c_password' => 'min:6|alpha_num',
                'is_locked' => 'required|mix:0|max:1',
                'status' => 'required|max:1|min:0',
                'role_id' => 'required|max:100|min:1'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $user->name = $input['name'];
            $user->email = $input['email'];

            $user->save();

            return $this->sendResponse(new UserResource($user), 'User updated successfully.');
        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user): JsonResponse
    {
        try{
            $user->delete();

            return $this->sendResponse([], 'User deleted successfully.');
        } catch (Exception $e) {
            Log::error('Message => '.$e->getMessage().'Line No => '.$e->getLine());
        }
    }
}
