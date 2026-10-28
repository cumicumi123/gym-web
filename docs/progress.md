# GymSense Progress

## Stage 1 — Nutrition
- [x] Mifflin-St Jeor BMR with gender offsets.
- [x] Activity factors from training frequency.
- [x] Cutting/bulking/strength targets with safe ranges.
- [x] Protein, fat, carbs, fiber, and water estimates.
- [x] Safety rules for minors, underweight users, and calorie floors.
- [x] Nutrition section on the recommendation and program pages.

## Stage 2 — Saved Programs and Auth
- [x] Custom login/register/logout flow.
- [x] Programs table, model, factory, policy.
- [x] Save recommendation as a program with consent.
- [x] Active program page with nutrition and schedule notes.
- [x] Account export and delete controls.

## Stage 3 — 4-Week Schedule
- [x] Seven-day, four-week schedule generator.
- [x] Split selection by frequency and requested split.
- [x] Rest/cardio placement and consecutive-day warnings.
- [x] Week 4 deload at roughly 60% volume.
- [x] Schedule page with mark-complete toggle.

## Stage 4 — Weekly Check-ins
- [x] Weekly check-in create/update.
- [x] Weight, waist, energy, and notes.
- [x] Program and session progress summary.
- [x] Progress page with chart data and accessibility summaries.
- [x] Empty states for missing program or fewer than two check-ins.

## Stage 5 — Charts and Privacy
- [x] Chart.js via npm with weight and combination charts.
- [x] SVG session ring, macro donut, progress bars.
- [x] 12-week heatmap and 4-week timeline.
- [x] Reduced-motion support and accessible labels.
- [x] Privacy page and account data controls.
- [x] Privacy copy updated on form, home, and about pages.

## Verification
- [x] Migrations run.
- [x] `php artisan test --compact` passes: 50 tests, 252 assertions.
- [x] `npm run build` passes.
- [x] Pint applied to changed PHP files.

## Notes
- Auth is custom/minimal; no starter kit.
- Chart.js is the only new frontend dependency.
- Data is only stored when a logged-in user explicitly saves a program and consents.
