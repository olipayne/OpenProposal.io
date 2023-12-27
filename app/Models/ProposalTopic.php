<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalTopic extends Model
{
    use HasFactory;

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'users_topics', 'proposal_topic_id', 'user_id');
    }
}
