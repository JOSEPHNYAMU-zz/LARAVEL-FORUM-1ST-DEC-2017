<?php

namespace Tests\Unit;

use App\Channel;
use App\Notifications\ThreadWasUpdated;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;


class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    public function setUp()
    {
        parent::setUp();

        $this->thread = factory(Thread::class)->create();
    }

    /** @test */
    function a_thread_can_make_a_string_path()
    {
        $thread = create(Thread::class);

        $this->assertEquals('/threads/'. $thread->channel->slug . '/' . $thread->id, $thread->path());
    }

    /** @test */
    function a_thread_has_a_creator()
    {
        $this->assertInstanceOf(User::class, $this->thread ->creator);
    }

    /** @test */
    function a_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread ->replies);
    }

    /* @test */
    function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'Foobar',

            'user_id' => 1,
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /** @test */
    function a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {

        Notification::fake();

        $this->signIn()

            ->thread

            ->subscribe()

            ->addReply([

            'body' => 'Foobar',

            'user_id' => 999,

        ]);

        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);

    }

    /** @test */
    function a_thread_has_a_channel()
    {
        $thread = create(Thread::class);

        $this->assertInstanceOf(Channel::class, $thread->channel);
    }

    /** @test */
    function a_thread_can_be_subscribed_to()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $thread->subscribe();

        $this->assertEquals(
            1,
            $thread->subscriptions()->where('user_id', auth()->id())->count()
        );
    }

    /** @test */
    function a_thread_can_be_unsubscribed_from()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $thread->subscribe(auth()->id());

        $thread->unsubscribe(auth()->id());

        $this->assertCount(0,  $thread->subscriptions);

    }


    /** @test */
    function it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        $thread = create(Thread::class);

        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /** @test */
    function a_user_can_unsubscribe_from_a_thread()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $thread->subscribe();

        $this->delete($thread->path() . '/subscribe');

        $this->assertCount(0, $thread->subscriptions);
    }

    /** @test */
    function a_thread_can_check_if_the_authenticated_user_has_read_all_the_replies(){

        $this->signIn();

        $thread = create('App\Thread');

        tap(auth()->user(), function ($user) use ($thread) {


            $this->assertTrue($thread->hasUpdatesFor($user));

            $user->read($thread);

            $this->assertFalse($thread->hasUpdatesFor($user));


        });
    }
}
