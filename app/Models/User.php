<?php

namespace App\Models;

use App\Models\Lead;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
        'last_login',
        'is_active',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'owner');
    }

    public function leadsCreated()
    {
        return $this->hasMany(Lead::class, 'created_by');
    }

    public function is_agent()
    {
        return $this->role === 'agent';
    }

    public function is_manager()
    {
        return $this->role === 'manager';
    }

    public function can_view_lead(Lead $lead)
    {
        if ($this->is_agent()) {
            return $lead->created_by === $this->id;
        }

        if ($this->is_manager()) {
            return true;
        }

        return false;
    }
}
