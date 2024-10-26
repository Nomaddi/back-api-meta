<?php
namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN'
        ];

        if ($request->isMethod('OPTIONS')) {
            // Si es una solicitud OPTIONS (preflight), devolvemos una respuesta 200 con los encabezados CORS
            return response()->json('{"status":"OK"}', 200, $headers);
        }

        // Para todas las demás solicitudes (POST, GET, etc.), continuamos con la respuesta normal
        $response = $next($request);

        // Añadimos los encabezados CORS a la respuesta
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }
}
