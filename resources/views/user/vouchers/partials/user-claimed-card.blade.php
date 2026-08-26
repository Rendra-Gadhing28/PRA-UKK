@include('user.vouchers.partials.voucher-card', ['v' => $v, 'claimedVoucherIds' => [$v->id], 'user' => auth()->user(), 'userVoucher' => $uv])
