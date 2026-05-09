---
tags: [flowflex, design, brand, identity]
domain: Design System
status: built
last_updated: 2026-05-08
---

# Brand Foundation

The core identity and personality of FlowFlex. Every design decision traces back here. Updated for 2026 — including motion branding, AI brand moments, and voice standards for conversational interfaces.

## Who FlowFlex Is

FlowFlex is the AI-native operating system for modern businesses. Not a startup tool, not an enterprise monolith — something deliberately in between. It is calm, capable, and quietly powerful. The product should feel like a well-designed workspace: clean surfaces, clear hierarchy, nothing screaming for attention.

In 2026, AI is not a feature in FlowFlex — it is infrastructure. The brand must communicate this without resorting to robot imagery or blue orb clichés. FlowFlex AI feels like a knowledgeable colleague, not a chatbot.

## What the Brand Communicates

- **Trust** — finance, HR, legal, and now AI-processed data lives here. The UI must feel secure and serious.
- **Clarity** — complex data and AI outputs presented simply. No clutter, no noise, no hallucination anxiety.
- **Flow** — interactions feel smooth and continuous. AI responses, transitions, and data loading are all part of the flow.
- **Flex** — the platform adapts to the customer, not the other way around. AI personalises without intruding.
- **Intelligence** — quiet, confidence-inspiring AI that works in the background and surfaces insight at the right moment.

## Brand Personality

| Trait | What it means in the UI |
|---|---|
| Calm | Low visual noise, generous whitespace, muted tones — including AI loading states |
| Confident | Strong typographic hierarchy, decisive colours, AI suggestions feel grounded not speculative |
| Modern | Clean geometry, variable fonts, spring physics, CSS @layer architecture |
| Trustworthy | Consistent spacing, predictable patterns, AI outputs are clearly attributed and auditable |
| Warm | Not cold-corporate — slight warmth in the palette, human photography, AI voice that sounds human |
| Intelligent | AI features are subtle, contextual, and never intrusive |

## What FlowFlex Is NOT

- Not playful or startup-cute (no loud colour pops, no rounded blobs, no confetti, no AI sparkle emoji overload)
- Not cold enterprise grey (not SAP, not Salesforce Classic)
- Not minimal to the point of confusion (whitespace with purpose, not emptiness)
- Not dark-by-default (light is primary; dark mode is a choice, not a statement)
- Not AI-first in a gimmicky way (AI is infrastructure, not a branding tactic)
- Not "GPT-wrapper" energy — FlowFlex AI is deeply integrated, not bolted on

## The Name

`FlowFlex` — always written exactly this way:
- Capital F, capital F, no space, no hyphen, no dot
- Never: `Flowflex`, `flow flex`, `FLOWFLEX`, `flow-flex`
- In UI headings and marketing: `FlowFlex`
- In code (namespaces, config keys, env variables): `flowflex`
- In domain names and URLs: `flowflex.com`, `app.flowflex.com`

## Taglines

**Primary:** "Your business, your tools — in flow."

**Secondary options:**
- "Everything your business needs. Only what you actually use."
- "One platform. Every tool. Your way."
- "Built to flex with you."
- "The AI-native operating system for modern business." *(2026 positioning — use in AI-forward contexts)*

**When to use AI-forward taglines:** Product hunt launches, developer/technical audiences, analyst briefings, AI-specific feature announcements. Not on the homepage hero by default — lead with the core value proposition first.

## Logo

**Mark concept:** Stylised double wave form — two fluid, overlapping curves suggesting motion, continuity, and interconnection. Not symmetrical — one wave is slightly larger, suggesting the platform scaling around the customer.

### Logo Versions

| Version | Usage |
|---|---|
| Horizontal lockup (mark + wordmark side by side) | Primary — nav headers, email, marketing |
| Stacked lockup (mark above wordmark) | Square contexts — app icons, favicons (32px+) |
| Mark only | Favicon (16px), loading spinners, tiny contexts |
| Wordmark only | When mark is already established nearby |

### Logo Colours

