<?php

namespace App\Services\Booking;

use App\Models\Booking\Profile;

class ProfileService
{
	public function getProcessingFees($amount)
	{
		$profile = Profile::where('tenant_id', tenant('id'))->first();

		$processing_fee_percentage = $amount * ($profile->stripe_fee_percentage / 100);

		$processing_fee_fixed = $profile->stripe_fee_fixed;

		return [
			'percentage' => $processing_fee_percentage,
			'fixed' => $processing_fee_fixed,
			'fee' => number_format($processing_fee_percentage + $processing_fee_fixed, 2),
			'total' => $amount + $processing_fee_fixed + $processing_fee_percentage
		];
	}
}