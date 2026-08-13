<?php

namespace App\Modules\Product\Repositories;

use App\Modules\Core\Repositories\BaseRepository;
use App\Modules\Product\Models\Product;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
