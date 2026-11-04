# Restler 5.0.17 — 2026-11-04

Maintenance release following 5.0.16.

Explorer v2, Explorer v1 and the older public request-data Explorer fixture now use the Handlebars 4.7.10 runtime. Templates are rebuilt from retained source; the full old compilers and obsolete runtimes are removed. Both regular and minified legacy bundles are covered. Existing template escaping, custom markup and surrounding application bytes are preserved; prototype access and compiler revision checks remain enabled.

Validation completed during preparation:

- Explorer v2: 36 output comparisons plus DOM/security checks.
- Legacy explorers: 420 comparisons across v1/fixture and regular/minified bundles, plus escaping and runtime-only checks.
- All four complete legacy page variants passed browser smoke tests with no console errors.
- Pinned build tooling audit reported no advisories.

Reproduce with the commands in `tools/explorer-v2/README.md` and `tools/explorer-legacy/README.md`. This candidate keeps the existing Composer PHP requirement (`>=7.1`). It updates bundled browser assets; it neither upgrades a deployed PHP runtime nor audits every legacy Swagger dependency or raw documentation field.

Released from the `v5` source branch of Luracast/Restler. Do not release the old `master` snapshot or patch the separate Restler-Framework split directly. Consumers of `restler/framework` need a separately reviewed split synchronization; this source tag alone does not deliver its patched assets to that repository.
