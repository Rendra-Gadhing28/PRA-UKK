<?php

namespace App\Http\Controllers\User;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ClaimVoucherRequest;
use App\Models\Vouchers;
use App\Services\User\UserVoucherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Thin Controller — hanya menangani Request & Response.
 * Seluruh logika bisnis didelegasikan ke UserVoucherService.
 */
class UserVoucherController extends Controller
{
    public function __construct(
        private readonly UserVoucherService $voucherService
    ) {}

    // -------------------------------------------------------------------------

    /**
     * GET /user/vouchers
     * Tampilkan halaman daftar voucher beserta tab-tab kategori.
     */
    public function index(ClaimVoucherRequest $request): \Illuminate\View\View
    {
        $data = $this->voucherService->getIndexData(
            user  : Auth::user(),
            search: $request->string('search')->trim()->value() ?: null,
        );

        return view('user.vouchers.index', [
            ...$data,
            'user' => Auth::user(),
        ]);
    }

    // -------------------------------------------------------------------------

    /**
     * POST /user/vouchers/{voucher}/claim
     * Proses klaim voucher oleh user yang sedang login.
     */
    public function claim(ClaimVoucherRequest $request, Vouchers $voucher): RedirectResponse
    {
        $result = $this->voucherService->claim(Auth::user(), $voucher);

        match ($result['success']) {
            true  => ToastHelper::success($result['message']),
            false => match ($result['type']) {
                'duplicate' => ToastHelper::info($result['message']),
                default     => ToastHelper::error($result['message']),
            },
        };

        return redirect()->back();
    }
}