# Legacy Explorer template build and tests

These tools migrate Explorer v1 (Swagger 1.2) and the public request-data Explorer fixture (Swagger 1.1) to the Handlebars **4.7.10 runtime only**. Both regular and minified bundles are rebuilt. Surrounding application bytes are preserved exactly; only the Handlebars template call expressions change. No prototype-access or compiler-version checks are disabled.

Install the shared pinned development tools, then run from the repository root:

```sh
npm ci --ignore-scripts --prefix tools/explorer-v2
node tools/explorer-legacy/build.cjs
node tools/explorer-legacy/test.cjs
npm test --prefix tools/explorer-v2
npm audit --prefix tools/explorer-v2
```

The build uses Acorn syntax spans and Handlebars `ignoreStandalone: true` to preserve older whitespace behavior. Terser minifies only each replacement expression with compression/mangling disabled. It does not rewrite the surrounding application. Running the build again produces identical bytes. New runtimes are copied unchanged from the pinned package. The old libraries, including the unreferenced fixture beta-6 runtime, are removed after reference inspection.

## Sources and reconciliation

- `v1/templates`: Swagger UI tag `v2.0.17`, commit `4b535adcb70b4359d577143106f1dd5a95fadf8e`, `src/main/template/`. The local custom `main` source was reconstructed from the existing compiled function to preserve the footer navigation, icons and Powered by text. `resource` retains the local placement of the description expression inside its section, including its existing context behavior. All other source templates remain upstream. License notice retained.
- `fixture/templates`: Luracast/Restler-API-Explorer commit `f62dd4fd45861003b88f98d5d3423244e8050c61`, `src/main/template/`. `main` retains the existing `swagger.wordnik.com` link and lack of a final newline rather than silently taking the newer upstream link. License notice copied from upstream README.

All reconstructed sources were rendered against both original bundle variants before replacement: 130 cases per v1 bundle and 80 per fixture bundle matched exactly. `expected-html.json` stores those original outputs, shared by each page's two bundles. `test.cjs --record` refuses to record against the new runtime filename.

The 420 compatibility checks cover every template and optional/required parameters, defaults, body/file/array/read-only fields, OAuth sections, minimal metadata, model/status/content-type fields and customized footers. Additional checks reject inherited methods/properties, verify escaping, retain compiler revision checks and ensure no compiler is shipped. Source consumers are actual compiled template expressions extracted from the production bundles.

Browser smoke tests also loaded each complete page and both bundle variants with local Swagger 1.2/1.1 documents in the available in-app browser. Resource/operation expansion, model/status output, required text inputs, enumeration controls, regular-bundle collapse and custom footer links worked; console error lists were empty. No API operation was submitted. Supported older browsers were not exhaustively tested.

Existing intentionally raw documentation fields and older Swagger/UI libraries remain unchanged. This update fixes the Handlebars dependency boundary; it is not a full audit or redesign of the legacy explorers.
