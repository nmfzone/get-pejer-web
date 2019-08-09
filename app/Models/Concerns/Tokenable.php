<?php

namespace App\Models\Concerns;

use App\Models\Token;

trait Tokenable
{
    /**
     * Get all of the tokens for the model's.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tokens()
    {
        return $this->morphToMany(Token::class, 'tokenable');
    }
}
