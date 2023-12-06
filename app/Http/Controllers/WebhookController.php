<?php

namespace App\Http\Controllers;

use App\Services\AffiliateService;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Pass the necessary data to the process order method
     * 
     * @param  Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        try{
            $this->orderService->processOrder($request->all());
            return response()->json(['success'=>true,'message'=>'Order Process Successfully']);
        }catch(Exception $e)
        {
            return response()->json(['success'=>false,'message'=> $e->getMessage()],422);
        }
        // TODO: Complete this method
    }
}
