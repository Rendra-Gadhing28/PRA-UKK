---
target: admin/finances page
total_score: 24
max_score: 36
na_heuristics: 10
p0_count: 0
p1_count: 2
timestamp: 2026-08-30T09-03-36Z
slug: resources-views-admin-finances-index-blade-php
---
# Critique: admin/finances

Method: ⚠️ DEGRADED: single-context (no sub-agent tool exposed)

## Design Health Score

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 3 | Good summary cards (Pengeluaran Bulan Ini, Total Transaksi, Struk Ter-scan, Pemasukan Booking) |
| 2 | Match System / Real World | 3 | Financial metrics, scanned receipt badges, item breakdown popovers are clear |
| 3 | User Control and Freedom | 2 | Delete action uses native browser confirm() alert; no quick date preset pills |
| 4 | Consistency and Standards | 2 | FontAwesome 5 legacy prefix (`far fa-*`) mixed with FA 6; oversized filter inputs (`py-4`) |
| 5 | Error Prevention | 2 | Single-click delete form relies on native confirm alert without Alpine modal guard |
| 6 | Recognition Rather Than Recall | 3 | Scanned receipt preview modal, OCR badge, item quantity breakdown |
| 7 | Flexibility and Efficiency | 2 | Filter bar lacks quick category/date pills (e.g. "Bulan Ini", "Hari Ini", "Produk Salon") |
| 8 | Aesthetic and Minimalist Design | 2 | Filter inputs are oversized (`py-4`); missing `tabular-nums` on financial amounts |
| 9 | Error Recovery | 2 | Empty state is generic; filter reset button works |
| 10 | Help and Documentation | n/a | Internal admin panel |
| **Total** | | **24/36** | **Good** |

## Design Specificity Verdict

**LLM assessment**: Solid financial tracking dashboard with OCR receipt scan integration, but filter inputs are oversized (`py-4`), legacy FontAwesome prefixes are mixed, quick category pills are missing, and deletion relies on native browser alerts.

**Deterministic scan**: **0 findings** from `detect.mjs`.

## Priority Issues

### [P1] Missing Quick Filter Pills for Financial Categories & Date Presets
- **Why it matters**: Admins have to open dropdown select and click "Filter" to view expenses by category or timeframe.
- **Fix**: Add 1-click Quick Filter Pills ("Semua", "Bulan Ini", "Peralatan Salon", "Operasional", "Gaji").
- **Suggested command**: `/impeccable layout`

### [P1] Oversized Filter Inputs & Legacy FontAwesome Prefixes
- **Why it matters**: `py-4` padding on filter select and date inputs makes the filter card unnecessarily tall; legacy `far fa-*` icons break icon system consistency.
- **Fix**: Standardize filter inputs to `py-2.5 px-3 rounded-xl` and update icons to FontAwesome 6.5.1 (`fa-regular fa-calendar`, `fa-solid fa-*`).
- **Suggested command**: `/impeccable polish`

### [P2] Deletion Action Polish with Alpine.js Confirmation Modal
- **Why it matters**: Native browser `confirm()` popup breaks the luxury UI experience.
- **Fix**: Replace native confirm with Alpine.js confirmation modal and use a styled pill button with `fa-trash-can`.
- **Suggested command**: `/impeccable polish`

### [P2] Tabular Numerals for Financial Data
- **Why it matters**: Financial totals without `tabular-nums` cause digit misalignment when scanning vertically.
- **Fix**: Add `tabular-nums` to all monetary values in summary cards and table rows.
- **Suggested command**: `/impeccable polish`
