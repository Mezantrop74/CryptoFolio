<?php

namespace App\Console\Commands;

use App\Domain\Telegram\Parser\Parser;
use App\Models\NewsFeed\Post;
use App\Models\NewsFeed\Source;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ParseTelegramChannels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sources = Source::where('source_type', array_keys(config('newsfeed.source_types'))[0])->whereHas('subscriptions')->get();
        foreach ($sources as $source) {
            $messages = (new Parser(new Client()))->getMessages($source->link);
            foreach ($messages as $m) {
                try {
                    Post::where([
                        'source_id' => $source->id,
                        'origin_post_id' => $m->id,
                    ])->firstOr(function () use ($m, &$source) {
                        (new Post([
                            'post_id' => Str::uuid(),
                            'origin_post_id' => $m->id,
                            'source_id' => $source->id,
                            'content' => $m->text,
                            'posted_at' => $m->publication_date,
                        ]))->save();
                        $source->last_post_at = Carbon::now();
                    });
                } catch (\Exception $e) {
                    Log::channel('cmdlog')->error(sprintf('%s: %s in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
                }
                $source->save();
            }

            sleep(1);
        }

        return 0;
    }
}
