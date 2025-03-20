<?php
// app/Filters/JWTAuthFilter.php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Config\Services;
use Firebase\JWT\Key;
use App\Libraries\JWTAuthService;

class JWTAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getServer('HTTP_AUTHORIZATION');
        
        if (!$header) 
        {
            return Services::response()
                ->setJSON([
                    'status' => 401,
                    'error' => 'Token de autenticación no proporcionado'
                ])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
        
        try 
        {
            $token = explode(' ', $header)[1];
            $key = getenv('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            
            debug($token);
            debug( $key);
            debug($decoded);
            die;
            
            # Almacenar la información del token en el servicio singleton
            JWTAuthService::getInstance()->setData($decoded, $token);
            
            return $request;

        } 
        catch (ExpiredException $e) 
        {
            return Services::response()
                ->setJSON([
                    'status' => 401,
                    'error' => 'Token expirado'
                ])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        } 
        catch (\Exception $e) 
        {
            return Services::response()
                ->setJSON([
                    'status' => 401,
                    'error' => 'Token inválido: ' . $e->getMessage()
                ])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se necesita hacer nada después
    }
}