| Variant | Use on |
|---|---|
| Ocean (primary teal gradient mark + dark wordmark) | Light backgrounds |
| White (all white) | Dark backgrounds, dark nav bars |
| Mono dark (`#0D1F2D`) | Print, single-colour contexts |
| Mono light (`#FFFFFF`) | Print on dark, embossing |

### Logo Sizes

| Version | Minimum width |
|---|---|
| Horizontal lockup | 120px |
| Stacked lockup | 64px |
| Mark only | 24px |

### Clear Space

Minimum clear space = height of the capital F in the wordmark, on all sides.

## Motion Branding

Motion is part of the FlowFlex brand identity in 2026. These principles govern all animated moments:

**Logo animation (app loading, onboarding):**
- The two wave marks draw in sequentially — first wave, pause 80ms, second wave
- Duration: 600ms total, spring physics (`cubic-bezier(0.34, 1.56, 0.64, 1)`)
- The wordmark fades in after mark completes, 200ms, `ease-decelerate`
- Never use a spinning logo — that communicates loading anxiety, not brand confidence

**Transition signature:**
- Page transitions use a subtle horizontal content shift (8px, opacity) — not a full slide
- This feels like pages are on a continuous surface, reinforcing "flow"

**AI loading signature:**
- AI generation in progress: three dots with wave-like sequential animation (not a standard spinner)
- Dots use ocean-400/500/600 graduating colours to suggest depth
- Duration per dot cycle: 600ms, offset by 150ms per dot
- Always paired with contextual text ("Analysing your data…", not "Loading…")

**Micro-interaction signature:**
- Success actions: single subtle scale pulse on the affected element (1.0 → 1.03 → 1.0), 200ms
- Error states: single horizontal shake (±4px), 2 oscillations, 300ms total
- These are brand-consistent feedback signals — use them everywhere

## AI Brand Moments

When AI features surface in the UI, they must feel distinctly FlowFlex — not generic ChatGPT-style.

**AI suggestion chips:**
- Subtle ocean-50 background, ocean-300 left border, `sparkle` icon in ocean-400
- Never say "AI suggests:" — say "Suggested:", "Based on your data:", "You might want to:"
- Dismiss with a single click — never require explanation for dismissal

**AI-generated content labels:**
- All AI-drafted content (emails, summaries, reports) must carry a discrete "AI draft" badge
- Badge: `tide-100` background, `tide-600` text, positioned top-right of the content block
- Users can accept, edit, or regenerate — always three options, never forced accept

**Conversational AI interface:**
- See [[AI & Conversational UI]] for full specifications
- Voice of AI: calm, direct, helpful, concise. Never sycophantic ("Great question!"). Never uncertain ("I think maybe…").
- AI always admits what it doesn't know and suggests next steps

**Error in AI context:**
- When AI cannot complete a task: "I couldn't complete this — [reason]. Here's what I can do instead: [alternative]."
- Never: "Something went wrong with AI" — be specific, always

## Brand Values

- **Simplicity over features** — don't add a feature if it adds confusion
- **Modularity is a feature** — never force a customer to use what they don't need
- **Data integrity is sacred** — if two modules share data, it must always be consistent; AI outputs are auditable
- **Speed matters** — pages load fast, actions respond immediately, AI responses stream instantly
- **Privacy is non-negotiable** — customer data is never used to train AI models; this is stated clearly in the UI

## Photography & Illustration

**Photography (marketing):**
- Real working environments — not stock photo "office lifestyle" with forced smiles
- Show diverse teams working with actual software interfaces
- Avoid: coffee cups on laptops, whiteboard diagrams, generic cityscape backgrounds

**Illustration (in-app):**
- Simple, geometric, ocean colour palette
- Empty states use minimal line illustrations, not photographs
- AI feature illustrations: flowing lines and data patterns, not robot characters or brain imagery
- Max colours in any illustration: 3 (from the FlowFlex palette only)

## Related

- [[Colour System]]
- [[Typography]]
- [[Writing Style & Voice]]
- [[Motion & Animation]]
- [[AI & Conversational UI]]
- [[FlowFlex Overview]]
