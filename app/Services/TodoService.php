<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Todo;
use Illuminate\Database\Eloquent\Collection;

class TodoService
{
    /**
     * Get all todos ordered by creation date (newest first).
     */
    public function getAll(): Collection
    {
        return Todo::orderBy('created_at', 'desc')->get();
    }

    /**
     * Create a new todo.
     */
    public function create(string $title): Todo
    {
        return Todo::create([
            'title' => $title,
            'completed' => false,
        ]);
    }

    /**
     * Toggle todo completion status.
     */
    public function toggle(int $id): Todo
    {
        $todo = Todo::findOrFail($id);
        $todo->toggleCompletion();

        return $todo;
    }

    /**
     * Update todo title.
     */
    public function update(int $id, string $title): Todo
    {
        $todo = Todo::findOrFail($id);
        $todo->update(['title' => $title]);

        return $todo;
    }
}
