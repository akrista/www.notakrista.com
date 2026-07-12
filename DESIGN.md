---
name: www.notakrista.com
description: A working engineer's bench — direct, warm, capable, with a real life.
colors:
  bg: "oklch(0.270 0.005 65)"
  surface: "oklch(0.320 0.005 65)"
  surface-raised: "oklch(0.380 0.008 60)"
  ink: "oklch(0.910 0.030 88)"
  muted: "oklch(0.650 0.020 80)"
  primary: "oklch(0.700 0.180 50)"
  primary-ink: "oklch(0.270 0.005 65)"
  accent: "oklch(0.760 0.090 145)"
  border: "oklch(0.380 0.008 60)"
  bg-business: "oklch(0.955 0.030 95)"
  surface-business: "oklch(0.905 0.030 90)"
  ink-business: "oklch(0.230 0.005 70)"
  muted-business: "oklch(0.530 0.013 65)"
  primary-business: "oklch(0.490 0.180 45)"
  accent-business: "oklch(0.510 0.080 150)"
  border-business: "oklch(0.810 0.040 85)"
typography:
  display:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "clamp(2.5rem, 6vw, 4.5rem)"
    fontWeight: 600
    lineHeight: 1.05
    letterSpacing: "-0.025em"
  headline:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "clamp(1.75rem, 3vw, 2.5rem)"
    fontWeight: 600
    lineHeight: 1.15
    letterSpacing: "-0.02em"
  title:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.25rem"
    fontWeight: 500
    lineHeight: 1.3
    letterSpacing: "-0.01em"
  body:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: 1.6
    letterSpacing: "0"
  label:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 600
    lineHeight: 1.2
    letterSpacing: "0.08em"
    textTransform: "uppercase"
  mono:
    fontFamily: "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.55
rounded:
  xs: "2px"
  sm: "4px"
  md: "8px"
  lg: "12px"
  pill: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
  2xl: "48px"
  3xl: "64px"
  4xl: "96px"
  5xl: "128px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.primary-ink}"
    typography: "{typography.label}"
    rounded: "{rounded.md}"
    padding: "12px 24px"
  button-primary-business:
    backgroundColor: "{colors.primary-business}"
    textColor: "#ffffff"
    typography: "{typography.label}"
    rounded: "{rounded.md}"
    padding: "12px 24px"
  button-ghost:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.label}"
    rounded: "{rounded.md}"
    padding: "12px 24px"
  card:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.ink}"
    rounded: "{rounded.lg}"
    padding: "{spacing.lg}"
  card-business:
    backgroundColor: "{colors.surface-business}"
    textColor: "{colors.ink-business}"
    rounded: "{rounded.lg}"
    padding: "{spacing.lg}"
  input:
    backgroundColor: "{colors.bg}"
    textColor: "{colors.ink}"
    typography: "{typography.body}"
    rounded: "{rounded.md}"
    padding: "12px 16px"
  nav-top:
    backgroundColor: "{colors.bg}"
    textColor: "{colors.ink}"
    typography: "{typography.body}"
    height: "64px"
---

# Design System: www.notakrista.com

## 1. Overview

**Creative North Star: "The Working Bench."** A working engineer's bench, not a brand showroom. Tools laid out within reach, surfaces that age well, a single window letting in light. The site is the bench — measured, deliberate, real. Visitors leave with the sense that a person works here, not that a team performed here.

The system is built on the **Gruvbox** palette: earthy, warm, low-chroma, terminal-native. Gruvbox was a vim color scheme before it was a brand — that origin is the brand. Two views share one identity spine. The **personal view** (default) is Gruvbox Dark: full chroma, retro-tech energy, the same bench at 1 AM with a project on the second monitor. The **business view** is Gruvbox Light: same colors, lower chroma, more breathing room — the bench at 10 AM with the windows open. A single switcher toggles between them, the way a developer toggles a vim colorscheme.

The site refuses four things, named in PRODUCT.md. **No corporate resume energy.** No "Hi, I'm John, passionate about..." copy, no skill grid, no endorsements row. **No SaaS / indie hacker grammar.** No big gradient metrics, no eyebrow kickers on every section, no "ship faster" energy. **No default-Laravel / Bootstrap-era portfolios.** No "Let's get started" placeholder, no two-card split, no logo wall. **No maximalist dev-ornament portfolio.** No animated background grids, no feTurbulence paper grain, no decoration-on-decoration.

**Key Characteristics:**
- **One identity, two modes.** The personal view (Gruvbox Dark) and the business view (Gruvbox Light) are the same system tuned to two registers. No second brand, no "playful vs. serious" split.
- **The site is a tool, not a brochure.** The calendar, wishlist, donations, streaks, and relief are real working features, not decorative widgets. The product is its own proof.
- **Direct, warm, capable.** Show the work and the life plainly. The tone never overclaims and never underplays.
- **Flat by default.** Surfaces convey depth with tonal layering, not drop shadows. Lift appears only on state — focus, hover, modal, dropdown.

