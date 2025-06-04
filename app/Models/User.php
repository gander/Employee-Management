<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'average_annual_salary',
        'position',
        'residential_address_country',
        'residential_address_postal_code',
        'residential_address_city',
        'residential_address_house_number',
        'residential_address_apartment_number',
        'different_correspondence_address',
        'correspondence_address_country',
        'correspondence_address_postal_code',
        'correspondence_address_city',
        'correspondence_address_house_number',
        'correspondence_address_apartment_number',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'average_annual_salary' => 'decimal:2',
            'different_correspondence_address' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public static function rules()
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'average_annual_salary' => 'nullable|numeric|decimal:0,2',
            'position' => 'required|in:front-end,back-end,pm,designer,tester',
            'residential_address_country' => 'required|string|max:255',
            'residential_address_postal_code' => 'required|string|max:20',
            'residential_address_city' => 'required|string|max:255',
            'residential_address_house_number' => 'required|string|max:20',
            'residential_address_apartment_number' => 'nullable|string|max:20',
            'different_correspondence_address' => 'required|boolean',
            'correspondence_address_country' => 'required_if:different_correspondence_address,true|string|max:255',
            'correspondence_address_postal_code' => 'required_if:different_correspondence_address,true|string|max:20',
            'correspondence_address_city' => 'required_if:different_correspondence_address,true|string|max:255',
            'correspondence_address_house_number' => 'required_if:different_correspondence_address,true|string|max:20',
            'correspondence_address_apartment_number' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ];
    }
}
