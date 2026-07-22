## Design Context

This project's strategic and visual context lives at the project root and in `.impeccable/`. **Read these files before making UI decisions**: they encode the brand, the visual system, and the in-browser iteration config.

- **`PRODUCT.md`**: register, platform, users, purpose, positioning, conversion & proof, brand personality, anti-references, design principles, accessibility floor. Answers "who/what/why."
- **`DESIGN.md`**: visual system: palette, typography, elevation, components, do's and don'ts. Answers "how it looks." On visual decisions, DESIGN.md wins over PRODUCT.md; on strategic/voice decisions, PRODUCT.md wins.
- **`.impeccable/design.json`**: sidecar with tonal ramps, motion tokens, breakpoints, self-contained component HTML/CSS, and narrative. Used by `$impeccable live` and Stitch-compatible tooling.
- **`.impeccable/live/config.json`**: pre-configured for `$impeccable live` to boot straight into variant mode. Files: `resources/views/welcome.blade.php`, `resources/views/dashboard.blade.php`.

**Quick rules from the system** (full text in PRODUCT.md / DESIGN.md):

- **Register:** brand (personal site/marketing). **Platform:** web.
- **Positioning:** an engineer who ships in public, with a real life. **Personality:** direct, warm, capable.
- **North Star (visual):** The Working Bench. **Palette:** Gruvbox, in OKLCH. Personal view (dark) is the default; business view (light) is a tonal shift.
- **Two anti-references that bind:** no corporate resume / LinkedIn energy; no SaaS / indie hacker grammar. No default-Laravel / Bootstrap-era portfolio. No maximalist dev-ornament.
- **Accessibility floor:** WCAG 2.2 AA + `prefers-reduced-motion`. **Primary CTA:** get in touch.
