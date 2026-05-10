---
name: notakrista.com
description: Personal brand and portfolio — a living Laravel starter kit demonstration.
colors:
  monokai-orange: "oklch(74% 0.175 65)"
  monokai-green: "oklch(80% 0.155 135)"
  monokai-pink: "oklch(62% 0.21 8)"
  warm-page: "oklch(99% 0.005 65)"
  warm-charcoal: "oklch(18% 0.01 65)"
  warm-ink: "oklch(17% 0.01 65)"
  warm-steel: "oklch(45% 0.014 65)"
  warm-slate: "oklch(60% 0.01 65)"
  warm-chalk: "oklch(95% 0.005 65)"
  warm-smoke: "oklch(75% 0.008 65)"
  warm-mist: "oklch(55% 0.008 65)"
  warm-border: "oklch(90% 0.008 65)"
  warm-deep: "oklch(30% 0.014 65)"
  warm-subtle: "oklch(92% 0.006 65)"
  warm-subtle-dark: "oklch(24% 0.012 65)"
  print-ink: "oklch(15% 0.008 65)"
  print-gray: "oklch(45% 0.008 65)"
  print-line: "oklch(85% 0.005 65)"
  print-ground: "oklch(97% 0.003 65)"
typography:
  display:
    fontFamily: "'Bricolage Grotesque', ui-sans-serif, system-ui, sans-serif"
    fontSize: "clamp(2.5rem, 10vw + 0.5rem, 9rem)"
    fontWeight: 800
    lineHeight: 0.85
    letterSpacing: "-0.04em"
  headline:
    fontFamily: "'Bricolage Grotesque', ui-sans-serif, system-ui, sans-serif"
    fontSize: "clamp(2.5rem, 8vw + 0.5rem, 6rem)"
    fontWeight: 800
    lineHeight: 0.85
    letterSpacing: "-0.04em"
  title:
    fontFamily: "'Bricolage Grotesque', ui-sans-serif, system-ui, sans-serif"
    fontSize: "clamp(1.4rem, 3vw + 0.3rem, 2.5rem)"
    fontWeight: 700
    lineHeight: 0.9
    letterSpacing: "-0.025em"
  body:
    fontFamily: "'Albert Sans', ui-sans-serif, system-ui, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: 1.6
  label:
    fontFamily: "'Albert Sans', ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 600
    letterSpacing: "0.05em"
    textTransform: "uppercase"
rounded:
  sm: "8px"
  md: "12px"
  xl: "16px"
  full: "9999px"
spacing:
  gap-sm: "8px"
  gap-md: "16px"
  gap-lg: "24px"
  gap-xl: "32px"
  gap-2xl: "48px"
  section-md: "clamp(3rem, 8vw, 6rem)"
  section-lg: "clamp(5rem, 12vw, 10rem)"
components:
  button-primary:
    backgroundColor: "{colors.monokai-orange}"
    textColor: "{colors.warm-ink}"
    rounded: "{rounded.sm}"
    padding: "12px 24px"
  button-primary-hover:
    backgroundColor: "{colors.monokai-orange}"
  button-outline:
    textColor: "{colors.warm-steel}"
    rounded: "{rounded.sm}"
    padding: "12px 24px"
  button-ghost:
    textColor: "{colors.warm-steel}"
    rounded: "{rounded.sm}"
    padding: "12px 24px"
  chip-language:
    backgroundColor: "oklch(80% 0.155 135 / 18%)"
    textColor: "{colors.monokai-green}"
    rounded: "{rounded.full}"
  chip-status:
    backgroundColor: "{colors.warm-subtle}"
    textColor: "{colors.warm-steel}"
    rounded: "{rounded.full}"
  input-select:
    rounded: "{rounded.sm}"
    padding: "10px 12px"
  card-callout:
    backgroundColor: "{colors.monokai-orange}"
    textColor: "{colors.warm-ink}"
    rounded: "{rounded.xl}"
---

# Design System: notakrista.com

## 1. Overview

**Creative North Star: "The Signature Block"**

The definitive, personal mark of authorship — like a craftsman's stamp or a developer's commit signature. This system is the visible proof that says "I built this, and the construction is the evidence." It is not a template wearing someone's name. It is the output of deliberate choices, each element existing because it does work.

