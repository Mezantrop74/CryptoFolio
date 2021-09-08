<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CryptoObserver
 * @package App\Models
 */
class CryptoObserver extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['observer_id', 'user_id', 'crypto_id', 'ticker_type'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function crypto()
    {
        return $this->belongsTo(Crypto::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * @return mixed
     */
    public function watchOnlyWallets()
    {
        return $this->hasMany(Wallet::class)->withoutGlobalScope('not-watch-only')->watchOnly();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentExchanges()
    {
        return $this->hasMany(Exchange::class, 'sender_crypto_observer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedExchanges()
    {
        return $this->hasMany(Exchange::class, 'receiver_crypto_observer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(CryptoNotification::class);
    }
}
