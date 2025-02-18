<?php

namespace App\Models;

use App\Models\Ingredients;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory ;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'descriptiopn'
    ];
   
    protected $with = ['ingredients'];

      /**
     * @return BelongsToMany
     * @throws \Throwable
     */
    public function ingredients(): BelongsToMany
    {
        
        try {
            return $this->belongsToMany(
                Ingredients::class,
                'ingredient_product',
                'product_id',
                'ingredient_id',
            )->withPivot('amount');

        } catch (\Throwable $exception) {
            Log::error($this->message(
                'Model',
                'Product',
                __function__,
                $exception->getMessage()
            ));
            throw $exception;
        }
    }

    public static function GetAvalilableProducts(){
        return self::select('id','name','description')->where('is_available',1)->get();
    }
}
