<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalComment extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'proposal_id' => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Proposal, ProposalComment>
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, ProposalComment>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
