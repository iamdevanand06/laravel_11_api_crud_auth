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
        $user = User::paginate(10);

        return $this->sendResponse(UserResource::collection($user)->response()->getData(), 'User retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|max:20|min:4|regex:/^[a-zA-Z ]+$/u',
            // 'mobile_number' => 'required|min:10|max:10|regex:/^[6-9]{1}[0-9]{9}+$',
            'email' => 'required|email|unique:users',
            'password' => 'required_with:c_password|min:6|alpha_num|same:c_password',
            'c_password' => 'min:6|alpha_num',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = User::create($input);

        return $this->sendResponse(new UserResource($user), 'User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('User not found.');
        }

        return $this->sendResponse(new UserResource($user), 'User retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|max:20|min:4|regex:/^[a-zA-Z ]+$/u',
            'email' => 'required|email|unique:users',
            // 'mobile_number' => 'required|min:10|max:10|regex:/^[6-9]{1}[0-9]{9}+$',
            'password' => 'required_with:c_password|min:6|alpha_num|same:c_password',
            'c_password' => 'min:6|alpha_num',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user->name = $input['name'];
        $user->email = $input['email'];

        $user->save();

        return $this->sendResponse(new UserResource($user), 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return $this->sendResponse([], 'User deleted successfully.');
    }
}
