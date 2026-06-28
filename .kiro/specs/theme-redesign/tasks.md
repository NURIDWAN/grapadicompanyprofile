# Implementation Plan: Theme Redesign

## Overview

Redesign the Grapadi International company profile website from a forest green primary color with light/dark mode to a premium dark-only theme with gold/amber accents. The implementation modifies the Tailwind CSS v4 @theme configuration, refactors existing Blade components, creates new components (Stats, CTA Banner, FAQ), and updates the home page composition — all while preserving the existing Laravel + Alpine.js architecture.

## Tasks

- [x] 1. Update theme configuration and typography
  - [x] 1.1 Replace the Tailwind CSS @theme block in `resources/css/app.css` with the new gold/dark color palette
    - Remove all green primary palette tokens (--color-primary-50 through --color-primary-950 green values)
    - Remove --color-navy-brand and light mode tokens (--color-background-light, --color-surface-light)
    - Add gold primary palette (50: #FDF8E8 through 950: #322810, primary: #C9A84C)
    - Add background tokens (--color-background-dark: #0A0A0F, --color-surface-dark: #12121A)
    - Add border tokens (--color-border-dark: #1E1E2A)
    - Define font-display as 'Cormorant Garamond', serif and font-body as 'Montserrat', sans-serif
    - Add the `.text-gold-gradient` utility class with linear-gradient(135deg, #C9A84C 0%, #E5C157 50%, #C9A84C 100%) and background-clip: text
    - Remove the @custom-variant dark switching mechanism
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 2.1, 2.2, 2.4_

  - [x] 1.2 Update `resources/views/layouts/app.blade.php` to enforce dark-only mode
    - Set the `dark` class unconditionally on the HTML root element
    - Remove any dark mode toggle component inclusion
    - Update Google Fonts link to include Cormorant Garamond (with font-display: swap)
    - Add preconnect hints for Google Fonts CDN
    - Ensure Montserrat is still loaded with font-display: swap
    - _Requirements: 1.5, 2.3, 13.1_

  - [x] 1.3 Write tests for theme token contrast compliance
    - **Property 7: Color contrast compliance**
    - **Property 1: Dark-only consistency**
    - Verify gold text (#C9A84C) on #0A0A0F and #12121A meets ≥7:1 contrast ratio
    - Verify muted text (#9CA3AF) on dark backgrounds meets ≥7:1 contrast ratio
    - Verify HTML root always has "dark" class and no light mode tokens exist
    - **Validates: Requirements 1.6, 10.1, 10.2, 10.3**

- [x] 2. Refactor the Navbar component
  - [x] 2.1 Rewrite `resources/views/components/navbar.blade.php` with the new dark theme and navigation structure
    - Always dark background (bg-background-dark or bg-surface-dark)
    - Display menu items: Beranda, Layanan, Industri, Case Studies, Insights, Tentang Kami
    - Add gold-styled "Konsultasi" CTA button on the right
    - Implement scroll shadow behavior with Alpine.js (shadow after 20px scroll)
    - Add active page indicator using gold underline or text color based on current route
    - Remove any dark mode toggle button
    - Implement mobile hamburger menu with slide-down panel for viewports below 768px
    - Add aria-expanded attribute on mobile menu toggle, ensure Tab key navigation in panel
    - Add noscript fallback for navigation when JavaScript is unavailable
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 13.4_

  - [ ]* 2.2 Write tests for Navbar scroll behavior and active route indication
    - **Property 2: Navbar scroll shadow**
    - **Property 3: Active route indication**
    - Test shadow class appears when scrolled > 20px and disappears at ≤ 20px
    - Test that only the active route menu item has gold styling
    - **Validates: Requirements 3.4, 3.6**

- [x] 3. Refactor the Hero Section component
  - [x] 3.1 Rewrite `resources/views/components/hero-section.blade.php` with the new dark + gold design
    - Full-width section with min-h-screen, dark building background image at reduced opacity
    - Top-to-bottom dark overlay gradient (from-background-dark/80 to-background-dark)
    - Display headline in font-display at 3rem mobile / 4.5rem desktop with gold gradient on key words
    - Display subheadline in muted gray text
    - Primary CTA button (gold filled, dark text) and secondary CTA button (gold outline)
    - Both buttons keyboard-focusable with visible focus indicators (2px, ≥3:1 contrast)
    - Graceful fallback when background image fails (dark color shows through overlay)
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [x] 3.2 Add the stats bar within the Hero Section
    - Display 4 stat items below the hero main content area
    - Each stat: gold Material Icon, bold numeric value, descriptive label
    - Use inline stats within hero or separate sub-section depending on layout
    - _Requirements: 4.6_

- [x] 4. Create new Stats Section component
  - [x] 4.1 Create `resources/views/components/stats-section.blade.php`
    - 4-column grid on viewports ≥ 768px, 2-column grid on mobile
    - Each stat item: gold-colored Material Icon, bold number (≥24px), descriptive label
    - Dark surface background (#12121A) or transparent with 1px border-dark border
    - Handle missing stat fields gracefully (omit item without rendering error)
    - Accept stats array prop with icon, number, and label fields
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

  - [ ]* 4.2 Write tests for stat item rendering completeness
    - **Property 4: Stat item rendering completeness**
    - Verify each stat item renders gold icon, bold number, and label
    - Verify missing fields cause item omission without errors
    - **Validates: Requirements 5.3, 5.5**

- [x] 5. Create new CTA Banner component
  - [x] 5.1 Create `resources/views/components/cta-banner.blade.php`
    - Dark card (bg-surface-dark) with 1px gold/amber border (border-primary at reduced opacity)
    - Centered content with vertical padding ≥ 48px
    - Title in font-display with gold text color, max 80 characters
    - Single gold-filled CTA button (primary background, dark text) with link
    - Keyboard focus indicator on button (≥3:1 contrast, 2px thickness)
    - Responsive single-column centered layout from 320px to 2560px without overflow
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5_

- [x] 6. Create new FAQ Section component
  - [x] 6.1 Create `resources/views/components/faq-section.blade.php`
    - 2-column grid on viewports ≥ 768px, single-column on mobile
    - Each FAQ item: dark card (bg-surface-dark), border-dark border, gold expand/collapse icon
    - Alpine.js toggle for each item (x-data with isOpen state)
    - All answers collapsed by default on page load
    - Each toggle independent of others
    - Add role="button", aria-expanded (true/false), aria-controls referencing answer element ID
    - Respond to Enter and Space key presses
    - Graceful degradation: show all answers expanded when Alpine.js fails to load
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 13.3_

  - [ ]* 6.2 Write tests for FAQ interaction and keyboard accessibility
    - **Property 5: FAQ interaction and keyboard accessibility**
    - Test toggle behavior on click and Enter/Space keys
    - Verify aria-expanded and aria-controls attributes are correctly set
    - **Validates: Requirements 7.3, 7.4, 7.5, 7.6**

- [x] 7. Checkpoint - Ensure all new components render correctly
  - Ensure all tests pass, ask the user if questions arise.

- [x] 8. Refactor the Service Card component
  - [x] 8.1 Rewrite `resources/views/components/service-card.blade.php` with the dark + gold design
    - Dark surface background (bg-surface-dark) with border-dark and rounded corners
    - Gold-colored Material Icon in a subtle icon container
    - White title text, gray/muted description text
    - Gold "Explore" link with trailing arrow icon at bottom
    - Hover: transition border color to gold at 30% opacity (≤300ms duration)
    - Visible focus indicator on the link for keyboard users
    - Handle empty/null description gracefully (render icon, title, link without breakage)
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

  - [ ]* 8.2 Write tests for Service Card element completeness
    - **Property 6: Service card element completeness**
    - Verify all elements render with valid props
    - Verify graceful handling of null description
    - **Validates: Requirements 8.2, 8.5**

- [x] 9. Refactor the Footer component
  - [x] 9.1 Rewrite `resources/views/components/footer.blade.php` with the new dark design
    - Background using background-dark color token
    - Company logo and name top-left
    - Three link columns: Menu, Layanan, Kontak (each with ≥2 links)
    - Muted text color for links
    - Gold hover color (#C9A84C) on link hover
    - Social media icons row (LinkedIn, Instagram minimum) linking to external URLs
    - Copyright text bar at bottom with company name and current year
    - Visible focus indicator on all links for keyboard navigation
    - Handle null site settings (logo, company name) with predefined fallback values
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 9.7, 9.8_

  - [ ]* 9.2 Write tests for graceful degradation with null settings
    - **Property 10: Graceful degradation with null settings**
    - Verify footer renders with fallback values when site_settings return null
    - **Validates: Requirements 9.6, 13.5**

- [x] 10. Update the Home Page composition
  - [x] 10.1 Update `resources/views/pages/home.blade.php` with the new section order and layout
    - Render sections in order: Hero, Stats, Client Logos ("Trusted By"), CTA Banner, Services, FAQ, Final CTA Banner, Footer
    - Apply vertical padding: 80px (5rem) between sections on ≥1024px, 48px (3rem) below 1024px
    - Hide sections entirely when data source returns empty/null (no empty containers)
    - Navbar rendered via layout (fixed top, not in section order)
    - Constrain main content to max-width 1536px centered on viewports ≥1536px
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 11.4_

  - [x] 10.2 Update `resources/views/components/client-logos.blade.php` to match dark theme
    - Dark background styling consistent with new theme
    - Section title "Trusted By" or equivalent
    - _Requirements: 12.1_

- [x] 11. Implement responsive layout and accessibility polish
  - [x] 11.1 Ensure responsive breakpoints and touch targets across all components
    - Verify all content renders without horizontal scrollbar from 320px to 2560px
    - Apply layout breakpoints: 640px (sm), 768px (md), 1024px (lg), 1280px (xl), 1536px (2xl)
    - Multi-column grids collapse to single-column below 768px
    - Heading fonts scale ≥20% larger at 768px and above
    - Interactive elements have minimum 44x44px touch targets on mobile (<768px)
    - _Requirements: 11.1, 11.2, 11.3, 11.5_

  - [x] 11.2 Add visible focus indicators to all interactive elements
    - Buttons, links, toggles, and inputs must show focus indicator on keyboard focus
    - Focus indicator: ≥3:1 contrast against adjacent colors, minimum 2px thickness
    - Apply consistently across navbar, hero CTAs, CTA banner button, FAQ toggles, service card links, footer links
    - _Requirements: 10.4_

  - [ ]* 11.3 Write tests for no horizontal overflow and focus indicators
    - **Property 9: No horizontal overflow**
    - **Property 8: Focus indicator visibility**
    - Test pages render without horizontal scrollbar at various viewport widths
    - Verify interactive elements have visible focus indicators
    - **Validates: Requirements 11.1, 10.4**

- [x] 12. Final checkpoint - Ensure all tests pass and full integration
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Property tests validate universal correctness properties from the design document
- The project uses PHP/Laravel Blade for templates, Tailwind CSS v4 for styling, and Alpine.js for interactivity
- No new npm packages are required — only existing infrastructure is used with updated configuration
- All components should use `site_setting()` calls with fallback values for resilience

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1"] },
    { "id": 1, "tasks": ["1.2", "4.1", "5.1", "6.1"] },
    { "id": 2, "tasks": ["1.3", "2.1", "3.1", "4.2", "6.2", "8.1", "9.1"] },
    { "id": 3, "tasks": ["2.2", "3.2", "8.2", "9.2"] },
    { "id": 4, "tasks": ["10.1", "10.2", "11.1", "11.2"] },
    { "id": 5, "tasks": ["11.3"] }
  ]
}
```