## 2. Colors

The palette is **Gruvbox** expressed in OKLCH: warm grays, a single warm orange as primary, an aqua-green as accent, and the soft yellow, red, and blue reserved for status and signal. Every role has two values — one for the personal (dark) view, one for the business (light) view — so the brand color carries the same identity across modes.

### Primary

- **Signal Orange** (`oklch(0.700 0.180 50)` — dark, `oklch(0.490 0.180 45)` — business): the warm signal. CTAs, the brand rail, the active focus ring, the streak highlight, the donation button. Anchors both views; the business view uses the deeper variant (chroma identical, lightness lower) so the same color reads as restrained rather than loud. White text on the dark variant, white text on the business variant (per the Helmholtz-Kohlrausch rule: dark text on saturated mid-luminance fills reads as muddy).

### Accent

- **Moss Aqua** (`oklch(0.760 0.090 145)` — dark, `oklch(0.510 0.080 150)` — business): the cool counterpoint to Signal Orange. Used for links, focus-under states, the "online" / "available" status pill, the active switcher state. Hue near the original brand seed (160°, ±15°), low chroma, deliberately quiet. Different lightness from primary (0.76 vs 0.70 — 95° hue difference is the actual separator), so primary and accent read as a pair, not as two variants of the same color.

### Tertiary (status only)

- **Cathode Yellow** (`oklch(0.810 0.150 80)` — dark, `oklch(0.620 0.140 70)` — business): warnings, the "in progress" streak, the "donation received" toast. Used sparingly; it shouts by design.
- **Terminal Red** (`oklch(0.640 0.220 25)` — dark, `oklch(0.380 0.180 28)` — business): errors, the "earthquake relief urgent" block, the "this slot is taken" state. Loud by definition.
- **Calm Blue** (`oklch(0.680 0.050 200)` — dark, `oklch(0.450 0.090 230)` — business): informational only. The "no public work yet" empty state, the GitHub link. The quietest of the gruvbox set.

### Neutral

- **Workshop Floor** (`oklch(0.270 0.005 65)` — dark, `oklch(0.955 0.030 95)` — business): the body bg. The brand's warmth lives in primary, not the surface. A pure-ish warm dark in the personal view; a warm cream in the business view. The cream is the same warmth as the dark — it's not a "warm because the brand is warm" tint, it's "warm because gruvbox is warm" (chromatic warmth in both).
- **Bench Surface** (`oklch(0.320 0.005 65)` — dark, `oklch(0.905 0.030 90)` — business): cards, panels, sections. One tonal step up from bg.
- **Bench Raised** (`oklch(0.380 0.008 60)` — dark only): a third step for the calendar's event blocks, the wishlist rows, the streak cards. Personal view only — the business view doesn't need the depth.
- **Iron Ink** (`oklch(0.910 0.030 88)` — dark, `oklch(0.230 0.005 70)` — business): body text. ≥7:1 contrast against bg in both views. Carries a faint green cast (chroma 0.005–0.030) toward the brand hue, not toward warmth-by-default.
- **Bolt Muted** (`oklch(0.650 0.020 80)` — dark, `oklch(0.530 0.013 65)` — business): secondary text, meta lines, the timestamp under a streak. ≥3.5:1 against bg.

### Named Rules

**The Gruvbox Rule.** Use only the gruvbox palette. No off-palette colors. No blue tailwind-500 because "we need a blue." No emerald-400 because "the chart needs green." If a status is needed, the gruvbox blue, yellow, or red exists for that. The discipline is the brand.

**The One Orange Rule.** Signal Orange is the only saturated color allowed on a filled button or a brand mark. Other elements that need emphasis use weight, size, or a tonal shift — never a second saturated color. The rarity of orange on the page is what makes it signal.

**The Same-Hue Neutrals Rule.** Neutrals carry chroma 0.005–0.030 toward the brand hue (warm dark, warm cream), not toward warmth-by-default. A pure-neutral surface would feel like a different brand; the chroma is what makes the system gruvbox.

## 3. Typography

**Display Font:** Instrument Sans (humanist, 600 weight, tight tracking) — the same family as body, in heavier weight. One family in multiple weights, no second face.
**Body Font:** Instrument Sans 400, generous line-height (1.6).
**Mono Font:** the system mono stack — ui-monospace → SFMono-Regular → Menlo → Monaco. JetBrains Mono or Berkeley Mono recommended if a custom face is added; the system stack is the floor.
**Label Font:** Instrument Sans 600, uppercase, +0.08em letter-spacing. Used for the switcher ("BUSINESS / PERSONAL"), section labels, and badge text. Tight, deliberate, never decorative.

