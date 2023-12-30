<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Proposal>
     */
    public function proposalsToReview(): BelongsToMany
    {
        return $this->belongsToMany(Proposal::class, 'reviewers', 'user_id', 'proposal_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Proposal>
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<ProposalTopic>
     */
    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(ProposalTopic::class, 'users_topics', 'user_id', 'proposal_topic_id');
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            // Set a blank password if one isn't provided, normally when creating new reviewers
            if (empty($user->password)) {
                $user->password = '';
            }
        });

        static::saving(function ($user) {
            if (! $user->is_reviewer) {
                $user->is_default_reviewer = false;
                // Remove the user from any ProposalTopic they were a default reviewer for
                $user->topics()->detach();

            }

        });
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        // If the user is an admin, they can access all panels
        if ($this->is_admin) {
            return true;
        }

        // If the user is a reviewer, they can access the reviewer panel
        if ($this->is_reviewer && $panel->getId() === 'reviewer') {
            return true;
        }

        // If the user has no special permissions, they can access the general panel
        if ($panel->getId() === 'proposer') {
            return true;
        }

        return false;
    }
}
