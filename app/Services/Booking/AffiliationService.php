<?php

namespace App\Services\Booking;

use App\Models\Booking\Affiliation;

class AffiliationService
{
	public static function checkAffiliationSession()
	{
		if (!session()->has('affiliation_id') || session('affiliation_id') == '') {
			return null;
		}

		$id = session('affiliation_id');
		$hash = session('affiliation_hash');

		$affiliation = Affiliation::find($id);

		if (!$affiliation) {
			return null;
		}

		if ($affiliation->hash !== $hash) {
			return null;
		}

		return $affiliation->id;
	}
}