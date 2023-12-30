<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Proposal extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'status' => Status::class,
        'proposed_authors' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Proposal>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Vote>
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ProposalComment>
     */
    public function proposalComments(): HasMany
    {
        return $this->hasMany(ProposalComment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User>
     */
    public function reviewers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'reviewers', 'proposal_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<ProposalTopic>
     */
    public function topic(): HasOne
    {
        return $this->hasOne(ProposalTopic::class);
    }
}
