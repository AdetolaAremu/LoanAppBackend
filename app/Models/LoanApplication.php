<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function loanType()
    {
        return $this->belongsTo(LoanType::class, 'loan_type_id');
    }

    public function guarantor()
    {
        return $this->hasOne(LoanGuarantor::class);
    }

    public function comment()
    {
        return $this->hasOne(LoanApplicationComment::class);
    }
}