**Character:** Humanist, deliberate, slightly technical. The pairing is one family doing all the work; weight and tracking separate the roles. Display sits at -0.025em so the letters read as set type, not as a default. The mono stack is the gruvbox tell — the code, the streak count, the calendar slot are all mono, and that detail is what makes the system feel like a working tool.

### Hierarchy

- **Display** (600, clamp(2.5rem, 6vw, 4.5rem), 1.05, -0.025em): the hero statement on the landing. Cap at 4.5rem (~72px). Use once per surface. Balance with `text-wrap: balance`.
- **Headline** (600, clamp(1.75rem, 3vw, 2.5rem), 1.15, -0.02em): the work, the projects, the section anchors.
- **Title** (500, 1.25rem, 1.3, -0.01em): card headings, modal titles, the contact form.
- **Body** (400, 1rem, 1.6, 0): paragraphs, descriptions, the "about" copy. Cap line length at 65–75ch. `text-wrap: pretty` for long prose.
- **Label** (600, 0.75rem, 1.2, +0.08em, uppercase): switchers, section labels, badges, the "01 / 02 / 03" sequence when one is used deliberately (not as AI scaffolding — only when the section IS a sequence).
- **Mono** (400, 0.875rem, 1.55, 0): code, dates, times, streak counts, terminal-feel elements. The "Wakatime: 1423 hrs" line, the calendar slot, the version label.

### Named Rules

**The One-Family Rule.** One sans family in multiple weights. No second display face, no serif. The mono stack is the only other family, used for code, dates, and the gruvbox tell.

**The Mono Tell Rule.** Anywhere a code-feeling element appears (date, time, version, count, status), the mono stack is mandatory. Anywhere a system element reads as "I am a label, not a sentence," the label treatment (uppercase, +0.08em) is mandatory. The two together are what makes the system read as a working tool.

## 4. Elevation

The system is **flat by default**. Surfaces convey depth through tonal layering (Workshop Floor → Bench Surface → Bench Raised), not through drop shadows. A card at rest has no shadow; the moment it gains focus, hovers into a state, or rises to a modal, lift appears as a 1- or 2-step tonal shift plus a subtle shadow, then disappears on close. The personal view gets the full 3-step tonal ladder; the business view stays at 2 steps, never 3, because the business view is restrained by design.

### Shadow Vocabulary

- **None-at-rest.** Default state. No shadow.
- **focus-ring** (`box-shadow: 0 0 0 2px var(--bg), 0 0 0 4px var(--primary)`): the focus state on any interactive element. The two-ring pattern (transparent gap between the surface and the brand color) is the brand's focus signature; it's the only place shadow appears on most surfaces.
- **modal-lift** (`box-shadow: 0 12px 32px oklch(0 0 0 / 0.45)`): modals, popovers, the calendar's date picker, the donation confirm. Single, soft, long. Personal view only; the business view uses the focus-ring only.
- **dropdown-lift** (`box-shadow: 0 6px 20px oklch(0 0 0 / 0.35)`): the switcher menu, the user menu, the team picker. Shorter and softer than modal-lift.

### Named Rules

**The Flat-By-Default Rule.** No shadow on a resting surface. No exception. The system reads as flat, deliberate, and confident. The moment a shadow appears, the user knows the state changed.

**The Focus-Ring-First Rule.** A focused interactive element gets a focus-ring, not a shadow. The two-ring pattern (`bg → primary`) is the brand's interaction language. Hover, active, and disabled all use the same ring, not a different visual treatment.

## 5. Components

The component library is the gruvbox-fluent set: small, deliberate, working-tool pieces. Each component has the same identity in both views; the only difference is the active values (orange on dark, deeper orange on light).

### Buttons

- **Shape:** gently rounded (`rounded-md` = 8px). Full-pill is reserved for tags and badges; never buttons.
- **Primary:** Signal Orange fill, dark text, uppercase label treatment. Hover: tonal shift to the business-orange value. Active: subtle 1px inset border in `bg`. Focus: focus-ring.
- **Ghost:** transparent fill, `ink` text, no border at rest, 1px border in `border` on hover. Used for secondary actions ("View GitHub", "Open calendar").
- **Destructive:** Terminal Red fill (dark variant), white text. Used for "Delete wishlist item", "Cancel donation." No hover-to-orange — red is its own thing.

### Cards

