<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $table = 'supplier_payments';

    /**
     * This project uses legacy datetime columns (created_at, updated_at) but not Laravel timestamps behavior.
     */
    public $timestamps = false;

    protected $fillable = [
        'SCID',
        'admission_id',
        'sale_id',
        'amount',
        'remarks',
        'created_by',
        'created_at',
        'is_posted',
        'posted_on',
        'updated_by',
        'updated_at',
        'is_active',
        'is_sync',
    ];

    public function supplier()
    {
        // Supplier record is in sup_cus_details, and model is App\Models\Customer
        return $this->belongsTo(Customer::class, 'SCID', 'SCID');
    }
}
