# Restler 5.0.17 — 2026-11-04

Maintenance release following 5.0.16.

Explorer v2, Explorer v1 and the older public request-data Explorer fixture now use the Handlebars 4.7.10 runtime. Templates are rebuilt from retained source; the full old compilers and obsolete runtimes are removed. Both regular and minified legacy bundles are covered. Existing template escaping, custom markup and surrounding application bytes are preserved; prototype access and compiler revision checks remain enabled.

## Why update the browser dependency?

The bundled Handlebars versions predated fixes for prototype-chain access and template/compiler injection paths. For example, the [partial lookup advisory](https://github.com/handlebars-lang/handlebars.js/security/advisories/GHSA-2qvq-rjwj-gvw9) describes rendering an inherited polluted partial, and the [own-property check advisory](https://github.com/handlebars-lang/handlebars.js/security/advisories/GHSA-p8wg-vrv2-v86f) describes a compiler injection path fixed in 4.7.10. These are different trust boundaries; this update does not establish that this Explorer's fixed templates exposed every listed attack.

The release uses the patched **runtime only** in the browser because Explorer renders precompiled, repository-owned templates. Templates were rebuilt with the matching patched compiler to avoid a runtime/compiler revision mismatch. Prototype-access restrictions stay enabled. Compiler tooling is development-only, reducing the code shipped to browsers.

## Upgrade and compatibility

Deploy the updated HTML entry points, runtime files and generated Swagger UI bundles together. Invalidate cached/CDN copies so an old entry point cannot request a removed runtime. Custom templates must be recompiled with the matching Handlebars version; do not pair old compiled templates with the new runtime or re-enable inherited-property access to work around errors. Test customized Swagger descriptions/helpers and older browsers separately.

The PHP requirement remains `>=7.1` for this patch; this is not a recommendation to use unsupported PHP. Use a supported, patched runtime. This browser dependency update does not imply every PHP site was vulnerable, and it does not perform a runtime upgrade.

Validation completed during preparation:

- Explorer v2: 36 output comparisons plus DOM/security checks.
- Legacy explorers: 420 comparisons across v1/fixture and regular/minified bundles, plus escaping and runtime-only checks.
- All four complete legacy page variants passed browser smoke tests with no console errors.
- Pinned build tooling audit reported no advisories.

Reproduce with the commands in [Explorer v2 build guide](tools/explorer-v2/README.md) and [legacy Explorer build guide](tools/explorer-legacy/README.md). This release keeps the existing Composer PHP requirement (`>=7.1`). It updates bundled browser assets; it neither upgrades a deployed PHP runtime nor audits every legacy Swagger dependency or raw documentation field.

Released from the `v5` source branch of Luracast/Restler. Do not release the old `master` snapshot or patch the separate Restler-Framework split directly. Consumers of `restler/framework` need a separately reviewed split synchronization; this source tag alone does not deliver its patched assets to that repository.
