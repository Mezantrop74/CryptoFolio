<?php

namespace App\Models;

use Brick\Money\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Crypto
 * @package App\Models
 */
class Crypto extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['cmc_id', 'name', 'symbol', 'slug', 'platform', 'watch_rate', 'ticker_type'];
    /**
     * @var string[]
     */
    protected $casts = [
        'platform' => 'array'
    ];

    /**
     * @return Currency
     */
    public function getCurrencyAttribute()
    {
        return new Currency(
            substr($this->symbol, 0, 3), // length > 3 is the reason of intl number formatter exception
            $this->id,
            $this->name,
            14
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function observers()
    {
        return $this->hasMany(CryptoObserver::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function currentRate()
    {
        return $this->hasMany(Rate::class)->latest();
    }
}
