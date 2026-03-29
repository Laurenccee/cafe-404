# Design System Document: The Editorial Barista

## 1. Overview & Creative North Star
**Creative North Star: "The Artisanal Ledger"**

This design system rejects the sterile, "SaaS-blue" aesthetics of typical POS systems in favor of a high-end editorial experience. It is designed to feel like a premium lifestyle magazine transitioned into a functional tool. We achieve this through **Organic Asymmetry** and **Tonal Depth**. 

While the system is built for the high-speed environment of a coffee shop, it prioritizes "Calm Productivity." By using generous whitespace, sophisticated serif headings, and a palette rooted in natural earth tones, we transform a management interface into a digital extension of the physical cafe's warmth. We move beyond the "grid of buttons" by treating every screen as a curated layout.

---

## 2. Colors: Tonal Transitions & The "No-Line" Rule
Our palette is a spectrum of roasted depths and steamed creams, punctuated by a vibrant "Matcha" green for high-impact intent.

### Color Roles
*   **Primary (`#002c02`):** The Deep Matcha. Used for high-priority actions, primary brand moments, and active states.
*   **Secondary (`#79573f`):** The Roasted Bean. Used for structural secondary elements and navigational cues.
*   **Surface (`#fcf9f8`):** The Steamed Milk. Our canvas. It provides a warm, off-white base that reduces eye strain compared to pure white.

### The "No-Line" Rule
**Explicit Instruction:** Do not use 1px solid borders to section content. Traditional borders create visual noise that slows down cognitive processing in fast-paced environments. Instead:
*   **Define boundaries** solely through background color shifts. A `surface-container-low` card sitting on a `surface` background is sufficient to define an edge.
*   **The Glass & Gradient Rule:** Use subtle gradients (e.g., `primary` to `primary_container`) for main CTAs to give them "soul." For floating modals, use **Glassmorphism**: semi-transparent surface colors with a `12px` to `20px` backdrop-blur to keep the user grounded in the workspace.

---

## 3. Typography: The Editorial Contrast
We pair the functional clarity of a sans-serif with the authority of a serif to create a "Premium Service" feel.

*   **Display & Headlines (`notoSerif`):** Use these for page titles, large metrics (e.g., daily revenue), and brand moments. The serif evokes the sophistication of a printed menu.
*   **UI & Body (`manrope`):** All functional elements (buttons, inputs, lists) must use the sans-serif. It is engineered for legibility at small scales during rapid interactions.
*   **Hierarchy Note:** Use `display-lg` (`3.5rem`) for empty states or hero moments. Use `label-sm` (`0.6875rem`) sparingly, only for non-critical metadata, ensuring it maintains high contrast against the `surface` tokens.

---

## 4. Elevation & Depth: Tonal Layering
Depth in this system is not about "rising off the page" with heavy shadows; it is about "stacking" layers of fine paper.

*   **The Layering Principle:** 
    *   **Base:** `surface`
    *   **Low Priority Content:** `surface_container_low`
    *   **Standard Cards:** `surface_container_lowest` (creates a soft "lift" against a low-tier background).
*   **Ambient Shadows:** If a floating element (like a dropdown) requires a shadow, use a large blur (`24px`+) at `4%` opacity. The shadow color must be a tinted version of the coffee browns (`secondary`), never pure black.
*   **The "Ghost Border" Fallback:** If accessibility requires a border, use `outline_variant` at **20% opacity**. This provides a "suggestion" of a line without breaking the editorial flow.

---

## 5. Components: Tactile & Intentional

### Buttons
*   **Primary:** `primary` background with `on_primary` text. Use `xl` (`0.75rem`) roundedness.
*   **Secondary:** `secondary_container` background with `on_secondary_container` text.
*   **Tertiary:** No background; `primary` text. Use for low-emphasis actions like "Cancel."

### Cards & Lists
*   **Forbid Dividers:** Do not use horizontal lines between list items. Use vertical spacing (Scale `4`: `0.9rem`) or alternating `surface_container` tints to separate orders or inventory items.
*   **Stateful Hover:** On hover, a card should transition from `surface_container_low` to `surface_container_high`.

### Input Fields
*   **Style:** Minimalist. A subtle `surface_variant` background with a bottom-heavy `outline_variant` (2px).
*   **Focus State:** The Matcha `primary` color should animate as a glow or a thickened bottom border.

### Contextual Components (The "Barista Tools")
*   **Order Status Chips:** Use `tertiary_fixed` for "Brewing" and `primary_fixed` for "Ready."
*   **Tactile Quantity Steppers:** Large, `md` rounded buttons (`0.375rem`) to ensure ease of use on touchscreens behind the counter.

---

## 6. Do's and Don'ts

### Do:
*   **Use Asymmetry:** Place a large `display-md` heading on the left and a small `label-md` metadata group on the right to create a sophisticated, non-template look.
*   **Embrace Negative Space:** Use spacing scale `16` (`3.5rem`) between major sections to let the UI breathe.
*   **Prioritize Matcha for Action:** Only use the Matcha green for elements that move the business forward (e.g., "Complete Order," "Save Changes").

### Don't:
*   **Don't use "System Red" for everything:** Reserve `error` (`#ba1a1a`) for critical failures. For "Out of Stock," use the muted `secondary` or `outline` tokens.
*   **Don't use 100% Black:** Always use `on_surface` (`#1b1c1c`) for text to maintain the warmth of the coffee-inspired palette.
*   **Don't crowd the screen:** If a screen feels busy, increase the spacing scale rather than adding borders or dividers.
