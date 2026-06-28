# Requirements Document

## Introduction

This document defines the requirements for the Grapadi International company profile website theme redesign. The redesign transitions the site from a forest green primary color with light/dark mode support to a premium dark-only theme with deep navy/black backgrounds and gold/amber accent colors. The visual layer is updated while preserving the existing component-based architecture (Laravel Blade + Tailwind CSS v4 + Alpine.js).

## Glossary

- **Theme_System**: The Tailwind CSS v4 @theme configuration that defines color tokens, typography, and spacing consumed by all UI components
- **Navbar**: The fixed top navigation component that displays menu items and CTA button
- **Hero_Section**: The full-width landing section with background image, headline, and call-to-action buttons
- **Stats_Section**: A new component displaying company statistics in a 4-column grid with gold icons
- **CTA_Banner**: A mid-page call-to-action card component with bordered design
- **FAQ_Section**: An expandable question-and-answer component in a 2-column grid layout
- **Service_Card**: A card component displaying an individual service offering
- **Footer**: The site-wide footer component with multi-column link layout
- **Gold_Gradient**: A CSS utility class that applies a linear gold gradient to text using background-clip
- **WCAG_AA**: Web Content Accessibility Guidelines level AA, requiring minimum 4.5:1 contrast ratio for normal text
- **Design_Token**: A named CSS custom property (e.g., --color-primary) that stores a design value for reuse across components

## Requirements

### Requirement 1: Theme Color System

**User Story:** As a website visitor, I want the site to use a premium dark theme with gold accents, so that the brand conveys a high-end strategic consulting aesthetic.

#### Acceptance Criteria

