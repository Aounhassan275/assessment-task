<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Order;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MerchantController extends Controller
{
    public function __construct(
        MerchantService $merchantService
    ) {}

    /**
     * Useful order statistics for the merchant API.
     * 
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        $query = Order::query()->whereBetween('created_at',[$request->from,$request->to]);
        $order_count = $query->count();
        $revenue = $query->sum('subtotal');
        $commissions_owed = Order::whereBetween('created_at',[$request->from,$request->to])->whereNotNull('affiliate_id')->where('payout_status','unpaid')->sum('commission_owed');
        return response()->json([
            'count' => $order_count,
            'revenue' => $revenue,
            'commissions_owed' => $commissions_owed
        ]);
        // TODO: Complete this method
    }
}
