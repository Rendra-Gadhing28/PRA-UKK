---
target: admin/treatments page
total_score: 15
max_score: 36
na_heuristics: 10
p0_count: 1
p1_count: 3
timestamp: 2026-08-30T08-04-07Z
slug: resources-views-admin-treatments-index-blade-php
---
# Critique: admin/treatments

Method: ⚠️ DEGRADED: single-context (no sub-agent tool exposed)

## Design Health Score

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 2 | No active filter indicator beyond pill highlight; no loading state |
| 2 | Match System / Real World | 2 | Emojis mixed with FontAwesome; "Hapus" label on toggle-active |
| 3 | User Control and Freedom | 2 | No confirmation on toggle; no undo |
| 4 | Consistency and Standards | 1 | Emoji + FA mixed; uniform gap-4; broken HTML |
| 5 | Error Prevention | 1 | Toggle fires no confirmation; misleading label |
| 6 | Recognition Rather Than Recall | 3 | Good pills and table labels |
| 7 | Flexibility and Efficiency | 1 | No sort, no bulk, no keyboard |
| 8 | Aesthetic and Minimalist Design | 2 | Oversized filter; flat hierarchy |
| 9 | Error Recovery | 1 | Bare empty state |
| 10 | Help and Documentation | n/a | Admin internal |
| **Total** | | **15/36** | **Poor** |

## Design Specificity Verdict

Category-interchangeable generic CRUD table. No Yalia identity beyond rose colors.

## Priority Issues

### [P0] "Hapus" button routes to toggle-active — catastrophic label mismatch
### [P1] Broken HTML: </div> closing a <td> at line 181
### [P1] Unicode emojis violate FontAwesome-only system
### [P1] Missing Aksi column — 8 headers but 7 data cells
### [P2] Uniform gap-4 everywhere destroys visual hierarchy

## Persona Red Flags

**Alex**: No sort, no bulk, no keyboard.
**Sam**: No aria-labels on toggle, emoji without aria-hidden, broken nesting.

## Minor Observations

- Empty state has no CTA
- Filter label mb-4 too generous
- py-4 on inputs oversized for admin
- Pagination border inconsistency
