<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class MigrateController extends Controller
{
    public function index(): string
    {
        $key = getenv('MIGRATION_KEY');
        if (! $key || $this->request->getGet('key') !== $key) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $migrate = \Config\Services::migrations();

        try {
            $migrate->latest();
            return 'Migrations ran successfully.';
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setBody('Migration failed: ' . $e->getMessage());
        }
    }
}
