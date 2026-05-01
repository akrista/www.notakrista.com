<!-- SEED — re-run `$impeccable document` once there's code to capture the actual tokens and components. -->

---
name: notakrista.com
description: Multi-framework Laravel showcase — Livewire, React, Vue, Svelte
---

# Design System: notakrista.com

## 1. Overview

**Creative North Star: "The Framework Gallery"**

A clean, technically precise showcase where each framework gets equal visual weight. The design should feel like a well-organized exhibition — spacious, intentional, letting the work speak. Three to four named color roles carry the surface deliberately. Motion is responsive: feedback and transitions on interactions, no choreographed sequences.

This system explicitly rejects generic SaaS template aesthetics — identical card grids, hero-metric layouts, gradient text, and AI-generated sameness.

**Key Characteristics:**
- Framework parity — no framework is visually privileged over another
- Full palette — 3–4 named color roles, each with a clear purpose
- Single sans typography — Instrument Sans, warm but technical
- Responsive motion — transitions on state changes, no orchestrated sequences
- Laravel ecosystem native — feels at home alongside Laravel.com

## 2. Colors

**The Full Palette Rule.** Three to four named color roles, each used deliberately. No color exists without a job.

### Primary
- **Vivid Green** (oklch(72% 0.19 155)): Primary actions, focus rings, key interactive elements. The main accent.

### Secondary
- **Deep Navy** (oklch(25% 0.08 260)): Dark surfaces, structural elements, secondary emphasis. Carries weight without competing with the primary.

### Neutral
- **Surface Light** (oklch(99% 0.005 260)): Primary light background. Barely tinted toward the brand hue.
- **Surface Dark** (oklch(15% 0.02 260)): Primary dark background. Deep, not pure black.
- **Text Primary Light** (oklch(18% 0.02 260)): Body text on light surfaces.
- **Text Secondary Light** (oklch(45% 0.02 260)): Secondary text, labels, muted content on light surfaces.
- **Text Primary Dark** (oklch(95% 0.005 260)): Body text on dark surfaces.
- **Text Secondary Dark** (oklch(75% 0.01 260)): Secondary text on dark surfaces.
- **Border Light** (oklch(90% 0.01 260)): Subtle dividers and field borders on light surfaces.
- **Border Dark** (oklch(30% 0.02 260)): Subtle dividers and field borders on dark surfaces.

### Named Rules
**The Tinted Neutral Rule.** No pure `#000` or `#fff`. Every neutral carries a trace of the brand hue (chroma 0.005–0.01). This keeps the palette cohesive without being obvious.

## 3. Typography

**Display Font:** Instrument Sans (with ui-sans-serif, system-ui fallback)
**Body Font:** Instrument Sans (same family, different weights)

**Character:** A single sans family across the board — warm but technical, approachable but precise. Hierarchy through scale and weight contrast, not font switching.

### Hierarchy
- **Body** (400–500, 13–16px, 1.4–1.6 line-height): Default text. Cap line length at 65–75ch.
- **Headline** (600, 18–24px, 1.3 line-height): Section headers, card titles.
- **Display** (600, clamp-based responsive scale): Hero headlines only.

### Named Rules
**The Single Family Rule.** One typeface, multiple weights. Hierarchy comes from scale (≥1.25 ratio between steps) and weight contrast, not font pairing.

## 4. Elevation

Flat by default. Depth is conveyed through tonal layering and subtle borders rather than shadows. The existing system uses inset box-shadows for card edges — a refined alternative to drop shadows.

### Shadow Vocabulary
- **Inset Edge** (`inset 0 0 0 1px rgba(26,26,0,0.16)`): Card and container edges on light surfaces. Creates a refined, contained feel without external shadows.
- **Inset Edge Dark** (`inset 0 0 0 1px #fffaed2d`): Card and container edges on dark surfaces.

### Named Rules
**The Flat-By-Default Rule.** Surfaces are flat at rest. No drop shadows on cards, modals, or panels. Depth comes from tonal contrast and border treatment.

## 5. Components

### Buttons
- **Shape:** Gently rounded corners (8px / `rounded-lg`)
- **Primary:** Vivid Green background, dark text, minimum 44px height. Uppercase, tracking-wide for emphasis.
- **Outline:** Transparent background, 2px border, subtle hover fill. Ghost-like until interaction.
- **Ghost:** No border, text-only. Underline on hover with 4px offset.
- **Hover / Focus:** Brightness shift (1.1x) on primary, border color shift on outline, scale(0.98) on active. Focus ring: 2px Vivid Green at 50% opacity with 2px offset.
- **Transition:** 150ms ease-out-quart. Fast, responsive, no choreography.

### Inputs / Fields
- **Style:** Follow existing form patterns. Subtle border, clean background.
- **Focus:** 2px ring in Vivid Green at 50% opacity, 2px offset. Clear, accessible, not decorative.

### Navigation
- **Style:** Pill-shaped badges and links. Framework indicators use colored pills (e.g., pink for Livewire).
- **Default:** Small text (11px), medium weight, rounded-full containers.
- **Hover:** Border color shift to framework brand color on hover.

## 6. Do's and Don'ts

### Do:
- **Do** use the full palette deliberately — each of the 3–4 color roles should have a clear, documented purpose.
- **Do** keep motion responsive — 150–250ms transitions on state changes, ease-out-quart easing.
- **Do** use inset borders for container edges instead of drop shadows.
- **Do** maintain framework parity — each framework (Livewire, React, Vue, Svelte) gets equal visual treatment.
- **Do** cap body text at 65–75ch for readability.

### Don't:
- **Don't** use generic SaaS template patterns — identical card grids, hero-metric layouts, gradient text, or AI-generated sameness.
- **Don't** use pure `#000` or `#fff` — every neutral should carry a trace of the brand hue.
- **Don't** use drop shadows for elevation — the system is flat by default.
- **Don't** use border-left or border-right greater than 1px as a colored accent stripe on cards or list items.
- **Don't** use glassmorphism or backdrop-blur as a default treatment.
- **Don't** choreograph sequences — motion is for feedback, not performance.
