<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking\Voucher;
use App\Services\UtilService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * VOUCHER INDEX PAGE
     *
     * @param  int  $id
     * @return array
     */
    public function index()
    {
        $vouchers = Voucher::all();

        return view('Booking.vouchers.index', compact('vouchers'));
    }

    /**
     * VOUCHER SHOW PAGE
     *
     * @param  int  $id
     * @return array
     */
    public function show($id)
    {
        $voucher = Voucher::find($id);

        return view('Booking.vouchers.show', compact('voucher', 'id'));
    }

    /**
     * VOUCHER NEW PAGE
     *
     * @return View
     */
    public function create()
    {
        return view('Booking.vouchers.new');
    }

    /**
     * VOUCHER INSERT
     *
     * @param  object  $request
     * @return Redirect
     */
    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:191',
            'voucher_code' => 'required|max:191',
            'amount' => 'required|int',
            'amount_type' => 'required',
            'terms' => 'required',
            'is_active' => 'nullable|boolean',
            'usage_limit' => 'nullable|int',
            'expired_at' => 'nullable|date'
        ]);

        if (! isset($validatedData['is_active'])) {
            $validatedData['is_active'] = 0;
        }

        if (isset($payload['expired_at'])) {
            $dateArr = explode('.', $payload['expired_at']);
            $validatedData['expired_at'] = sprintf('%d-%d-%d', $dateArr[2], $dateArr[1], $dateArr[0]);
        }

        $voucher = Voucher::create($validatedData);

        session()->flash('messages', 'Voucher added');

        return redirect('vouchers/' . $voucher->id);
    }

    /**
     * VOUCHER UPDATE
     *
     * @param  int  $id
     * @param  object  $request
     * @return array
     */
    public function update($id, Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:191',
            'voucher_code' => 'required|max:191',
            'amount' => 'required|int',
            'amount_type' => 'required',
            'terms' => 'required',
            'is_active' => 'nullable|boolean',
            'usage_limit' => 'nullable|int',
            'expired_at' => 'nullable|date'
        ]);

        if (! isset($validatedData['is_active'])) {
            $validatedData['is_active'] = 0;
        }

        if (isset($payload['expired_at'])) {
            $dateArr = explode('.', $payload['expired_at']);
            $validatedData['expired_at'] = sprintf('%d-%d-%d', $dateArr[2], $dateArr[1], $dateArr[0]);
        }

        $voucher = Voucher::find($id);

        $voucher->update($validatedData);

        session()->flash('messages', 'Voucher updated');

        return redirect('vouchers/' . $id);
    }

    /**
     * VOUCHER DELETE
     *
     * @param  int  $id
     * @return Redirect
     */
    public function delete($id)
    {
        $voucher = Voucher::find($id);

        $voucher->delete();

        session()->flash('messages', $voucher->voucher_code . ' Voucher removed');

        return redirect('vouchers');
    }

    /**
     * Generate auto pass-code.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateCode(): JsonResponse
    {
        do {
            $uniqueCode = UtilService::alphanumericGenerator(8, false);
        } while (Voucher::where('voucher_code', $uniqueCode)->exists());

        return response()->json($uniqueCode);
    }
}
