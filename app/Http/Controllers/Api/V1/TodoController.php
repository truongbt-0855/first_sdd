<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoRequest;
use App\Services\TodoService;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    public function __construct(
        private readonly TodoService $todoService
    ) {}

    /**
     * Display a listing of todos.
     */
    public function index(): JsonResponse
    {
        $todos = $this->todoService->getAll();

        return response()->json([
            'data' => $todos,
        ]);
    }

    /**
     * Store a newly created todo.
     */
    public function store(StoreTodoRequest $request): JsonResponse
    {
        $todo = $this->todoService->create(
            $request->validated('title')
        );

        return response()->json([
            'data' => $todo,
        ], 201);
    }
}
