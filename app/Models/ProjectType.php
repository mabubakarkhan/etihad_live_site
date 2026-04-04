<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class ProjectType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'show_in_projects',
        'show_in_properties',
        'show_in_dealers',
    ];

    protected function casts(): array
    {
        return [
            'show_in_projects' => 'boolean',
            'show_in_properties' => 'boolean',
            'show_in_dealers' => 'boolean',
        ];
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_project_type')->withTimestamps();
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_project_type')->withTimestamps();
    }
}
