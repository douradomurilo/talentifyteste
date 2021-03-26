<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keyword' => 'string',
            'address' => 'string',
            'min_salary' => 'numeric',
            'max_salary' => 'numeric',
            'company' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $jobs = Job::where('status', '=', 'active');

        if (!empty($request->all())) {
            if (isset($request->keyword)) {
                $jobs->where(function ($query) use ($request) {
                    $query->where('title', 'like', "%$request->keyword%")
                        ->orWhere('description', 'like', "%$request->keyword%");
                });
            }

            if (isset($request->address))
                $jobs->where('address', 'like', "%$request->address%");

            if (isset($request->min_salary))
                $jobs->where('salary', '>=', $request->min_salary);

            if (isset($request->max_salary))
                $jobs->where('salary', '<=', $request->max_salary);

            if (isset($request->company)) {
                $companies = Company::where('name', 'like', "%$request->company%")
                    ->get()
                    ->pluck('id');

                $jobs->whereIn('company_id', $companies);
            }
        }
        
        return response()->json($jobs->get());
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'string|in:active,inactive',
            'address' => 'string|max:255',
            'salary' => 'numeric'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $job = new Job;

            foreach ($request->all() as $key => $value) {
                if ($key == 'api_token') continue;
                $job->$key = $request->$key;
            }

            $user = User::where('api_token', '=', $request->api_token)->first();
            $job->user_id = $user->id;
            $job->company_id = $user->company_id;
            
            $job->save();

            return response()->json(['message' => 'Vaga cadastrada com sucesso!'], 201);
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
        $job = Job::find($id);

        if ($job)
            return response()->json($job);

        return response()->json(['error' => 'Vaga não encontrada'], 404);
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
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string|max:255',
            'status' => 'string|in:active,inactive',
            'address' => 'string|max:255',
            'salary' => 'numeric'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $job = Job::find($id);
        
        if (!$job)
            return response()->json(['error' => 'Vaga não encontrada'], 404);
        
        $user = User::where('api_token', '=', $request->api_token)->first();

        if ($job->user_id != $user->id)
            return response()->json(['error' => 'Você não tem permissão para modificar esta Vaga'], 403);

        try {
            foreach ($request->all() as $key => $value) {
                if ($key == 'api_token') continue;
                $job->$key = ($request->$key ?? '');
            }
            
            $job->save();

            return response()->json(['message' => 'Vaga atualizada com sucesso!']);
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
        $job = Job::find($id);

        if (!$job)
            return response()->json(['error' => 'Vaga não encontrada'], 404);
        
        $user = User::where('api_token', '=', $request->api_token)->first();

        if ($job->user_id != $user->id)
            return response()->json(['error' => 'Você não tem permissão para deletar esta Vaga'], 403);

        try {
            $job->delete();
            
            return response()->json(['message' => 'Vaga deletada com sucesso!']);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
