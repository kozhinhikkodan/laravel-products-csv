<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('user::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('user::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {

            Validator::make($request->all(), [
                'name' => 'required|unique:users,name,NULL,id,deleted_at,NULL',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ])->setAttributeNames([
                'c_password' => 'Confirm Password'
            ])->validate();

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

            return $this->sendResponse(UserResource::make($user), 'User created', 201);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('user::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('user::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }


    public function login(Request $request): JsonResponse
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required',
                'password' => 'required',
            ])->validate();



            if (Auth::attempt(['name' => $request->name, 'password' => $request->password])) {

                $user = Auth::user();


                $user->token = $user->createToken('APP')->plainTextToken;
                $user->name = $user->name;

                return $this->sendResponse($user, 'User Logged in.');
            } else {
                return $this->sendError('Unauthorized.', ['error' => 'Unauthorized'], 401);
            }
        } catch (\Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function logout(Request $request): JsonResponse
    {
        try {
            if (auth()->check()) {
                Auth::user()->tokens()->delete();
            }
            return $this->sendResponse([], 'User logged out successfully.');
        } catch (\Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
