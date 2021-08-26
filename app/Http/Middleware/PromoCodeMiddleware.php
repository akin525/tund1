<?php

namespace App\Http\Middleware;

use App\Models\PromoCode;
use Closure;
use Illuminate\Http\Request;

class PromoCodeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $input = $request->all();

        if (!isset($input['promo'])) {
            return response()->json(['success' => 0, 'message' => 'Add promo to your request']);
        }

        if ($input['promo'] != "0") {
            $pc = PromoCode::where('code', $input['promo'])->first();
            if (!$pc) {
                return response()->json(['success' => 0, 'message' => 'Invalid Promo Code']);
            }

            if ($pc->used == 1) {
                return response()->json(['success' => 0, 'message' => 'Promo Code has been used']);
            }
        }

        return $next($request);
    }
}
