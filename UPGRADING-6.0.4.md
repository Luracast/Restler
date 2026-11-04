# Restler 6.0.4: security rationale and upgrade guidance

This patch retains the declared PHP requirement `^8`. It does **not** impose reactive-restler 7.0.0's PHP 8.5.12 minimum: that is a separate repository and breaking release. Use a supported, patched PHP runtime; a permissive package constraint is not a recommendation to deploy end-of-life PHP.

## Why these changes?

- **React HTTP 1.11.0 → 1.11.1:** a malformed chunked body can trap the decoder in a CPU-consuming loop, while an HTTP client receiving an unterminated response header section can buffer without a limit and exhaust memory. The fix matters when these client/server paths receive attacker-influenced data. See the upstream [chunk decoder advisory](https://github.com/reactphp/http/security/advisories/GHSA-x424-64qh-5j54) and [response-header advisory](https://github.com/reactphp/http/security/advisories/GHSA-g4f2-2pf3-2pwj). Bounded child-process tests cover malformed chunks, legitimate input and multiple oversized-header forms.
- **Symfony YAML 7.4.15 → 7.4.18:** deeply nested inline collections could recurse in the lexer before parser depth checks applied, exhausting the stack. The patched lexer participates in the nesting limit and rejects excessive input. Exposure requires parsing attacker-influenced YAML; see the [upstream advisory](https://github.com/symfony/symfony/security/advisories/GHSA-2v3c-qxhr-7vpv). Tests cover sequence/mapping/mixed nesting, normal parsing and recovery after rejection. The locked YAML 7.4 branch already required PHP 8.2; this patch does not introduce that integration requirement.
- **JWT 7 API compatibility and authentication state:** the dependency was already JWT 7 before this patch, but the decoder still called its old API. The updated adapter constructs `Key(publicKey, 'RS256')`, binding the verification algorithm to the configured RSA key. It clears previous token state before another authentication attempt and only publishes the token after signature, claims and user identification succeed. Tests cover valid tokens, invalid claims/signatures, algorithm confusion and consecutive success/failure attempts. This is an integration repair, not a claim that all JWT installations were compromised.

## Installing the patched integrations

React HTTP and JWT are optional integrations represented in this library's `require-dev`; YAML is present in its development lockfile. Composer does not install a library's development dependencies into a consuming application. Updating Restler alone therefore does not prove that an application's own lockfile has updated these packages.

For this repository checkout, install its committed lockfile with `composer install`. For a consuming application, inspect its own `composer show`, `composer.lock` and `composer audit --locked`, select compatible patched integration versions, and run a targeted Composer update there. Do not replace an application's lockfile with this repository's lockfile or assume a global update is necessary.

If the application enables JWT authentication, use Firebase JWT 7 with the updated adapter and verify its key/issuer/audience/subject configuration. If the application embeds Explorer v5, that is a separate release line: see the [v5 release notes](https://github.com/Luracast/Restler/blob/v5/RELEASE_NOTES.md). After updating, run application tests in staging, including enabled YAML, authentication and async HTTP paths. Verify actual platform requirements with `composer check-platform-reqs` on the deployment runtime; this release does not upgrade PHP or deploy code for you.

## Verification and limits

The 6.0.4 candidate passed 311 Behat scenarios / 1,651 steps on the React server, plus the focused JWT, HTTP and YAML checks. Composer audit found no known lockfile advisories when checked. An audit result is tied to the inspected lockfile and advisory data; it is not a complete application security assessment or proof of deployed protection.
