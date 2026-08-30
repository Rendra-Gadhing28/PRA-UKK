---
target: admin/vouchers page
total_score: 19
max_score: 36
na_heuristics: 10
p0_count: 1
p1_count: 2
timestamp: 2026-08-30T09-06-17Z
slug: resources-views-admin-vouchers-index-blade-php
---
# Critique: admin/vouchers

Method: ⚠️ DEGRADED: single-context (no sub-agent tool exposed)

## Design Health Score

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 2 | Good coupon ticket metaphor, but status badges use font-size antipatterns (`text-[10px]`) |
| 2 | Match System / Real World | 2 | Emojis in filter select (`🎟️`, `🟢`, `⚪`, `🔴`) violate FontAwesome icon system |
| 3 | User Control and Freedom | 2 | Copy code button uses native `alert()`; delete action uses native `confirm()` |
| 4 | Consistency and Standards | 1 | Extensive use of arbitrary font size `text-[10px]` (9 instances) violating DESIGN.md ramp |
| 5 | Error Prevention | 2 | Delete action relies on native confirm popup; toggle active has no confirmation |
| 6 | Recognition Rather Than Recall | 3 | Great Shopee-style coupon cutout ticket design (#VOUCHER-01, progress bar, min purchase) |
| 7 | Flexibility and Efficiency | 2 | Filter bar lacks 1-click Quick Status Filter Pills |
| 8 | Aesthetic and Minimalist Design | 1 | Detector flagged 10 items (`text-[10px]` and `gray-on-color` contrast on hover) |
| 9 | Error Recovery | 2 | Empty state is present, but reset button hover has contrast warnings |
| 10 | Help and Documentation | n/a | Internal admin panel |
| **Total** | | **19/36** | **Poor** |

## Design Specificity Verdict

**LLM assessment**: Outstanding Shopee-style coupon ticket metaphor with discount value badges and quota progress bars, but currently bogged down by 9 instances of `text-[10px]` font size antipatterns, native browser `alert()` and `confirm()` dialogs, and emojis in the filter dropdown.

**Deterministic scan**: **10 findings** from `detect.mjs` (`design-system-font-size` and `gray-on-color`).

## Priority Issues

### [P0] Widespread Font Size Antipatterns (`text-[10px]`) & Detector Findings
- **Why it matters**: 9 instances of `text-[10px]` violate the `DESIGN.md` typography ramp, causing text readability issues on mobile screens and triggering 10 `detect.mjs` errors.
- **Fix**: Convert all `text-[10px]` to standard `text-xs` (12px) with clean tracking.
- **Suggested command**: `/impeccable polish`

### [P1] Native Browser `alert()` for Copy Code & `confirm()` for Deletion
- **Why it matters**: Native browser alerts break the luxury spa brand aesthetic when copying promo codes or deleting vouchers.
- **Fix**: Use Alpine.js for copy feedback toast/tooltip and replace `confirm()` with an Alpine.js modal.
- **Suggested command**: `/impeccable polish`

### [P1] Unicode Emojis in Filter Options & Missing Quick Filter Pills
- **Why it matters**: Emojis (`🎟️`, `🟢`, `⚪`, `🔴`) conflict with FontAwesome icons. Filtering vouchers requires selecting from a dropdown.
- **Fix**: Replace emojis with FontAwesome 6.5.1 icons and add 1-click Quick Status Filter Pills ("Semua", "Aktif", "Nonaktif", "Kadaluarsa").
- **Suggested command**: `/impeccable layout`

### [P2] Tabular Numerals & Price Contrast
- **Why it matters**: Voucher values and quota numbers lack `tabular-nums`, leading to digit shift.
- **Fix**: Add `tabular-nums` to discount percentages, max discount amounts, and quota progress stats.
- **Suggested command**: `/impeccable polish`
