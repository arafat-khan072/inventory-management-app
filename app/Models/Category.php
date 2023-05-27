<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use League\Glide\Server;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class Category extends Model
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

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    // protected static function booted()
    // {
    //     // static::created(function ($user) {
    //     //     //
    //     // });

    //     // static::creating(function ($model) {
    //     //     if (empty($model->slug)) {
    //     //         $slug = $model->createSlug($model->title);
    //     //     } else {
    //     //         $slug = $model->slug;
    //     //     }
    //     //     $model->slug = $slug;
    //     // });
    // }

    // public function createSlug($title, $id = 0)
    // {
    //     $slug     = Str::slug($title);
    //     $allSlugs = $this->getRelatedSlugs($slug, $id);
    //     if (!$allSlugs->contains('slug', $slug)) {
    //         return $slug;
    //     }

    //     $i          = 1;
    //     $is_contain = true;
    //     do {
    //         $newSlug = $slug . '-' . $i;
    //         if (!$allSlugs->contains('slug', $newSlug)) {
    //             $is_contain = false;
    //             return $newSlug;
    //         }
    //         $i++;
    //     } while ($is_contain);
    // }

    // protected function getRelatedSlugs($slug, $id = 0)
    // {
    //     return static::select('slug')->where('slug', 'like', $slug . '%')
    //         ->where('id', '<>', $id)
    //         ->get();
    // }

}
