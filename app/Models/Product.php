<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use League\Glide\Server;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
    }

    public function imageUrl(array $attributes = [])
    {
        if ($this->image) {
            return URL::to(App::make(Server::class)->fromPath($this->image, $attributes));
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
