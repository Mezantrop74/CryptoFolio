<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Rate
 * @package App\Models
 */
class Rate extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['crypto_id', 'rate', 'ticker_type'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function crypto()
    {
        return $this->belongsTo(Crypto::class);
    }
}
