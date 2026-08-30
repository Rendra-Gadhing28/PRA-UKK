---
target: admin/dashboard page
total_score: 28
max_score: 36
na_heuristics: 10
p0_count: 0
p1_count: 2
timestamp: 2026-08-30T09-09-16Z
slug: resources-views-admin-dashboard-blade-php
---
# Critique: admin/dashboard

Method: ⚠️ DEGRADED: single-context (no sub-agent tool exposed)

## Design Health Score

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 3 | Great TradingView burgundy line chart, growth indicators (+X% vs last month) |
| 2 | Match System / Real World | 3 | Executive metrics, treatment breakdown doughnut chart, recent bookings table |
| 3 | User Control and Freedom | 2 | **Route Bug**: "Lihat Semua →" links to `user.bookings.index` instead of `admin.bookings.index` |
| 4 | Consistency and Standards | 2 | Insight cards use raw inline SVGs instead of FontAwesome 6.5.1 icons |
| 5 | Error Prevention | 3 | Real-time analytics badge, verified export buttons (PDF & Excel) |
| 6 | Recognition Rather Than Recall | 3 | Center total overlay on doughnut chart, treatment rank badges (#1, #2, #3) |
| 7 | Flexibility and Efficiency | 3 | PDF & Excel (CSV) 1-click export actions in header |
| 8 | Aesthetic and Minimalist Design | 3 | High-contrast TradingView chart with glassmorphism tooltip and soft blush canvas |
| 9 | Error Recovery | 3 | Clean empty states for recent bookings and top treatments |
| 10 | Help and Documentation | n/a | Internal admin panel |
| **Total** | | **28/36** | **Good** |

## Design Specificity Verdict

**LLM assessment**: Highly polished executive dashboard featuring a custom TradingView burgundy line chart with hairline crosshairs, treatment doughnut chart, and growth metrics. Needs fixing of a routing bug in Recent Bookings ("Lihat Semua →" points to user route instead of `admin.bookings.index`), standardizing SVGs to FontAwesome 6.5.1 icons, and adding `tabular-nums` for financial values.

**Deterministic scan**: **0 findings** from `detect.mjs`.

## Priority Issues

### [P1] Broken Admin Route Link in "Recent Bookings" Header
- **Why it matters**: Clicking "Lihat Semua →" in the Recent Bookings card redirects the admin to `user.bookings.index` (customer portal) instead of `admin.bookings.index`.
- **Fix**: Update link target to `route('admin.bookings.index')`.
- **Suggested command**: `/impeccable polish`

### [P1] Standardize Raw Inline SVGs to FontAwesome 6.5.1 Icons
- **Why it matters**: Insight cards use raw inline SVG elements (`<svg class="w-5 h-5">`), violating the project's FontAwesome iconography rule.
- **Fix**: Replace SVGs with FontAwesome 6.5.1 icons (`fa-solid fa-money-bill-trend-up`, `fa-solid fa-wallet`, `fa-solid fa-circle-check`, `fa-solid fa-chart-line`).
- **Suggested command**: `/impeccable polish`

### [P2] Tabular Numerals for Executive Financial Metrics
- **Why it matters**: Income, expense, and net profit cards lack `tabular-nums`, which can cause minor font jitter when numbers update dynamically.
- **Fix**: Add `tabular-nums` class to all currency and metric values.
- **Suggested command**: `/impeccable polish`
