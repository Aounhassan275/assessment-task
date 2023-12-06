<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        $merchant = Merchant::where('domain',$data['merchant_domain'])->first();
        if(@$data['customer_email'])
        {
            $isAlreadyExist = $this->getAffiliateUser($data['customer_email']);
            if(!$isAlreadyExist)
            {
                $affiliate = $this->registerAffilate($merchant, $data);
            }else{
                $affiliate = $isAlreadyExist;
            }
        }else{
            $affiliate = $this->registerAffilate($merchant, $data);
        }
        if($this->checkDuplicatOrder($data['order_id']))
        {
            return true;
        }
        $this->createOrder($merchant,$affiliate,$data);
        // TODO: Complete this method
    }
    private function checkDuplicatOrder($order_id)
    {
        return Order::where('external_order_id',$order_id)->first() ? true : false;
    }
    private function getAffiliateUser($email)
    {
        return Affiliate::query()->join('users','affiliates.user_id','users.id')
                ->where('users.email',$email)->first();
    }
    private function createOrder($merchant,$affiliate,$data)
    {
        $dataRequest = [
            'merchant_id' => $merchant->id,
            'affiliate_id' => $affiliate->id,
            'subtotal' => $data['subtotal_price'],
            'commission_owed' => $data['subtotal_price'] * $affiliate->commission_rate,
            'discount_code' => @$data['discount_code'],
            'payout_status' => Order::STATUS_UNPAID,
            'external_order_id' => @$data['order_id'],
        ];
        Order::create($dataRequest);
    }
    private function registerAffilate($merchant, $data)
    {
        $user = User::create([
            'name' => $data['customer_name'],
            'email' => $data['customer_email'],
            'type' => User::TYPE_AFFILIATE,
        ]);
        $data = [
            'user_id' => $user->id,
            'merchant_id' => $merchant->id,
            'commission_rate' => 0.1,
            'discount_code' => $data['discount_code'],
        ];
        return Affiliate::create($data);
    }
}
