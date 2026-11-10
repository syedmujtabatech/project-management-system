<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'tagline',
        'company_email',
        'company_phone',
        'website',
        'address',
        'currency',
        'currency_symbol',
        'timezone',
    ];

    public static function get(): self
    {
        return self::firstOrCreate([], [
            'company_name' => 'ApexPMS Enterprise Inc.',
            'tagline' => 'Next-Gen Project Management & Corporate Collaboration',
            'company_email' => 'contact@apexcompany.com',
            'company_phone' => '+1 (800) 555-APEX',
            'website' => 'https://apexcompany.com',
            'address' => '742 Evergreen Terrace, Tech Park, Suite 400, New York, NY',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'timezone' => 'America/New_York',
        ]);
    }
}
