<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_todos(): void
    {
        // Arrange
        Todo::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/v1/todos');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'completed', 'created_at', 'updated_at'],
                ],
            ]);
    }

    public function test_can_create_todo(): void
    {
        // Arrange
        $todoData = ['title' => 'Học Laravel'];

        // Act
        $response = $this->postJson('/api/v1/todos', $todoData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'completed', 'created_at', 'updated_at'],
            ])
            ->assertJson([
                'data' => [
                    'title' => 'Học Laravel',
                    'completed' => false,
                ],
            ]);

        $this->assertDatabaseHas('todos', [
            'title' => 'Học Laravel',
            'completed' => false,
        ]);
    }

    public function test_cannot_create_todo_with_empty_title(): void
    {
        // Act
        $response = $this->postJson('/api/v1/todos', ['title' => '']);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title'])
            ->assertJson([
                'errors' => [
                    'title' => ['Vui lòng nhập tiêu đề.'],
                ],
            ]);
    }

    public function test_cannot_create_todo_with_title_over_255_chars(): void
    {
        // Arrange
        $longTitle = str_repeat('a', 256);

        // Act
        $response = $this->postJson('/api/v1/todos', ['title' => $longTitle]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title'])
            ->assertJson([
                'errors' => [
                    'title' => ['tiêu đề không được vượt quá 255 ký tự.'],
                ],
            ]);
    }

    public function test_cannot_create_todo_without_title(): void
    {
        // Act
        $response = $this->postJson('/api/v1/todos', []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title'])
            ->assertJson([
                'errors' => [
                    'title' => ['Vui lòng nhập tiêu đề.'],
                ],
            ]);
    }

    public function test_can_list_empty_todos(): void
    {
        // Act
        $response = $this->getJson('/api/v1/todos');

        // Assert
        $response->assertStatus(200)
            ->assertJson(['data' => []]);
    }

    public function test_can_toggle_todo_completion(): void
    {
        // Arrange
        $todo = Todo::factory()->create(['completed' => false]);

        // Act - Toggle to completed
        $response = $this->patchJson("/api/v1/todos/{$todo->id}/toggle");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $todo->id,
                    'completed' => true,
                ],
            ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed' => true,
        ]);
    }

    public function test_toggle_is_idempotent(): void
    {
        // Arrange
        $todo = Todo::factory()->create(['completed' => false]);

        // Act - Toggle twice
        $this->patchJson("/api/v1/todos/{$todo->id}/toggle");
        $response = $this->patchJson("/api/v1/todos/{$todo->id}/toggle");

        // Assert - Should be back to uncompleted
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $todo->id,
                    'completed' => false,
                ],
            ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed' => false,
        ]);
    }

    public function test_can_update_todo_title(): void
    {
        // Arrange
        $todo = Todo::factory()->create(['title' => 'Original Title']);

        // Act
        $response = $this->putJson("/api/v1/todos/{$todo->id}", [
            'title' => 'Updated Title',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $todo->id,
                    'title' => 'Updated Title',
                ],
            ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_cannot_update_todo_with_empty_title(): void
    {
        // Arrange
        $todo = Todo::factory()->create(['title' => 'Original Title']);

        // Act
        $response = $this->putJson("/api/v1/todos/{$todo->id}", ['title' => '']);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title'])
            ->assertJson([
                'errors' => [
                    'title' => ['Vui lòng nhập tiêu đề.'],
                ],
            ]);

        // Ensure original title unchanged
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Original Title',
        ]);
    }

    public function test_cannot_update_todo_with_title_over_255_chars(): void
    {
        // Arrange
        $todo = Todo::factory()->create(['title' => 'Original Title']);
        $longTitle = str_repeat('a', 256);

        // Act
        $response = $this->putJson("/api/v1/todos/{$todo->id}", ['title' => $longTitle]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title'])
            ->assertJson([
                'errors' => [
                    'title' => ['tiêu đề không được vượt quá 255 ký tự.'],
                ],
            ]);

        // Ensure original title unchanged
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Original Title',
        ]);
    }
}
