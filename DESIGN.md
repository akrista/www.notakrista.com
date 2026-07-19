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

The system is built on the **Gruvbox** palette: earthy, warm, low-chroma, terminal-native. Gruvbox was a vim color scheme before it was a brand — that origin is the brand. The layout is Gruvbox Dark: full chroma, retro-tech energy, the same bench at 1 AM with a project on the second monitor.

The site refuses four things, named in PRODUCT.md. **No corporate resume energy.** No "Hi, I'm John, passionate about..." copy, no skill grid, no endorsements row. **No SaaS / indie hacker grammar.** No big gradient metrics, no eyebrow kickers on every section, no "ship faster" energy. **No default-Laravel / Bootstrap-era portfolios.** No "Let's get started" placeholder, no two-card split, no logo wall. **No maximalist dev-ornament portfolio.** No animated background grids, no feTurbulence paper grain, no decoration-on-decoration.

**Key Characteristics:**
- **The site is a tool, not a brochure.** The calendar, wishlist, donations, streaks, and relief are real working features, not decorative widgets. The product is its own proof.
- **Direct, warm, capable.** Show the work and the life plainly. The tone never overclaims and never underplays.
- **Flat by default.** Surfaces convey depth with tonal layering, not drop shadows. Lift appears only on state — focus, hover, modal, dropdown.

## 2. Colors

The palette is **Gruvbox** expressed in OKLCH: warm grays, a single warm orange as primary, an aqua-green as accent, and the soft yellow, red, and blue reserved for status and signal.

### Primary

- **Signal Orange** (`oklch(0.700 0.180 50)`): the warm signal. CTAs, the active focus ring, the streak highlight, the donation button. White text on primary fill.

### Accent

- **Moss Aqua** (`oklch(0.760 0.090 145)`): the cool counterpoint to Signal Orange. Used for links, focus-under states, and status indicators.

### Status (sparingly)

- **Cathode Yellow** (`oklch(0.810 0.150 80)`): warnings, the "in progress" streak, the "donation received" toast. Shouts by design.
- **Terminal Red** (`oklch(0.640 0.220 25)`): errors, the "earthquake relief urgent" block, the "this slot is taken" state. Loud by definition.
- **Calm Blue** (`oklch(0.680 0.050 200)`): informational only. The "no public work yet" empty state, the GitHub link.

### Neutrals

- **Workshop Floor** (`oklch(0.270 0.005 65)`): the body bg. A pure-ish warm dark.
- **Bench Surface** (`oklch(0.320 0.005 65)`): cards, panels, sections. One tonal step up from bg.
- **Bench Raised** (`oklch(0.380 0.008 60)`): a third step for the calendar's event blocks, the wishlist rows, and streak cards.
- **Iron Ink** (`oklch(0.910 0.030 88)`): body text. ≥7:1 contrast against bg. Carries a faint green cast (chroma 0.005–0.030) toward the brand hue.
- **Bolt Muted** (`oklch(0.650 0.020 80)`): secondary text, meta lines, the timestamp under a streak. ≥3.5:1 against bg.

### Named Rules

**The Gruvbox Rule.** Use only the gruvbox palette. No off-palette colors. The discipline is the brand.

**The One Orange Rule.** Signal Orange is the only saturated color allowed on a filled button or a brand mark. Other elements that need emphasis use weight, size, or a tonal shift — never a second saturated color.

**The Same-Hue Neutrals Rule.** Neutrals carry chroma 0.005–0.030 toward the brand hue (warm dark), not toward warmth-by-default.

## 3. Typography

**Display Font:** Instrument Sans (humanist, 600 weight, tight tracking) — the same family as body, in heavier weight. One family in multiple weights, no second face.
**Body Font:** Instrument Sans 400, generous line-height (1.6).
**Mono Font:** the system mono stack — ui-monospace → SFMono-Regular → Menlo → Monaco.
**Label Font:** Instrument Sans 600, uppercase, +0.08em letter-spacing. Used for section labels and badge text.

### Hierarchy

- **Display** (600, clamp(2.5rem, 6vw, 4.5rem), 1.05, -0.025em): the hero statement on the landing. Cap at 4.5rem (~72px). Use once per surface. Balance with `text-wrap: balance`.
- **Headline** (600, clamp(1.75rem, 3vw, 2.5rem), 1.15, -0.02em): the work, the projects, the section anchors.
- **Title** (500, 1.25rem, 1.3, -0.01em): card headings, modal titles, the contact form.
- **Body** (400, 1rem, 1.6, 0): paragraphs, descriptions, the "about" copy. Cap line length at 65–75ch. `text-wrap: pretty` for long prose.
- **Label** (600, 0.75rem, 1.2, +0.08em, uppercase): switchers, section labels, badges.
- **Mono** (400, 0.875rem, 1.55, 0): code, dates, times, streak counts, terminal-feel elements.

