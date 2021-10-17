<?php

namespace MyApp\Repos;

use PhalconRepositories\AbstractRepository;
use MyApp\Models\User;

class ServiceRepository extends AbstractRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
