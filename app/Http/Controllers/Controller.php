<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use PDOException;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected function databaseErrorResponse(PDOException $exception, string $operation): Response
    {
        $this->logDatabaseFailure($exception, $operation);

        return response()->view('errors.database', status: 503);
    }

    protected function databaseErrorRedirect(PDOException $exception, string $operation): RedirectResponse
    {
        $this->logDatabaseFailure($exception, $operation);

        return back()
            ->with('error', 'Data gagal diproses karena terjadi gangguan database. Silakan coba lagi.')
            ->withInput();
    }

    private function logDatabaseFailure(PDOException $exception, string $operation): void
    {
        Log::error('Database operation failed.', [
            'operation' => $operation,
            'exception' => $exception::class,
        ]);
    }
}
