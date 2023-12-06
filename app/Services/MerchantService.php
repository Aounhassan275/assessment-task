<?php

namespace App\Services;

use App\Jobs\PayoutOrderJob;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class MerchantService
{
    /**
     * Register a new user and associated merchant.
     * Hint: Use the password field to store the API key.
     * Hint: Be sure to set the correct user type according to the constants in the User model.
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return Merchant
     */
    public function register(array $data): Merchant
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['api_key'],
            'type' => User::TYPE_MERCHANT,
        ]);
        $merchant = new Merchant();
        $merchant->user_id = $user->id;
        $merchant->domain = $data['domain'];
        $merchant->display_name = $data['name'];
        $merchant->save();
        return $merchant;
    }

    /**
     * Update the user
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return void
     */
    public function updateMerchant(User $user, array $data)
    {
        $merchant = Merchant::where('user_id',$user->id)->first();
        $merchant->domain = $data['domain'];
        $merchant->display_name = $data['name'];
        $merchant->save();
        return $merchant;
        // TODO: Complete this method
    }

    /**
     * Find a merchant by their email.
     * Hint: You'll need to look up the user first.
     *
     * @param string $email
     * @return Merchant|null
     */
    public function findMerchantByEmail(string $email): ?Merchant
    {
        return Merchant::query()->join('users','merchants.user_id','users.id')
            ->where('users.email',$email)->first();
        // TODO: Complete this method
    }

    /**
     * Pay out all of an affiliate's orders.
     * Hint: You'll need to dispatch the job for each unpaid order.
     *
     * @param Affiliate $affiliate
     * @return void
     */
    public function payout(Affiliate $affiliate)
    {
        $orders = Order::where('affiliate_id',$affiliate->id)->where('payout_status',Order::STATUS_UNPAID)->get();
        foreach($orders as $order)
        {
            dispatch(new PayoutOrderJob($order));
        }
        // TODO: Complete this method
    }
}
