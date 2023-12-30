<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'proposal_id' => 'integer',
        'user_id' => 'integer',
        'vote' => 'integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Proposal, Vote>
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Vote>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
