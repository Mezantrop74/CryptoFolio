<?php

namespace App\Models;

use App\Events\WalletBalanceChanged;
use Brick\Math\RoundingMode;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Kleemans\AttributeEvents;

class Wallet extends Model
{
    use HasFactory, AttributeEvents;


    /**
     * @var string[]
     */
    protected $fillable = ['wallet_id', 'name', 'user_id', 'crypto_id', 'crypto_observer_id', 'balance', 'start_balance', 'note', 'watch_only', 'address'];


    /**
     * @var string[]
     */
    protected $dispatchesEvents = [
        'balance:*' => WalletBalanceChanged::class,
    ];

    /**
     *
     */
    protected static function booted()
    {
        static::addGlobalScope('not-watch-only', function (Builder $builder) {
            $builder->where('watch_only', false);
        });
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeWatchOnly($query)
    {
        return $query->withoutGlobalScope('not-watch-only')->where('watch_only', true);
    }

    /**
     * @param $value
     * @return Money
     * @throws UnknownCurrencyException
     */
    public function getBalanceAttribute($value)
    {
        try {
            return Money::of($value, $this->crypto->currency, null, RoundingMode::DOWN);
        } catch (UnknownCurrencyException $e) {
            Log::error($e);
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * @param $value
     * @return Money
     * @throws UnknownCurrencyException
     */
    public function getStartBalanceAttribute($value)
    {
        return Money::of($value, $this->crypto->currency);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function crypto()
    {
        return $this->belongsTo(Crypto::class);
    }

    /**
     * @return BelongsTo
     */
    public function observer()
    {
        return $this->belongsTo(CryptoObserver::class);
    }

    /**
     * @return HasMany
     */
    public function sentExchanges()
    {
        return $this->hasMany(Exchange::class, 'sender_wallet_id');
    }

    /**
     * @return HasMany
     */
    public function receivedExchanges()
    {
        return $this->hasMany(Exchange::class, 'receiver_wallet_id');
    }
}
