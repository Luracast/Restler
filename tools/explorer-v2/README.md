# Explorer v2 template build and tests

This builds only the precompiled Handlebars templates in the legacy Swagger UI 2.1.4 bundle. It preserves surrounding application code and ships the Handlebars **4.7.10 runtime only**. Do not disable prototype-access or compiler-version checks to load old templates.

Run from this directory (Node.js 20.19+ or a newer compatible release):

```sh
npm ci --ignore-scripts
npm run build
npm test
npm audit
```

`build.cjs` uses a JavaScript parser to identify all 15 template registrations. It replaces their complete syntax nodes, copies the pinned runtime, and updates `index.html`. The source templates are from Swagger UI tag `v2.1.4`, commit `5e5e33ace1214e453e2484b9e08220a3236c46a5`, `src/main/template/`. All 15 original compiled template nodes matched that upstream tag exactly before migration. Their Apache-2.0 license is included as `LICENSE.swagger-ui`.

`expected-html.json` records 36 output cases from the original Handlebars 2.0.0 bundle: all templates plus body/file/array/default-content-type variants. `test.cjs --record` refuses to record against the upgraded runtime, preventing accidental replacement of the compatibility baseline. Tests additionally verify escaping, prototype-property/method denial, compiler revision enforcement, and Swagger UI DOM rendering/controls/expansion from an in-memory Swagger document. Test tooling is development-only; no npm packages are required by the deployed Explorer.

This tool builds only Explorer v2. Explorer v1 and the older request-data fixture are migrated by the sibling `tools/explorer-legacy` tools. Existing application `SafeString` helpers and intentionally raw documentation fields are preserved; this dependency update does not claim to sanitize arbitrary Swagger descriptions.

JSDOM does not calculate layout, so the collapse assertion calls the production collapse function directly; a second toggle click depends on jQuery `:visible` and needs a real browser. These tests do not establish visual layout or supported legacy-browser behavior.
