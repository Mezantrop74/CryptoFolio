<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CryptoNotification
 * @package App\Models
 */
class CryptoNotification extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'notification_id', 'crypto_id', 'crypto_observer_id', 'user_id', 'trigger_percent', 'mute_empty', 'last_notified_at'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'last_notified_at' => 'datetime'
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
    public function crypto()
    {
        return $this->belongsTo(Crypto::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function observer()
    {
        return $this->belongsTo(CryptoObserver::class, 'crypto_observer_id');
    }
}
