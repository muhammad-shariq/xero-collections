<?php

namespace App\Services;
use App\Repositories\RoleRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class RoleService{

    private $roleRepository;

    public function __construct(RoleRepository $roleRepository){
        $this->roleRepository = $roleRepository;
    }

    public function getAll($pagination = false){
        if($pagination){
            return $this->roleRepository->paginate();
        }
        return $this->roleRepository->all();
    }

}
