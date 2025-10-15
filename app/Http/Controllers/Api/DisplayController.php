<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Display;
use App\Http\Requests\DisplayRequest;
use App\Http\Resources\DisplayResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DisplayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Display::with('user');

        // Filtros opcionales
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $displays = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => DisplayResource::collection($displays->items()),
            'pagination' => [
                'current_page' => $displays->currentPage(),
                'last_page' => $displays->lastPage(),
                'per_page' => $displays->perPage(),
                'total' => $displays->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DisplayRequest $request): JsonResponse
    {
        $display = Display::create($request->validated());
        $display->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Display created successfully',
            'data' => new DisplayResource($display)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $display = Display::with('user')->find($id);

        if (!$display) {
            return response()->json([
                'success' => false,
                'message' => 'Display not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new DisplayResource($display)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DisplayRequest $request, string $id): JsonResponse
    {
        $display = Display::find($id);

        if (!$display) {
            return response()->json([
                'success' => false,
                'message' => 'Display not found'
            ], 404);
        }

        $display->update($request->validated());
        $display->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Display updated successfully',
            'data' => new DisplayResource($display)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $display = Display::find($id);

        if (!$display) {
            return response()->json([
                'success' => false,
                'message' => 'Display not found'
            ], 404);
        }

        $display->delete();

        return response()->json([
            'success' => true,
            'message' => 'Display deleted successfully'
        ]);
    }
}
