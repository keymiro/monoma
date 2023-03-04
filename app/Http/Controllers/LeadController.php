<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadRequest;
use App\Models\Lead;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LeadController extends Controller
{
    public function index()
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();

            if ($user->is_manager()) {
                $leads = Lead::all();
            }

            if ($user->is_agent()) {
                $leads = Lead::where('owner', $user->id)->get();
            }

            return response()->json([
                'meta' => [
                    'success' => true,
                    'errors' => []
                ],
                'data' => $leads
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ['Token expirado']
                ]
            ], 401);
        }
    }

    public function show($id)
    {
        $lead = Lead::find($id);
        $user = JWTAuth::parseToken()->authenticate();

        if (!$lead) {
            return response()->json([
                'meta' => ['success' => false, 'errors' => ['lider no encontrado']],
            ], 404);
        }
        if (!$user->can_view_lead($lead)) {
            return response()->json([
                'meta' => ['success' => false, 'errors' => ['Token expirado']],
            ], 401);
        }

        return response()->json([
            'meta' => ['success' => true, 'errors' => []],
            'data' => $lead
        ], 200);
    }

    public function store(LeadRequest $request)
    {
        try {

            $validated = $request->validated();

            $user = JWTAuth::parseToken()->authenticate();

            if (!$user->is_manager()) {
                return response()->json([
                    'meta' => [
                        'success' => false,
                        'errors' => [
                            'El acceso a ese recurso está prohibido'
                        ]
                    ]
                ], 403);
            }

            $lead = Lead::create([
                'name' => $validated['name'],
                'source' => $validated['source'],
                'owner' => $validated['owner'],
                'created_by' => $user->id
            ]);

            return response()->json([
                'meta' => [
                    'success' => true,
                    'errors' => []
                ],
                'data' => $lead
            ], 201);
        } catch (JWTException $e) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ['Token expirado']
                ]
            ], 401);
        }
    }
}
