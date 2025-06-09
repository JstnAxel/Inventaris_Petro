<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class Stationary extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'stock',
        'image',
        'unit',
        'user_id',
        'note',
        'price'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function removeVowels($string)
    {
        return preg_replace('/[AEIOUaeiou]/', '', $string);
    }

    public static function generateCode($categoryName, $lastNumber)
    {
        $number = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        $consonants = strtoupper(self::removeVowels($categoryName));
        $year = now()->year;
        $month = now()->format('m');

        return "{$number}/{$consonants}/{$year}/{$month}";
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $category = \App\Models\Category::find($model->category_id);

            $prefix = strtoupper(self::removeVowels($category->name));

            $lastCode = self::where('code', 'like', '%/' . $prefix . '/%')
                ->orderByRaw('CAST(SUBSTRING_INDEX(code, "/", 1) AS UNSIGNED) DESC')
                ->value('code');

            $lastNumber = 0;
            if ($lastCode) {
                $lastNumber = (int) explode('/', $lastCode)[0];
            }

            $model->code = self::generateCode($category->name, $lastNumber);
            $model->user_id = Auth::id() ?? 1;
        });

        static::updated(function ($model) {
            if ($model->wasChanged('image')) {
                $oldImage = $model->getOriginal('image');
                if (!empty($oldImage)) {
                    $disk = 'public';
                    if (Storage::disk($disk)->exists($oldImage)) {
                        Storage::disk($disk)->delete($oldImage);
                    }
                }
            }
        });

        static::deleting(function ($model) {
            if ($model->isForceDeleting()) {
                if ($model->image) {
                    Storage::disk('public')->delete($model->image);
                }
            }
        });
    }
}
