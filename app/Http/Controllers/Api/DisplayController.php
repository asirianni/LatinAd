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
        // Filter only displays for the authenticated user using scope
        $query = Display::with('user')->forUser();

        // Optional filters
        if ($request->has('type')) {
            $query->where('type', $request->type);
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
        $data = $request->validated();
        $data['user_id'] = auth()->id(); // Automatically assign to authenticated user
        
        $display = Display::create($data);
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
        $display = Display::with('user')->ownedBy()->find($id);

        if (!$display) {
            return response()->json([
                'success' => false,
                'message' => 'Display not found or you do not have permission to view it'
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
        $display = Display::ownedBy()->find($id);

        if (!$display) {
            return response()->json([
                'success' => false,
                'message' => 'Display not found or you do not have permission to update it'
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
        $display = Display::ownedBy()->find($id);

        if (!$display) {
            return response()->json([
                'success' => false,
                'message' => 'Display not found or you do not have permission to delete it'
            ], 404);
        }

        $display->delete();

        return response()->json([
            'success' => true,
            'message' => 'Display deleted successfully'
        ]);
    }
}
