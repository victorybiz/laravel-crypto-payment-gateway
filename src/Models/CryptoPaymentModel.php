<?php

namespace Victorybiz\LaravelCryptoPaymentGateway\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoPaymentModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crypto_payments';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'paymentID';


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'paymentID',
        'boxID',
        'boxType',
        'orderID',
        'userID',
        'countryID',
        'coinLabel',
        'amount',
        'amountUSD',
        'unrecognised',
        'addr',
        'txID',
        'txDate',
        'txConfirmed',
        'txCheckDate',
        'processed',
        'processedDate',
        'recordCreated',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'txDate' => 'datetime',
        'txConfirmed' => 'boolean',
        'txCheckDate' => 'datetime',
        'processed' => 'boolean',
        'processedDate' => 'datetime',
        'recordCreated' => 'datetime',
    ];


    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_confirmed',
        'is_processed',
    ]; 
    
    /**
    * Get attribute for is_confirmed
    */
    public function getIsConfirmedAttribute()
    {
        return $this->txConfirmed ? true : false;
    }

    /**
    * Get attribute for is_processed
    */
    public function getIsProcessedAttribute()
    {
        return $this->processed ? true : false;
    }


    /**
     * Set payment status as processed.
     */
    public function setStatusAsProcessed()
    {
        $this->processed = 1;
        $this->processedDate = gmdate('Y-m-d H:i:s');
        $this->save();
    }
}
