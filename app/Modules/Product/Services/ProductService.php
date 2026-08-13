<?php

namespace App\Modules\Product\Services;

use App\Modules\Core\Services\BaseService;
use App\Modules\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ProductService extends BaseService
{
    public function __construct(ProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function listForIndex(int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function createProduct(array $data): Model
    {
        $data['created_by'] = Auth::id();

        return $this->repository->create($data);
    }

    public function updateProduct(int $id, array $data): Model
    {
        return $this->repository->update($id, $data);
    }
}