### Named Rules

**The One-Family Rule.** One sans family in multiple weights. No second display face, no serif. The mono stack is the only other family.

**The Mono Tell Rule.** Anywhere a code-feeling element appears (date, time, version, count, status), the mono stack is mandatory.

## 4. Elevation

The system is **flat by default**. Surfaces convey depth through tonal layering (Workshop Floor → Bench Surface → Bench Raised), not through drop shadows. A card at rest has no shadow; the moment it gains focus, hovers into a state, or rises to a modal, lift appears as a 1- or 2-step tonal shift plus a subtle shadow, then disappears on close.

### Shadow Vocabulary

- **None-at-rest.** Default state. No shadow.
- **focus-ring** (`box-shadow: 0 0 0 2px var(--bg), 0 0 0 4px var(--primary)`): the focus state on any interactive element. The two-ring pattern is the brand's focus signature.
- **modal-lift** (`box-shadow: 0 12px 32px oklch(0 0 0 / 0.45)`): modals, popovers, the calendar's date picker, the donation confirm. Single, soft, long.
- **dropdown-lift** (`box-shadow: 0 6px 20px oklch(0 0 0 / 0.35)`): the user menu, the team picker, or select dropdowns.

### Named Rules

**The Flat-By-Default Rule.** No shadow on a resting surface. No exception.

**The Focus-Ring-First Rule.** A focused interactive element gets a focus-ring, not a shadow.

## 5. Components

The component library is the gruvbox-fluent set: small, deliberate, working-tool pieces.

### Buttons

- **Shape:** gently rounded (`rounded-md` = 8px). Full-pill is reserved for tags and badges; never buttons.
- **Primary:** Signal Orange fill, primary-ink text, uppercase label treatment. Hover: tonal shift or opacity change. Active: subtle 1px inset border. Focus: focus-ring.
- **Ghost:** transparent fill, `ink` text, no border at rest, 1px border in `border` on hover.
- **Destructive:** Terminal Red fill, white text.

### Cards

- **Corner Style:** `rounded-lg` (12px).
- **Background:** Bench Surface, one tonal step up from bg.
- **Shadow:** none-at-rest. focus-ring on interactive cards.
- **Internal Padding:** `spacing.lg` (24px) for standard cards, `spacing.xl` (32px) for hero cards.

### Inputs

- **Style:** 1px border in `border`, transparent fill, `rounded-md`, `padding: 12px 16px`.
- **Focus:** focus-ring (2px bg + 2px primary), no shadow.
- **Placeholder:** Bolt Muted color, 1rem body.
- **Error:** 1px Terminal Red border + a single Cathode Yellow inline message under the field.

### Navigation

- **Top bar:** `height: 64px`, bg = body bg, 1px bottom border in `border`. Logo on the left, navigation in the center, and the contact CTA on the right.
- **Mobile:** the top bar collapses to a logo + a single hamburger that opens a full-screen sheet (not a dropdown) with the nav links and the CTA stacked.

### Chips / Streaks

- **Style:** Bench Raised bg, `rounded-pill`, padding 4px 10px, mono font, 0.75rem.
- **State:** the active streak uses primary fill and dark text. Inactive uses muted text on bench surface.

## 6. Do's and Don'ts

### Do:

- **Do** use Gruvbox. The palette is the brand. No off-palette colors.
- **Do** use the focus-ring for any interactive element. Two rings, `bg → primary`.
- **Do** use the mono stack for anything that reads as a code-feeling element: dates, times, counts, versions, statuses.
- **Do** show the work and the life plainly. The calendar, wishlist, donations, streaks, and relief are real working features.
- **Do** respect `prefers-reduced-motion`.

### Don't:

- **Don't** use a corporate resume or LinkedIn-style profile.
- **Don't** use SaaS / indie hacker grammar.
- **Don't** use a default-Laravel / Bootstrap-era portfolio.
- **Don't** use a maximalist dev-ornament portfolio.
- **Don't** use `border-left` or `border-right` greater than 1px as a colored accent.
- **Don't** use gradient text.
- **Don't** use `border-radius: 32px+` on cards.
- **Don't** use paired `border + box-shadow` on the same element.
- **Don't** include numbered section markers (01 / 02 / 03) above every section.
