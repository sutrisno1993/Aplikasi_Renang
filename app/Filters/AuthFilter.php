<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('auth'));
        }

        // Role "boss" bersifat read-only, kecuali approval layer 2 pembayaran.
        if (session()->get('role') === 'boss') {
            $path = trim($request->getUri()->getPath(), '/');
            if (str_starts_with($path, 'index.php/')) {
                $path = substr($path, strlen('index.php/'));
            }

            $method = strtolower($request->getMethod());
            $isBossApprovalRoute = (bool) preg_match('#^admin/pembayaran/(confirm-boss/\d+|reject-boss/\d+|complete-month-boss)$#', $path);

            // Boss hanya boleh request non-GET di endpoint approval boss.
            if ($method !== 'get' && ! $isBossApprovalRoute) {
                return redirect()->to(base_url('admin/dashboard'))
                    ->with('error', 'Akses boss hanya baca data. Perubahan hanya untuk approval pembayaran boss.');
            }

            // Walau GET, blok endpoint tulis yang memakai link langsung.
            $isWriteLikeGetRoute = (bool) preg_match(
                '#^admin/.+/(save|store|update|delete|approve|reject|create|edit)(/|$)#',
                $path
            );

            if ($isWriteLikeGetRoute && ! $isBossApprovalRoute) {
                return redirect()->to(base_url('admin/dashboard'))
                    ->with('error', 'Akses boss hanya baca data. Endpoint ini khusus admin operasional.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}