<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase

{
    use RefreshDatabase;

    /** @test */
    function can_get_all_books()
    {
        $books = Book::factory(4)->create();
        $response = $this->getJson(route(  'books.index'  ));
        $response->assertStatus(200);

        $response
            ->assertJsonFragment(['title' => $books[0]->title])
            ->assertJsonFragment(['title' => $books[1]->title])
            ->assertJsonFragment(['title' => $books[2]->title])
            ->assertJsonFragment(['title' => $books[3]->title]);

    }

    /** @test */
    function can_get_a_single_book()
    {
        $book = Book::factory()->create();
        $response = $this->getJson(route(  'books.show', $book  ));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $book->title,
        ]);
    }

    /** @test */
    function can_create_book()
    {
        $this->postJson(route(  'books.store'  ), [
            '',
        ])->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
            ]);

        $this->postJson(route(  'books.store'  ),
            ['title'=>'new title']
        )
            ->assertStatus(201)
            ->assertJsonFragment([
                'title' =>'new title',
            ]);
        $this->assertDatabaseHas('books', [
            'title' => 'new title',
        ]);

    }

    /** @test */
    function can_update_book()
    {
        $book = Book::factory()->create();

        $this->patchJson(route(  'books.update', $book  ), [
            '' ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
            ]);

        $this->patchJson(route(  'books.update', $book  ), [
            'title' => 'title updated',
        ])
            ->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'title updated',
            ]);
        $this->assertDatabaseHas('books', [
            'title' => 'title updated',
        ]);
    }

    /** @test */
    function can_delete_book()
    {
        $book = Book::factory()->create();

        $this->deleteJson(route(  'books.destroy', $book  ))
            ->assertStatus(204);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }
}
