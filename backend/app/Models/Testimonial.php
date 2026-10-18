<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'role_or_company', 'avatar', 'rating', 'content', 'is_featured', 'sort_order'])]
class Testimonial extends Model
{
    use HasFactory;

    protected $casts = [
        'rating'      => 'integer',
        'is_featured' => 'boolean',
        'sort_order'  => 'integer',
    ];

    /**
     * Get user initials for avatar placeholder
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= strtoupper(substr($w, 0, 1));
        }
        return $initials ?: 'JG';
    }
}
