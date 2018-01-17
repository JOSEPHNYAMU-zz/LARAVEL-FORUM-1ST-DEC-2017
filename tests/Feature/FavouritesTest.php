<?php

namespace Tests\Feature;

use App\Reply;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FavouritesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function guest_cannot_favourite_anything()
    {
        $this->withExceptionHandling()
            ->post('replies/1/favourites')
            ->assertRedirect('/login');
    }

    /** @test */
    function an_authenticated_user_can_favourite_any_reply()
    {
        $this->signIn();

        $reply =  create(Reply::class);

        $this->post("replies/{$reply->id}/favourites");

        $this->assertCount(1, $reply->favourites);
    }

    /** @test */
    function an_authenticated_user_may_only_favourite_a_reply_only_once()
    {
        $this->signIn();

        $reply = create(Reply::class);

        try
        {
            $this->post("replies/{$reply->id}/favourites");

            $this->post("replies/{$reply->id}/favourites");
        }
        catch (\Exception $exception)
        {
            $this->fail('Cannot favourite the same reply more than once.');
        }

        $this->assertCount(1, $reply->favourites);
    }

    /** @test */
    function an_authenticated_user_can_unfavourite_a_reply()
    {
        $this->signIn();

        $reply =  create(Reply::class);

        $reply->favourite();

        $this->delete("replies/{$reply->id}/favourites");

        $this->assertCount(0, $reply->favourites);
    }
}