The atmosphere is warm and focused — a desk lit by a single task lamp in an otherwise dark room, where the only thing that glows is the work. The palette is derived from Monokai: a committed orange accent (#FD971F) that is confident without being loud, warm without being soft. The neutrals are tinted at hue 65 so they share the orange axis — a cohesive warm family rather than competing temperatures. Density is purposeful: generous whitespace gives the eye clear anchor points, and nothing competes for attention.

What this system explicitly rejects: SaaS landing-page clichés (hero metrics, gradient CTAs, feature icon grids), developer portfolio templates (centered headshots, skill bars, identical project cards), and the reflexive AI aesthetic (gradient text, glassmorphism, side-stripe borders). It is not loud, not trendy, not trying to impress through excess. The craft impresses because the craft is visible.

**Key Characteristics:**
- Light-first, dark mode via user toggle
- Massive display typography as the primary visual element — no decorative imagery needed
- Asymmetric split layout (hero text counterweighted by a solid-color callout card)
- Flat by default — tonal layering conveys hierarchy, not shadows
- One saturated accent (Monokai Orange) applied with intent, not restraint
- Every interactive element feels precisely engineered: crisp transitions, deliberate active states, no fluff

## 2. Colors: The Monokai Palette

A committed palette anchored by Monokai Orange — drawn directly from the Monokai editor theme's signature orange (#FD971F): a color that millions of developers see every day. The neutrals are tinted warm at hue 65 so they harmonize with the orange accent, creating a unified atmosphere that feels cohesive without being monochromatic. This palette is not "inspired by" Monokai — it is Monokai, adapted for the web.

### Primary
- **Monokai Orange** (oklch(74% 0.175 65)): The signature accent, derived from Monokai's #FD971F. Used as the dominant surface color on the home page callout card (100% fill), as link underlines and hover states, as focus rings, and as the primary button fill. It is never timid — when it appears, it owns the surface.

### Secondary
- **Monokai Green** (oklch(80% 0.155 135)): Derived from Monokai's string color (#A6E22E). Reserved exclusively for code and technology indicators: language chips, code-related metadata, tech UI signals. It marks "this is code" — never a brand color, never a surface fill larger than a chip.
- **Monokai Pink** (oklch(62% 0.21 8)): Derived from Monokai's keyword color (#F92672). A reserved accent deployed with extreme rarity — never as a surface fill, never in navigation, never in body text. Its only permitted use is for high-emphasis one-off moments where orange would be too quiet. If you use it more than once per page, you have used it too much.

### Neutral

*Light mode:*
- **Warm Page** (oklch(99% 0.005 65)): Page background. Not stark white — the 0.005 chroma at 65° makes it feel like high-quality paper under warm light.
- **Warm Ink** (oklch(17% 0.01 65)): Primary text. Dark enough for AAA contrast against the warm page background.
- **Warm Steel** (oklch(45% 0.014 65)): Secondary text and metadata. Subdued but readable.
- **Warm Slate** (oklch(60% 0.01 65)): Muted text for timestamps, secondary metadata, disabled states.
- **Warm Border** (oklch(90% 0.008 65)): Structural 1px borders. Present but quiet.
- **Warm Subtle** (oklch(92% 0.006 65)): Subtle backgrounds — tab toggles, chip statuses, skeleton placeholders, hover surfaces.

*Dark mode:*
- **Warm Charcoal** (oklch(18% 0.01 65)): Page background. Deep without being oppressive — the warmth prevents it from feeling like a void.
- **Warm Chalk** (oklch(95% 0.005 65)): Primary text on dark surfaces.
- **Warm Smoke** (oklch(75% 0.008 65)): Secondary text on dark surfaces.
- **Warm Mist** (oklch(55% 0.008 65)): Muted text on dark surfaces.
- **Warm Deep** (oklch(30% 0.014 65)): Structural borders on dark surfaces.
- **Warm Subtle Dark** (oklch(24% 0.012 65)): Subtle backgrounds — hover surfaces, skeletal states on dark.

### Named Rules
**The Monokai Orange Rule.** Monokai Orange is the system's only saturated accent for surfaces and interactions. It must appear on every page in at least one intentional surface role — never as mere decoration, never as a one-pixel stripe. Its presence signals that the page is complete.

**The No-Pure-Black Rule.** Neither `#000` nor `#fff` appear anywhere. Every neutral is tinted toward the 65° hue axis at chroma 0.005–0.014.

**The Monokai Green Rule.** Monokai Green is reserved for code and technology indicators only. It marks programming languages, technical metadata, and code-related UI. It never fills a surface larger than a chip or icon container. Its role is functional identification, not brand expression.

**The Monokai Pink Rule.** Monokai Pink is deployed at most once per page. If two elements on the same page use it, at least one is wrong. Its only permitted use is for rare high-emphasis moments — a single stat, a single badge, a single call-to-action that must cut through.

## 3. Typography: The Workhorse Pair

**Display Font:** Bricolage Grotesque (weights 600, 700, 800)
**Body Font:** Albert Sans (weights 400, 500, 600)

**Character:** Bricolage Grotesque is a compressed, confident grotesk with tight apertures and sharp terminals — it dominates through mass, not ornament. Albert Sans counters with warmth and readability: open counters, generous x-height, humanist proportions. Together they create the voice of someone who thinks in precise terms but communicates with clarity.

### Hierarchy
- **Display** (800, clamp(2.5rem, 10vw + 0.5rem, 9rem), line-height 0.85, tracking -0.04em): Hero headlines only. The home page uses this at full scale; project pages dial it back to headline scale.
- **Headline** (800, clamp(2.5rem, 8vw + 0.5rem, 6rem), line-height 0.85, tracking -0.04em): Page titles on interior pages.
- **Title** (700, clamp(1.4rem, 3vw + 0.3rem, 2.5rem), line-height 0.9): Section headers within pages. Used for the callout card headline on the home page.
- **Body** (400/500, 1rem/1.125rem, line-height 1.6): Running text. 400 for paragraphs, 500 for medium emphasis. 65ch max line length on prose.
- **Label** (600, 0.75rem, letter-spacing 0.05em, uppercase): Filter labels, tab text, button text, navigation items. Small but assertive — the uppercase + tracking ensures legibility at scale.

### Named Rules
**The Weight Jump Rule.** Adjacent typographic steps differ by at least 100 weight units or a 1.25× size ratio. 600 → 800 display, 400 → 500 → 600 body. No 500 → 550 weight gaps.

**The Display-Only Rule.** Bricolage Grotesque is reserved for headings (display, headline, title levels). Body copy, labels, and UI chrome use Albert Sans exclusively.

## 4. Elevation: Flat by Default

This system conveys depth through tonal layering, not shadows. No `box-shadow` values are defined anywhere in the design system — the flatness is intentional and complete.

On light surfaces, depth is communicated by subtracting lightness: the warm page background (99%) drops to warm subtle (92%) for inset surfaces like tab toggles, and to full Monokai Orange for the callout card — a dramatic tonal jump that reads as "forward" without any shadow. On dark surfaces, the same logic inverts: charcoal base (18%) rises to warm deep (30%) for structural emphasis.

**The No-Shadow Rule.** Shadows are prohibited at all levels. If a surface needs to read as elevated, darken or lighten the background (tonal layering) or use a 1px border (structural layering). If a shadow would be the only answer, redesign the element.

## 5. Components

Every component shares a philosophy: precise and deliberate. Interactions are crisp, transitions are exponential-out, active states are felt through scale shifts (`scale(0.98)`) and brightness changes — never through glow, blur, or gratuitous animation.

### Buttons

**Shape:** 8px radius (`rounded-lg`). All buttons share the same corner treatment.

**Primary** (Monokai Orange fill, Warm Ink text): The action button. Used for the email and social links on the home page. Hover brightens to 110%. Active presses down with 0.98 scale and 95% brightness. Disabled state drops to 50% opacity. Focus ring is Monokai Orange at 50% opacity with 2px ring offset.

**Outline** (transparent fill, Warm Border stroke, Warm Steel text): The secondary action. Used for filter controls and secondary navigation. Hover shifts border to Warm Slate and fills with Warm Subtle. Focus ring matches primary.

**Ghost** (transparent, Warm Steel text): The tertiary action. Hover adds underline with 4px offset to Warm Ink. No background shift — the underline is the entire state change. Focus ring matches.

### Chips / Tags

Two distinct chip styles, both using the label typography tier:

**Language Chip:** 18% Monokai Green background with full Monokai Green text. Fully rounded (pill shape). Used on GitHub repo items to mark programming languages. Read-only, not interactive.

**Status Chip:** Warm Subtle background (dark: Warm Subtle Dark) with Warm Steel text. Fully rounded. Used on personal projects to show development status (active, completed, etc.). Read-only.

### Inputs / Selects

**Style:** Warm Border 1px stroke (dark: Warm Deep), 8px radius, 10px vertical / 12px horizontal padding. Background alternates between the Warm Subtle tint and transparent depending on context. Label positioned above in label typography tier (600, 0.75rem, uppercase, 0.05em tracking, Warm Steel).

**Focus:** 2px Monokai Orange ring with 2px offset from surface background. Matches the global `:focus-visible` treatment.

### Navigation

**Header:** Fixed position, top-right corner. Contains the language switcher and ThemeToggle (a Svelte Blade Island component). No background — floats over page content. Safe area insets respected on notched devices.

**Tab Toggle:** Rounded pill container (12px radius) with Warm Subtle background. Active tab gets the surface background (Warm Page light / Warm Charcoal dark) and a subtle shadow-sm — the single intentional shadow in the system, used only here to create a "pressed forward" pill effect. Keyboard navigable with arrow keys and Home/End.

**Footer:** Minimal — 1px Warm Border top, copyright + tagline in label typography. No navigation links, no sitemap, no social icons.

### Cards / Containers

**Callout Card** (Monokai Orange fill, 16px radius): The home page's right-column element. Dense internal padding (24px mobile, 32px desktop). Contains a title, description, and action buttons. The Monokai Orange surface is the visual counterweight to the massive display headline on the left — the card doesn't need elevation because the color itself creates the hierarchy.

**List Items:** GitHub repos and personal projects use border-bottom dividers (Warm Border 1px), not cards. Hover states add a subtle background that extends full-width via negative margin. Project items include a 48px square icon container (12px radius) with Monokai Orange tinted background.

### Named Rules
**The Press-Feel Rule.** Every interactive element has an active state: either `scale(0.98)`, a brightness shift, or both. If an element is clickable and pressing it produces no tactile feedback, that's a bug.

**The No-Glow Rule.** Hover and focus states never use `box-shadow` glow effects. Focus is always a solid ring. Hover is always a background or text color shift. The absence of glow is the signature — precision over diffusion.

## 6. Do's and Don'ts

### Do:
- **Do** use massive display typography (clamp to 9rem) as the primary visual element on hero surfaces. The type IS the visual.
- **Do** counterweight large text blocks with a solid Monokai Orange surface — asymmetry is the default layout.
- **Do** use Bricolage Grotesque exclusively for headings and Albert Sans for everything else. Never swap roles.
- **Do** keep body text at 1rem minimum, capped at 65ch line length for prose.
- **Do** convey hierarchy through tonal layering (background lightness shifts) rather than borders or shadows.
- **Do** use `scale(0.98)` and brightness shifts for all active/pressed states.
- **Do** use exponential-out easing curves (`ease-out-quart`, `ease-out-expo`) for all transitions.
- **Do** respect `prefers-reduced-motion` — all animations and transitions collapse to 0.01ms when the user requests reduced motion.
- **Do** use Monokai Green exclusively for code and technology indicators — language chips, tech metadata, code-related UI.
- **Do** use Monokai Pink at most once per page, for high-emphasis moments only.

### Don't:
- **Don't** use `box-shadow` for elevation. The system is flat. Tonal layering or 1px borders convey depth instead.
- **Don't** use glassmorphism, gradient text, or side-stripe borders — these are the AI-slush signatures this system rejects.
- **Don't** use centered hero layouts. The home page is split 55/45. Interior pages are left-aligned.
- **Don't** create identical card grids — icon + heading + text repeated in same-sized tiles is a template, not a design.
- **Don't** use `#000` or `#fff` anywhere. All neutrals are tinted toward 65° at chroma ≤0.014.
- **Don't** add filler UI text — "Scroll to explore", "Discover more", scroll arrows, bouncing chevrons. The content pulls the eye; it doesn't need instructions.
- **Don't** use circular loading spinners. Skeletons match the layout shapes they replace, using Warm Subtle background with a pulse animation.
- **Don't** exceed one primary CTA button per surface. If everything is a button, nothing is the action.
- **Don't** use Monokai Green as a surface fill, accent, or brand expression. It is a functional marker only.
- **Don't** use Monokai Pink more than once per page. It is a punctuation mark, not a theme.
