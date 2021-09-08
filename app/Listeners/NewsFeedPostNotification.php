<?php

namespace App\Listeners;

use App\Events\NewsFeedPostCreated;
use App\Models\NewsFeed\Subscription;
use Enqueue\Redis\RedisConnectionFactory;

class NewsFeedPostNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(NewsFeedPostCreated $event)
    {
        $post = $event->post;
        $subs = Subscription::where([
            'source_id' => $post->source_id,
            'with_notify' => true,
        ])->with(['user', 'source'])->get();
        if (count($subs) == 0) {
            return;
        }
        $factory = new RedisConnectionFactory();
        $context = $factory->createContext();
        $queue = $context->createQueue('jabber');
        foreach ($subs as $sub) {
            if (!isset($sub->user->jabber)) {
                continue;
            }
            $message = $context->createMessage(sprintf("%s:\n%s", $sub->source->name, $post->content), ['jabber' => $sub->user->jabber]);
            $context->createProducer()->send($queue, $message);
        }
    }
}
