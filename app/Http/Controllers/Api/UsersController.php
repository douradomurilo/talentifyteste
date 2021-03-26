<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $user = new User;

            foreach ($request->all() as $key => $value) {
                if ($key == 'password_confirmation') continue;
                $user->$key = $request->$key;
            }
            
            $user->password = Hash::make($user->password);
            $user->api_token = Str::random(60);
            $user->save();

            return response()->json(['message' => 'Usuário cadastrado com sucesso!', 'data' => $user], 201);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if ($user)
            return response()->json($user);

        return response()->json(['error' => 'Usuário não encontrado'], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::where('api_token', '=', $request->api_token)->first();
        if ($id != $user->id)
            return response()->json(['error' => 'Você não tem permissão para modificar este Usuário'], 403);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,'.$id,
            'password' => 'string|min:8|confirmed',
            'company_id' => 'integer|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::find($id);
        
        if (!$user)
            return response()->json(['error' => 'Usuário não encontrado'], 404);

        try {
            foreach ($request->all() as $key => $value) {
                if ($key == 'password_confirmation') continue;
                $user->$key = ($request->$key ?? '');
            }
            
            $user->password = Hash::make($user->password);
            $user->api_token = Str::random(60);
            $user->save();

            return response()->json(['message' => 'Usuário atualizado com sucesso!', 'data' => $user]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = User::where('api_token', '=', $request->api_token)->first();
        if ($id != $user->id)
            return response()->json(['error' => 'Você não tem permissão para deletar este Usuário'], 403);

        $user = User::find($id);

        if(!$user)
            return response()->json(['error' => 'Usuário não encontrado'], 404);

        try {
            $user->delete();
            
            return response()->json(['message' => 'Usuário deletado com sucesso!']);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
