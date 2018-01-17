<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    function a_notification_is_prepared_when_a_subscribed_thread_receives_a_reply_that_is_not_by_the_current_user()
    {
        $thread = create(Thread::class)->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Some reply body here',
        ]);

        $this->assertCount(0, auth()->user()->fresh()->notifications);

        $thread->addReply([
            'user_id' =>  create(User::class)->id,
            'body' => 'Some reply body here',
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    function a_user_can_fetch_their_unread_notification()
    {
//          create(DatabaseNotification::class);

        $thread = create(Thread::class)->subscribe();

        $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Some reply body here',
        ]);

        $this->assertCount(
            1,
            $this->getJson("/profiles/". auth()->user()->name . "/notifications")->json()
        );
    }

    /** @test */
    function a_user_can_mark_a_notification_as_read()
    {
//        create(DatabaseNotification::class);
        $thread = create(Thread::class)->subscribe();

        $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Some reply body here',
        ]);

        $user = auth()->user();

        $this->assertCount(1,  $user->unreadNotifications);

        $notificationId =  auth()->user()->unreadNotifications->first()->id;

        $this->delete("/profiles/" . $user->name ."/notifications/{$notificationId}");

        $this->assertCount(0, $user->fresh()->unreadNotifications);

    }
}