1. THE Theme_System SHALL define the primary color as gold (#C9A84C) with a full palette of 11 shade steps: shade 50 (#FDF8E8), shade 100 (#F9EDCC), shade 200 (#F0D88F), shade 300 (#E5C157), shade 400 (#D4B04A), shade 500 (#C9A84C), shade 600 (#A88A3C), shade 700 (#866D2F), shade 800 (#6B5726), shade 900 (#5A491F), and shade 950 (#322810)
2. THE Theme_System SHALL define the page background color as deep dark (#0A0A0F) and surface color as (#12121A)
3. THE Theme_System SHALL define border colors including a default dark border (#1E1E2A) and a gold accent border (rgba(201, 168, 76, 0.2))
4. THE Theme_System SHALL remove all light mode color tokens including --color-background-light, --color-surface-light, and the entire green primary palette (--color-primary-50 through --color-primary-950 green values, and --color-navy-brand)
5. WHEN any page is rendered, THE Theme_System SHALL apply the "dark" class to the HTML root element unconditionally, remove the dark mode toggle component, and remove the @custom-variant dark switching mechanism so that no light mode alternative is available
6. THE Theme_System SHALL ensure that gold text (#C9A84C) on the page background (#0A0A0F) maintains a minimum WCAG AA contrast ratio of 4.5:1 for normal text

### Requirement 2: Typography System

**User Story:** As a website visitor, I want to see elegant serif headings and clean sans-serif body text, so that the typography reinforces the premium consulting brand.

#### Acceptance Criteria

1. THE Theme_System SHALL define the display font as 'Cormorant Garamond' with serif fallbacks (Georgia, Times New Roman, serif)
2. THE Theme_System SHALL define the body font as 'Montserrat' with sans-serif system fallbacks (ui-sans-serif, system-ui, sans-serif)
3. WHEN Google Fonts fail to load, THE Theme_System SHALL display text immediately using CSS font-stack fallbacks with font-display: swap behavior, ensuring no invisible text period occurs
4. THE Theme_System SHALL provide a Gold_Gradient utility class that applies a linear gradient (135deg, #C9A84C 0%, #E5C157 50%, #C9A84C 100%) clipped to text using background-clip: text and -webkit-background-clip: text

### Requirement 3: Navigation

**User Story:** As a website visitor, I want clear navigation with Indonesian-language menu items, so that I can easily find relevant sections of the site.

#### Acceptance Criteria

1. THE Navbar SHALL display the following menu items as navigable links in order: Beranda, Layanan, Industri, Case Studies, Insights, Tentang Kami
2. THE Navbar SHALL display a gold-styled "Konsultasi" CTA button positioned to the right of menu items
3. THE Navbar SHALL always render with a dark background regardless of scroll position
4. WHEN the page is scrolled more than 20 pixels, THE Navbar SHALL display a visible box-shadow to indicate elevation, and WHEN the page is scrolled back to 20 pixels or fewer, THE Navbar SHALL remove the box-shadow
5. WHILE the viewport width is below 768px, THE Navbar SHALL replace the menu items with a hamburger menu icon that toggles a slide-down navigation panel containing all menu items and the Konsultasi CTA button
6. IF the current route matches a menu item's destination, THEN THE Navbar SHALL indicate the active page using a gold underline or gold text color on that menu item, and no other menu item SHALL display the active indicator
7. THE Navbar SHALL not include a dark mode toggle control
8. WHILE the viewport width is below 768px, THE Navbar mobile menu toggle SHALL be keyboard-accessible with an aria-expanded attribute reflecting the panel open/closed state, and the navigation panel SHALL be navigable using the Tab key

### Requirement 4: Hero Section

**User Story:** As a website visitor, I want an impactful landing section with clear calls to action, so that I understand Grapadi's value proposition immediately.

#### Acceptance Criteria

1. THE Hero_Section SHALL display a full-width section with a minimum height of 100vh, a dark building background image at reduced opacity, and a top-to-bottom dark overlay gradient from background-dark/80 to background-dark
2. THE Hero_Section SHALL display a headline using the display font at a size of 3rem on mobile viewports and 4.5rem on desktop viewports, with a configurable subset of words rendered using the Gold_Gradient utility
3. THE Hero_Section SHALL display a subheadline in muted gray text below the headline summarizing the value proposition
4. THE Hero_Section SHALL display a primary CTA button with gold filled styling and a secondary CTA button with gold outline styling, both keyboard-focusable with visible focus indicators
5. IF a background image fails to load, THEN THE Hero_Section SHALL display the dark background color (#0A0A0F) through the overlay gradient, maintaining text readability without visual breakage
6. THE Hero_Section SHALL display a stats bar below the main content area containing 4 stat items, each with a gold-colored icon and a bold numeric value with descriptive label

### Requirement 5: Stats Section

**User Story:** As a website visitor, I want to see key company metrics at a glance, so that I can quickly assess Grapadi's experience and scale.

#### Acceptance Criteria

1. THE Stats_Section SHALL display statistics in a 4-column grid layout on viewports 768px and wider
2. WHILE the viewport width is below 768px, THE Stats_Section SHALL display statistics in a 2-column grid
3. THE Stats_Section SHALL render each statistic with a gold-colored Material Icon, a bold number at minimum 24px font size, and a label describing the metric
4. THE Stats_Section SHALL render each stat item with a dark surface background (#12121A) or transparent background and a 1px solid border using the border-dark color token (#1E1E2A)
5. IF a stat item is missing a required field (icon, number, or label), THEN THE Stats_Section SHALL omit that stat item from the grid without producing a rendering error

### Requirement 6: CTA Banner

**User Story:** As a website visitor, I want a prominent mid-page prompt to take action, so that I am encouraged to engage with Grapadi's services.

#### Acceptance Criteria

1. THE CTA_Banner SHALL display a dark card (using the surface-dark background token) with a 1px gold/amber border (using the primary color token at reduced opacity) and centered content with vertical padding of at least 48px
2. THE CTA_Banner SHALL display a centered title using the display font with gold-colored text (primary color token), limited to a maximum of 80 characters
3. THE CTA_Banner SHALL display a single gold-filled CTA button (primary color background with dark text) that contains a text label and navigates to a specified URL when activated
4. THE CTA_Banner SHALL render its content in a single-column centered layout on viewports from 320px to 2560px wide without horizontal overflow
5. WHEN the CTA button receives keyboard focus, THE CTA_Banner SHALL display a visible focus indicator around the button that meets a minimum contrast ratio of 3:1 against adjacent colors

### Requirement 7: FAQ Section

**User Story:** As a website visitor, I want to find answers to common questions quickly, so that I can learn about Grapadi's services without contacting support.

#### Acceptance Criteria

1. WHILE the viewport width is 768px or greater, THE FAQ_Section SHALL display FAQ items in a 2-column grid layout
2. WHILE the viewport width is below 768px, THE FAQ_Section SHALL display FAQ items in a single-column layout
3. WHEN the page initially loads, THE FAQ_Section SHALL render all FAQ answers in a collapsed (hidden) state
4. WHEN a user clicks or activates a FAQ question, THE FAQ_Section SHALL toggle the visibility of the associated answer independently of other FAQ items
5. THE FAQ_Section SHALL style each item as a dark card using the surface-dark background with a border-dark border and a gold primary-colored expand/collapse icon
6. THE FAQ_Section SHALL render each toggle as a keyboard-accessible control that responds to Enter and Space keys, includes role="button", aria-expanded set to true or false reflecting current state, and aria-controls referencing the associated answer element's ID

### Requirement 8: Service Card

**User Story:** As a website visitor, I want to browse available services in an attractive card format, so that I can understand what Grapadi offers.

#### Acceptance Criteria

1. THE Service_Card SHALL display a dark surface background (surface-dark) with a default border color (border-dark) and rounded corners
2. THE Service_Card SHALL display a gold-colored Material Icon, white title text, gray description text using the muted text color token, and a gold "Explore" link with a trailing arrow icon
3. WHEN a user hovers over the Service_Card, THE Service_Card SHALL transition the border color to gold at 30% opacity with a transition duration of no more than 300 milliseconds
4. WHEN the Service_Card link receives keyboard focus, THE Service_Card SHALL display a visible focus indicator meeting WCAG_AA requirements
5. IF the Service_Card is rendered with an empty or null description prop, THEN THE Service_Card SHALL render the remaining elements (icon, title, and link) without visual breakage

### Requirement 9: Footer

**User Story:** As a website visitor, I want access to site links and contact information at the bottom of every page, so that I can navigate or reach out from anywhere on the site.

#### Acceptance Criteria

1. THE Footer SHALL display a background using the background-dark color token, with the company logo and company name positioned in the top-left area of the footer
2. THE Footer SHALL display three link columns labeled Menu, Layanan, and Kontak, with each column containing at least 2 navigation links rendered in muted text color
3. THE Footer SHALL display a row of social media icons including at minimum LinkedIn and Instagram, where each icon links to the corresponding external profile URL
4. THE Footer SHALL display a copyright text bar at the bottom containing the company name and the current year
5. WHEN a user hovers over a footer link, THE Footer SHALL apply the primary gold color (#C9A84C) to the hovered link text
6. IF the site settings for company logo or company name are unavailable, THEN THE Footer SHALL render using predefined fallback values without producing a visual error or layout breakage
7. THE Footer SHALL be rendered on every page of the site as part of the base layout
8. WHEN a footer link receives keyboard focus, THE Footer SHALL display a visible focus indicator on that link

### Requirement 10: Accessibility and Contrast

**User Story:** As a website visitor with varying visual abilities, I want all text to be legible against dark backgrounds, so that I can read content comfortably.

#### Acceptance Criteria

1. THE Theme_System SHALL ensure all text and background color combinations meet WCAG_AA minimum contrast ratio of 4.5:1 for normal text (below 18pt regular or 14pt bold) and 3:1 for large text (18pt or above regular, 14pt or above bold)
2. THE Theme_System SHALL ensure gold text (#C9A84C) on both dark backgrounds (#0A0A0F and #12121A) achieves at least 7:1 contrast ratio
3. THE Theme_System SHALL ensure muted text (#9CA3AF) on both dark backgrounds (#0A0A0F and #12121A) achieves at least 7:1 contrast ratio
4. WHEN interactive elements (links, buttons, form inputs, and custom interactive components) receive keyboard focus, THE Theme_System SHALL display a focus indicator with at least 3:1 contrast ratio against adjacent colors and a minimum thickness of 2px

### Requirement 11: Responsive Layout

**User Story:** As a website visitor on any device, I want the site to adapt gracefully to my screen size, so that I have a good experience on mobile, tablet, and desktop.

#### Acceptance Criteria

1. WHILE the viewport width is between 320px and 2560px, THE Theme_System SHALL render all page content without a horizontal scrollbar and without any content being clipped or hidden beyond the viewport edge
2. THE Theme_System SHALL apply base styles targeting a 320px minimum viewport width, with layout enhancements added at breakpoints of 640px (sm), 768px (md), 1024px (lg), 1280px (xl), and 1536px (2xl)
3. WHEN the viewport width crosses the 768px breakpoint, THE Theme_System SHALL transition multi-column grids from single-column (below 768px) to multi-column layout (768px and above), and SHALL scale heading font sizes by at least 20% larger at 768px and above compared to below 768px
4. WHILE the viewport width is 1536px or greater, THE Theme_System SHALL constrain the main content area to a maximum width of 1536px centered horizontally, preventing text lines from exceeding readable length
5. WHILE the viewport width is below 768px, THE Theme_System SHALL render all interactive elements (buttons, links, toggles) with a minimum touch target size of 44x44 CSS pixels

### Requirement 12: Home Page Composition

**User Story:** As a website visitor, I want a well-structured landing page that guides me through Grapadi's value proposition, so that I understand the company's offerings in a logical flow.

#### Acceptance Criteria

1. THE Home Page SHALL render sections in the following DOM order: Hero_Section, Stats_Section, Client Logos section (titled "Trusted By"), CTA_Banner, Services cards, FAQ_Section, CTA_Banner (final instance), Footer
2. THE Home Page SHALL apply uniform vertical padding of 80px (5rem) between each section on viewports at or above the desktop breakpoint (1024px), and 48px (3rem) on viewports below the desktop breakpoint
3. IF a section's data source returns empty or null (e.g., no services, no FAQ items, no client logos), THEN THE Home Page SHALL hide that section entirely without rendering an empty container or causing layout gaps between the remaining visible sections
4. THE Home Page SHALL render the Navbar via the site layout independently of section content, positioned fixed at the top and not counted in the section order

### Requirement 13: Error Resilience

**User Story:** As a website visitor, I want the site to handle loading failures gracefully, so that I always see usable content even when resources fail.

#### Acceptance Criteria

1. IF Google Fonts fail to load, THEN THE Theme_System SHALL render text immediately using the CSS font-stack fallbacks (system serif for display font, system sans-serif for body font) by applying font-display: swap, ensuring no period of invisible text occurs
2. IF the hero background image fails to load, THEN THE Hero_Section SHALL display the dark background color (#0A0A0F) with the gradient overlay preserved, keeping headline text and CTA buttons fully visible and readable
3. IF Alpine.js fails to load, THEN THE FAQ_Section SHALL display all question-and-answer pairs in a fully expanded visible state so that all content is accessible without JavaScript interaction
4. IF Alpine.js fails to load, THEN THE Navbar SHALL display all navigation links accessible via a visible fallback mechanism (such as noscript-styled navigation or initially-visible mobile menu links) so that users can navigate the site without JavaScript
5. IF site settings return null values, THEN THE affected components SHALL render using the default fallback value specified in each site_setting() call (e.g., 'Grapadi' for company name, 'info@grapadi.com' for email) without producing rendering errors, missing content blocks, or broken image elements
