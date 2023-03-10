<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use ExtendedModel;
    
    protected $table = 'categories';

    protected $fillable = ['name'];

    public function films()
    {
        return $this->belongsToMany(Film::class, 'film_category');
    }
}