- **Corner Style:** `rounded-lg` (12px). Not 16px. Not 24px. The 12px cap is a codex test on its own.
- **Background:** Bench Surface, one tonal step up from bg. Personal view only — the business view uses bg + border (no card surface lift) to keep the canvas flat and readable.
- **Shadow:** none-at-rest. focus-ring on interactive cards.
- **Border:** 1px in `border` for the business view; none for the personal view (the tonal step is the boundary).
- **Internal Padding:** `spacing.lg` (24px) for standard cards, `spacing.xl` (32px) for hero cards. No `spacing.md` for cards — the bench is generous.

### Inputs

- **Style:** 1px border in `border`, transparent fill (so the bg shows through), `rounded-md`, `padding: 12px 16px`. Mono font is opt-in for fields that take dates, codes, or version strings.
- **Focus:** focus-ring (2px bg + 2px primary), no shadow.
- **Placeholder:** Bolt Muted color, 1rem body, never lighter than 4.5:1 contrast.
- **Error:** 1px Terminal Red border + a single Cathode Yellow inline message under the field. No shake animation. No icon. The red border is the affordance.

### Navigation

- **Top bar:** `height: 64px`, bg = body bg, 1px bottom border in `border` (personal view), or no border (business view). Logo on the left, the BUSINESS / PERSONAL switcher in the center (or right on mobile), the contact CTA on the right.
- **Switcher:** a segmented control, two pills, active pill = Bench Raised bg + ink text + 1px border in primary, inactive = transparent + muted text. Mono font for the labels, uppercase, +0.08em.
- **Mobile:** the top bar collapses to a logo + a single hamburger that opens a full-screen sheet (not a dropdown) with the nav links, the switcher, and the CTA stacked. The sheet slides from the right with a 240ms ease-out.

### Chips / Streaks

- **Style:** Bench Raised bg (personal) or border + transparent (business), `rounded-pill`, padding 4px 10px, mono font, 0.75rem. The streak count, the donation tier, the Wakatime rank.
- **State:** the active streak (e.g. "1423 hrs this year") uses the primary fill and dark text. The inactive ("113 days") uses the muted text on the bench surface.

### Brand Mark

- **Style:** a single-character or two-character mark using Instrument Sans 700 in primary, with the "nk." in mono 0.875rem below or beside. The mark is the work — it is the bench, not a logo. (Implementation deferred to a separate `craft` task; DESIGN.md only sets the rules.)

## 6. Do's and Don'ts

### Do:

- **Do** use Gruvbox. The palette is the brand. No off-palette colors. No tailwind blue-500 because "we need a blue."
- **Do** keep the personal view (dark) as the default. The brand lives there; the business view is a tonal shift, not a separate brand.
- **Do** use the focus-ring for any interactive element. Two rings, `bg → primary`. No single-ring focus. No outline-offset 0.
- **Do** use the mono stack for anything that reads as a code-feeling element: dates, times, counts, versions, statuses, the switcher labels.
- **Do** show the work and the life plainly. The calendar, wishlist, donations, streaks, and relief are real working features. The product is its own proof.
- **Do** respect `prefers-reduced-motion`. Every animation gets a crossfade or instant transition on reduced motion. No exception.
- **Do** treat the switcher as a feature for everyone, not a flourish. Recruiters on the personal view are on the wrong surface; the switcher is an accessibility consideration, not a brand one.

### Don't:

- **Don't** use a corporate resume or LinkedIn-style profile. No "Hi, I'm John, passionate about..." copy, no stock photo, no Skills grid.
- **Don't** use SaaS / indie hacker grammar. No big gradient metrics, no eyebrow kickers above every section, no "ship faster" energy, no testimonials marquee.
- **Don't** use a default-Laravel / Bootstrap-era portfolio. No "Let's get started" placeholder, no two-card split, no logo wall.
- **Don't** use a maximalist dev-ornament portfolio. No animated background grids, no feTurbulence paper grain, no decoration-on-decoration.
- **Don't** use `border-left` or `border-right` greater than 1px as a colored accent on cards, list items, or callouts. The brand rail is a different element; the rest is full borders or nothing.
- **Don't** use gradient text. `background-clip: text` is decorative, never meaningful. Use a single solid color and let weight or size do the emphasis.
- **Don't** use `border-radius: 32px+` on cards. 12px cap. Pills only for tags and badges.
- **Don't** use paired `border + box-shadow` on the same element. Pick one. The brand uses tonal layering, not borders + shadows.
- **Don't** gate content visibility on a class-triggered transition. Reveals must enhance an already-visible default; if the class doesn't fire, the section is still readable.
- **Don't** include numbered section markers (01 / 02 / 03) above every section. Use them only when the section actually IS a sequence and the order carries information.
- **Don't** use a saturated AI attractor. No cream-bg + dusty-brown primary. No forest-green-on-cream. No AI-purple-on-white. The gruvbox palette is the answer; deviations are not.
