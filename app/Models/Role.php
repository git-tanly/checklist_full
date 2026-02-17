<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\LocalUser;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    // Override relasi 'users' bawaan Spatie
    // Agar dia menunjuk ke LocalUser (mysql lokal), bukan User (mysql_portal)
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            LocalUser::class, // Target Model: LocalUser
            'model',          // Nama relasi di tabel pivot (model_id, model_type)
            config('permission.table_names.model_has_roles'),
            'role_id',
            'model_id'
        );
    }
}
