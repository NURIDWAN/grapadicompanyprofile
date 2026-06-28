<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Theme token contrast compliance and dark-only consistency tests.
 *
 * **Validates: Requirements 1.6, 10.1, 10.2, 10.3**
 *
 * Property 7: Color contrast compliance
 * Property 1: Dark-only consistency
 */
class ThemeContrastTest extends TestCase
{
    // Theme color constants
    private const COLOR_GOLD = '#C9A84C';
    private const COLOR_MUTED_GRAY = '#9CA3AF';
    private const COLOR_WHITE = '#FFFFFF';
    private const COLOR_PAGE_BACKGROUND = '#0A0A0F';
    private const COLOR_SURFACE_DARK = '#12121A';

    private const MINIMUM_CONTRAST_RATIO = 7.0;

    /**
     * Convert a hex color to its linearized sRGB components.
     *
     * Uses the WCAG 2.1 relative luminance formula:
     * - Convert hex to 0-1 range
     * - Linearize: if value <= 0.04045, divide by 12.92; else ((value + 0.055) / 1.055) ^ 2.4
     */
    private function hexToLinearRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $r = $r <= 0.04045 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.04045 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.04045 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);

        return [$r, $g, $b];
    }

    /**
     * Calculate relative luminance per WCAG 2.1.
     * L = 0.2126 * R + 0.7152 * G + 0.0722 * B
     */
    private function relativeLuminance(string $hex): float
    {
        [$r, $g, $b] = $this->hexToLinearRgb($hex);

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /**
     * Calculate the WCAG 2.1 contrast ratio between two colors.
     * Contrast = (L1 + 0.05) / (L2 + 0.05) where L1 > L2
     */
    private function contrastRatio(string $foreground, string $background): float
    {
        $l1 = $this->relativeLuminance($foreground);
        $l2 = $this->relativeLuminance($background);

        if ($l2 > $l1) {
            [$l1, $l2] = [$l2, $l1];
        }

        return ($l1 + 0.05) / ($l2 + 0.05);
    }

    // =========================================================================
    // Property 7: Color contrast compliance
    // =========================================================================

    /**
     * Gold text (#C9A84C) on page background (#0A0A0F) meets ≥7:1 contrast ratio.
     */
    public function test_gold_on_page_background_meets_contrast_requirement(): void
    {
        $ratio = $this->contrastRatio(self::COLOR_GOLD, self::COLOR_PAGE_BACKGROUND);

        $this->assertGreaterThanOrEqual(
            self::MINIMUM_CONTRAST_RATIO,
            $ratio,
            sprintf(
                'Gold (#C9A84C) on page background (#0A0A0F) contrast ratio is %.2f:1, expected ≥%.1f:1',
                $ratio,
                self::MINIMUM_CONTRAST_RATIO
            )
        );
    }

    /**
     * Gold text (#C9A84C) on surface dark (#12121A) meets ≥7:1 contrast ratio.
     */
    public function test_gold_on_surface_dark_meets_contrast_requirement(): void
    {
        $ratio = $this->contrastRatio(self::COLOR_GOLD, self::COLOR_SURFACE_DARK);

        $this->assertGreaterThanOrEqual(
            self::MINIMUM_CONTRAST_RATIO,
            $ratio,
            sprintf(
                'Gold (#C9A84C) on surface dark (#12121A) contrast ratio is %.2f:1, expected ≥%.1f:1',
                $ratio,
                self::MINIMUM_CONTRAST_RATIO
            )
        );
    }

    /**
     * Muted gray text (#9CA3AF) on page background (#0A0A0F) meets ≥7:1 contrast ratio.
     */
    public function test_muted_gray_on_page_background_meets_contrast_requirement(): void
    {
        $ratio = $this->contrastRatio(self::COLOR_MUTED_GRAY, self::COLOR_PAGE_BACKGROUND);

        $this->assertGreaterThanOrEqual(
            self::MINIMUM_CONTRAST_RATIO,
            $ratio,
            sprintf(
                'Muted gray (#9CA3AF) on page background (#0A0A0F) contrast ratio is %.2f:1, expected ≥%.1f:1',
                $ratio,
                self::MINIMUM_CONTRAST_RATIO
            )
        );
    }

    /**
     * Muted gray text (#9CA3AF) on surface dark (#12121A) meets ≥7:1 contrast ratio.
     */
    public function test_muted_gray_on_surface_dark_meets_contrast_requirement(): void
    {
        $ratio = $this->contrastRatio(self::COLOR_MUTED_GRAY, self::COLOR_SURFACE_DARK);

        $this->assertGreaterThanOrEqual(
            self::MINIMUM_CONTRAST_RATIO,
            $ratio,
            sprintf(
                'Muted gray (#9CA3AF) on surface dark (#12121A) contrast ratio is %.2f:1, expected ≥%.1f:1',
                $ratio,
                self::MINIMUM_CONTRAST_RATIO
            )
        );
    }

    /**
     * White text (#FFFFFF) on page background (#0A0A0F) meets ≥7:1 contrast ratio.
     */
    public function test_white_on_page_background_meets_contrast_requirement(): void
    {
        $ratio = $this->contrastRatio(self::COLOR_WHITE, self::COLOR_PAGE_BACKGROUND);

        $this->assertGreaterThanOrEqual(
            self::MINIMUM_CONTRAST_RATIO,
            $ratio,
            sprintf(
                'White (#FFFFFF) on page background (#0A0A0F) contrast ratio is %.2f:1, expected ≥%.1f:1',
                $ratio,
                self::MINIMUM_CONTRAST_RATIO
            )
        );
    }

    /**
     * White text (#FFFFFF) on surface dark (#12121A) meets ≥7:1 contrast ratio.
     */
    public function test_white_on_surface_dark_meets_contrast_requirement(): void
    {
        $ratio = $this->contrastRatio(self::COLOR_WHITE, self::COLOR_SURFACE_DARK);

        $this->assertGreaterThanOrEqual(
            self::MINIMUM_CONTRAST_RATIO,
            $ratio,
            sprintf(
                'White (#FFFFFF) on surface dark (#12121A) contrast ratio is %.2f:1, expected ≥%.1f:1',
                $ratio,
                self::MINIMUM_CONTRAST_RATIO
            )
        );
    }

    // =========================================================================
    // Property 1: Dark-only consistency
    // =========================================================================

    /**
     * The app.css file does not contain light-mode tokens.
     */
    public function test_app_css_has_no_light_mode_tokens(): void
    {
        $cssPath = resource_path('css/app.css');
        $this->assertFileExists($cssPath);

        $cssContent = file_get_contents($cssPath);

        $this->assertStringNotContainsString(
            '--color-background-light',
            $cssContent,
            'app.css should not contain --color-background-light token'
        );

        $this->assertStringNotContainsString(
            '--color-surface-light',
            $cssContent,
            'app.css should not contain --color-surface-light token'
        );
    }

    /**
     * The app.css file does not contain @custom-variant dark directive.
     */
    public function test_app_css_has_no_custom_variant_dark(): void
    {
        $cssPath = resource_path('css/app.css');
        $cssContent = file_get_contents($cssPath);

        $this->assertDoesNotMatchRegularExpression(
            '/@custom-variant\s+dark/',
            $cssContent,
            'app.css should not contain @custom-variant dark directive'
        );
    }

    /**
     * The HTML root element always has the "dark" class.
     * Verifies the layout template unconditionally sets class="dark" on <html>.
     */
    public function test_html_root_has_dark_class(): void
    {
        $layoutPath = resource_path('views/layouts/app.blade.php');
        $this->assertFileExists($layoutPath);

        $layoutContent = file_get_contents($layoutPath);

        // The <html> tag must contain class="dark"
        $this->assertStringContainsString(
            'class="dark"',
            $layoutContent,
            'Layout app.blade.php must have class="dark" on the HTML element'
        );

        // Verify the <html> line specifically has the dark class
        $lines = explode("\n", $layoutContent);
        $htmlLine = '';
        foreach ($lines as $line) {
            if (str_contains($line, '<html')) {
                $htmlLine = $line;
                break;
            }
        }

        $this->assertNotEmpty($htmlLine, 'Layout must contain an <html> element');
        $this->assertStringContainsString(
            'dark',
            $htmlLine,
            'The <html> element must have the "dark" class'
        );

        // Verify the dark class is NOT conditional (not wrapped in blade directives)
        $this->assertDoesNotMatchRegularExpression(
            '/@if.*dark.*class/s',
            $layoutContent,
            'The "dark" class should not be conditionally applied'
        );
    }
}
