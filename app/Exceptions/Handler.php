<?php
namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    // Autres méthodes...

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthenticationException) {
            // Vérifie si la requête attend une réponse JSON (API)
            if ($request->expectsJson()) {
                // Renvoie une réponse JSON avec un message d'erreur
                return response()->json([
                    'message' => 'Unauthenticated. Please provide a valid token.'
                ], 401); // 401 Unauthorized
            }

            // Si la requête n'attend pas un JSON (par exemple pour les vues web), redirige vers la page de login
            return redirect()->guest(route('login'));
        }

        return parent::render($request, $exception);
    }
}
