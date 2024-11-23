<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBoardRequest;
use App\Http\Resources\BoardResource;
use Illuminate\Http\Request;
use App\Models\Board;

class BoardController extends Controller
{
    // Get all boards
    public function index()
    {
        return BoardResource::collection(Board::all());
    }

    // Create a new board
    public function store(StoreBoardRequest $request)
    {
        $validated = $request->validated();

        // Check if the board size fits an even number of cards
        if ($validated['board_cols'] * $validated['board_rows'] % 2 !== 0) {
            return response()->json([
                'message' => 'Board size must fit an even number of cards'
            ], 400);
        }

        // Check if the board already exists
        if (Board::where('board_cols', $validated['board_cols'])
            ->where('board_rows', $validated['board_rows'])
            ->exists()) {
            return response()->json([
                'message' => 'Board already exists'
            ], 400);
        }
        
        $board = new Board();
        $board->fill($validated);
        $board->save();

        return new BoardResource($board);
    }

    // Delete a board
    public function destroy(Board $board)
    {
        $board->delete();

        return response()->json([
            'message' => 'Board deleted'
        ]);
    }
}
