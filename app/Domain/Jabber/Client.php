<?php

namespace App\Domain\Jabber;

use App;
use App\Domain\Jabber\Commands\Pool\AssetCommand;
use App\Domain\Jabber\Commands\Pool\HelpCommand;
use App\Domain\Jabber\Commands\Pool\MyAssetsCommand;
use App\Domain\Jabber\Commands\Pool\PingCommand;
use App\Domain\Jabber\Commands\Pool\RateCommand;
use App\Domain\Jabber\Commands\Pool\WalletCommand;
use App\Models\User;
use Carbon\Carbon;
use Enqueue\Redis\RedisConnectionFactory;
use Exception;
use Illuminate\Support\Facades\Log;
use MessageParser;
use Norgul\Xmpp\Options;
use Norgul\Xmpp\XmppClient;
use ResponseParser;

class Client
{
    private $client;
    private $pingTimeout = 60;
    private $notifyTimeout = 60;

    public function __construct()
    {
        $options = new Options();
        $options
            ->setHost(config('services.jabber.host'))
            ->setPort(config('services.jabber.port'))
            ->setUsername(config('services.jabber.username'))
            ->setPassword(config('services.jabber.password'))
            ->setResource(config('services.jabber.resource'))
            ->setUseTls(config('services.jabber.use_tls'));
        $options->setLogger(new Logger());
        $this->client = new XmppClient($options);
        $this->client->connect();
    }

    public function daemon()
    {
        $factory = new RedisConnectionFactory();
        $context = $factory->createContext();
        $queue = $context->createQueue('jabber');
        $consumer = $context->createConsumer($queue);

        $lastResponseAt = Carbon::now();
        $lastNotifyAt = Carbon::now();
        do {
            $rawResponse = $this->client->getResponse();
            if ($rawResponse)
                try {
                    $lastResponseAt = Carbon::now();
                    $response = ResponseParser::parse($rawResponse);
                    if (isset($response['type'])) {
                        if ($response['type'] == 'subscribe') {
                            if (User::where('jabber', $response['content']['from'])->exists()) {
                                $this->client->presence->acceptSubscription($response['content']['from']);
                                sleep(0.5);
                                $this->client->message->send(__('You are welcome! Send !help'), $response['content']['from']);
                            }
                        }
                        if ($response['type'] == 'chat') {
                            $msg = MessageParser::parse($rawResponse);
                            $user = User::where('jabber', $response['content']['from'])->first();
                            if (!isset($user)) {
                                continue;
                            }

                            App::setLocale($user->lang);

                            if ($msg->command() == '!ping') {
                                $this->client->message->send((string)new PingCommand($msg, $user), $user->jabber);
                            }

                            if ($msg->command() == '!help') {
                                $this->client->message->send((string)new HelpCommand($msg, $user), $user->jabber);
                            }

                            if ($msg->command() == '!rate' && count($msg->args()) == 1) {
                                $this->client->message->send((string)new RateCommand($msg, $user), $user->jabber);
                            }

                            if ($msg->command() == '!my' && count($msg->args()) == 1) {
                                $this->client->message->send((string)new MyAssetsCommand($msg, $user), $user->jabber);
                            }

                            if ($msg->command() == '!asset' && count($msg->args()) == 1) {
                                $this->client->message->send((string)new AssetCommand($msg, $user), $user->jabber);
                            }

                            if ($msg->command() == '!wallet' && count($msg->args()) >= 1) {
                                $this->client->message->send((string)new WalletCommand($msg, $user), $user->jabber);
                            }
                        }
                    }
                } catch (Exception $e) {
                    Log::channel('cmdlog')->error(sprintf('%s: %s in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
                }
            if (Carbon::now()->diffInSeconds($lastResponseAt) >= $this->pingTimeout) {
                $lastResponseAt = Carbon::now();
                $this->client->iq->ping();
            }

            try {
                if($message = $consumer->receive(1)) {
                    $jabber = $message->getProperty('jabber');
                    if(!isset($jabber)) {
                        continue;
                    }
                    $this->client->message->send($message->getBody(), $message->getProperty('jabber'));
                    $consumer->acknowledge($message);
                }
            } catch (Exception $e) {
                Log::channel('cmdlog')->error(sprintf('%s: %s in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
            }

            try {
                $messages = Notifier::RateNotifications();
                if (count($messages) > 0) {
                    $this->sendMessages($messages);
                }
            } catch (Exception $e) {
                Log::channel('cmdlog')->error(sprintf('%s: %s in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
            }
            sleep(0.1);
        } while (true);
    }


    public function sendMessages(array $messages)
    {
        foreach ($messages as $recipient => $message) {
            $this->client->message->send($message, $recipient);
            sleep(0.1);
        }
        return $this;
    }

    public function message($message, $recipient)
    {
        $this->client->message->send($message, $recipient);
        return $this;
    }

    public function confirm($jid)
    {
        $this->client->presence->acceptSubscription($jid);
        return $this;
    }

    public function __destruct()
    {
        $this->client->disconnect();
    }


}
