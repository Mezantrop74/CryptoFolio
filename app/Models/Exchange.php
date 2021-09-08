<?php

namespace App\Models;

use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Exchange
 * @package App\Models
 */
class Exchange extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'exchange_id',
        'user_id',
        'sender_crypto_id',
        'sender_crypto_observer_id',
        'sender_rate_id',
        'sender_wallet_id',
        'sender_amount',
        'receiver_crypto_id',
        'receiver_crypto_observer_id',
        'receiver_rate_id',
        'receiver_wallet_id',
        'receiver_amount',
        'commission',
        'profit',
        'note'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function senderObserver()
    {
        return $this->belongsTo(CryptoObserver::class, 'sender_crypto_observer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiverObserver()
    {
        return $this->belongsTo(CryptoObserver::class, 'receiver_crypto_observer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function senderWallet()
    {
        return $this->belongsTo(Wallet::class, 'sender_wallet_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiverWallet()
    {
        return $this->belongsTo(Wallet::class, 'receiver_wallet_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function senderRate()
    {
        return $this->belongsTo(Rate::class, 'sender_rate_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiverRate()
    {
        return $this->belongsTo(Rate::class, 'receiver_rate_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function senderCrypto()
    {
        return $this->belongsTo(Crypto::class, 'sender_crypto_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiverCrypto()
    {
        return $this->belongsTo(Crypto::class, 'receiver_crypto_id');
    }

    /**
     * @return string
     */
    public function getSenderUsdAmountAttribute()
    {
        return (string)BigDecimal::of($this->sender_amount)->multipliedBy($this->senderRate->rate);
    }

    /**
     * @return string
     */
    public function getReceiverUsdAmountAttribute()
    {
        return (string)BigDecimal::of($this->receiver_amount)->multipliedBy($this->receiverRate->rate);
    }
}
