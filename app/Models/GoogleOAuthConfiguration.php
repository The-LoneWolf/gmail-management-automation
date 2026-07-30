<?php

namespace App\Models;

use Database\Factories\GoogleOAuthConfigurationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'client_id', 'client_secret', 'redirect_uri', 'scopes', 'is_active'])]
class GoogleOAuthConfiguration extends Model
{
    /** @use HasFactory<GoogleOAuthConfigurationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'client_secret' => 'encrypted',
            'scopes' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
