<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\JWTAuthService;

class AuthController extends ResourceController
{
    use ResponseTrait;
    
    protected $format = 'json';
    // protected $userModel;

    // public function __construct()
    // {
    //     $this->userModel = new UserModel();
    // }

    public function register()
    {
        $rules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'password' => 'required|min_length[8]',
        ];
        
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        
        $data = [
            'email' => $this->request->getVar('email'),
            'username' => $this->request->getVar('username'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
        ];
        
        $userModel = new UserModel();
        $userModel->save($data);
        
        return $this->respondCreated([
            'status' => 201,
            'message' => 'Usuario registrado correctamente',
            'user_id' => $userModel->getInsertID()
        ]);
    }


    public function login()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }
        
        $userModel = new UserModel();
        $user = $userModel->where('email', $this->request->getVar('email'))->first();
        
        if (!$user) {
            return $this->failNotFound('Email no encontrado');
        }
        
        $verify = password_verify($this->request->getVar('password'), $user['password']);
        
        if (!$verify) {
            return $this->fail('Contraseña incorrecta', 401);
        }
        
        $key = getenv('JWT_SECRET');
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600, // Token válido por 1 hora
            'uid' => $user['id'],
        ];
        
        $token = JWT::encode($payload, $key, 'HS256');
        
        return $this->respond([
            'status' => 200,
            'message' => 'Login correcto',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'username' => $user['username']
            ]
        ]);
    }
    

    public function profile()
    {
        # Obtener ID de usuario del servicio de autenticación
        $userId = JWTAuthService::getInstance()->getUserId();

        $userModel = new UserModel();
        $user = $userModel->find($userId);
        
        if (!$user) 
        {
            return $this->failNotFound('Usuario no encontrado');
        }
        
        return $this->respond([
            'status' => 200,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'username' => $user['username']
            ]
        ]);
    }

}
