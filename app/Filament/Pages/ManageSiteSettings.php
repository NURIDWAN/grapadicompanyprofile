<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class ManageSiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.manage-site-settings';
    protected static ?string $title = 'Site Settings';
    protected static ?string $navigationLabel = 'Site Settings';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 0;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // General
            'company_name' => SiteSetting::get('site_company_name', 'Grapadi'),
            'tagline' => SiteSetting::get('site_tagline', 'Market Intelligence & Consulting'),
            'email' => SiteSetting::get('site_email', 'info@grapadi.com'),
            'phone' => SiteSetting::get('site_phone', ''),
            'whatsapp' => SiteSetting::get('site_whatsapp', ''),
            'address' => SiteSetting::get('site_address', ''),
            
            // Logo & Branding
            'logo' => SiteSetting::get('site_logo', ''),
            'logo_dark' => SiteSetting::get('site_logo_dark', ''),
            'logo_only' => SiteSetting::get('site_logo_only', false),
            'favicon' => SiteSetting::get('site_favicon', ''),
            
            // Social Media
            'social_facebook' => SiteSetting::get('social_facebook', ''),
            'social_twitter' => SiteSetting::get('social_twitter', ''),
            'social_linkedin' => SiteSetting::get('social_linkedin', ''),
            'social_instagram' => SiteSetting::get('social_instagram', ''),
            'social_youtube' => SiteSetting::get('social_youtube', ''),
            'social_tiktok' => SiteSetting::get('social_tiktok', ''),
            
            // Footer
            'footer_copyright' => SiteSetting::get('footer_copyright', '© {year} {company}. All rights reserved.'),
            'footer_description' => SiteSetting::get('footer_description', ''),
            'footer_newsletter_title' => SiteSetting::get('footer_newsletter_title', 'Newsletter'),
            'footer_newsletter_description' => SiteSetting::get('footer_newsletter_description', 'Subscribe to our Newsletter. Never miss out on an update from us.'),
            
            // Software Bisnis Plan (deprecated - kept for data preservation)
            'sbp_is_active' => SiteSetting::get('sbp_is_active', false),
            
            // Hero Section
            'hero_title' => SiteSetting::get('hero_title', 'Grapadi Konsultan Indonesia'),
            'hero_subtitle' => SiteSetting::get('hero_subtitle', 'Where Data Meets Insight'),
            'hero_cta_text' => SiteSetting::get('hero_cta_text', 'Book a Discovery Call'),
            'hero_cta_url' => SiteSetting::get('hero_cta_url', '/contact'),
            'hero_show_logo' => SiteSetting::get('hero_show_logo', true),
            'hero_stats' => $this->decodeJsonStats(SiteSetting::get('hero_stats', null)),
            
            // Director Section
            'director_title' => SiteSetting::get('director_title', 'Director'),
            'director_name' => SiteSetting::get('director_name', 'Muhammad Dwi Andika, SE, M.Ec.Dev, CA, CWA'),
            'director_description' => SiteSetting::get('director_description', 'Dengan pengalaman 17+ tahun, Andika membantu klien mengatasi hambatan bisnis, memahami pasar, dan mengambil keputusan strategis berbasis data. Banyak perusahaan di Asia Tenggara telah merasakan dampak langsung dari pendekatan konsultasi yang praktis dan menyeluruh yang ia pimpin.'),
            'director_image' => SiteSetting::get('director_image', 'image/about/person/image.png'),
            'director_linkedin' => SiteSetting::get('director_linkedin', ''),

            // About Page - Hero Section
            'about_hero_tagline' => SiteSetting::get('about_hero_tagline', 'About Us'),
            'about_hero_title' => SiteSetting::get('about_hero_title', 'Strategic Insights, Digital Advantage'),
            'about_hero_description' => SiteSetting::get('about_hero_description', 'Grapadi adalah perusahaan Business Advisory dan Riset Strategis yang mendampingi pengambilan keputusan bisnis dan investasi melalui studi kelayakan, business plan, dan perencanaan strategis berbasis riset. Berpengalaman puluhan tahun, Grapadi melayani berbagai sektor industri sebagai mitra strategis bisnis dan institusi.'),
            'about_hero_image' => SiteSetting::get('about_hero_image', 'image/about/company/image.png'),

            // About Page - Leadership Team Section
            'about_team_title' => SiteSetting::get('about_team_title', 'Leadership Team'),
            'about_team_subtitle' => SiteSetting::get('about_team_subtitle', 'Meet the minds behind our strategic insights.'),

            // About Page - CTA Section
            'about_cta_title' => SiteSetting::get('about_cta_title', 'Ready to work with us?'),
            'about_cta_description' => SiteSetting::get('about_cta_description', "Whether you're looking for insights to grow your business or a career to grow your potential, we want to hear from you."),
            'about_cta_primary_text' => SiteSetting::get('about_cta_primary_text', 'Join Our Team'),
            'about_cta_primary_url' => SiteSetting::get('about_cta_primary_url', '/careers'),
            'about_cta_secondary_text' => SiteSetting::get('about_cta_secondary_text', 'Contact Us'),
            'about_cta_secondary_url' => SiteSetting::get('about_cta_secondary_url', '/contact'),

            // About Page - What We Do Section
            'about_what_we_do_title' => SiteSetting::get('about_what_we_do_title', 'What We Do?'),
            'about_what_we_do_description' => SiteSetting::get('about_what_we_do_description', 'We provide technology-enabled business consulting services with a data-driven approach and digital tools, supported by long and proven experience across industries, to strengthen business processes, corporate governance, and sustainable decision-making toward better performance.'),
            'about_what_we_do_items' => $this->decodeJsonItems(SiteSetting::get('about_what_we_do_items', null), 'what_we_do'),

            // About Page - Vision Section
            'about_vision_title' => SiteSetting::get('about_vision_title', 'Vision'),
            'about_vision_description' => SiteSetting::get('about_vision_description', 'Menjadi mitra konsultan manajemen yang memadukan keunggulan digital dan jaringan global ITIALUS untuk merancang solusi bisnis yang strategis, berkelanjutan, dan berstandar internasional.'),

            // About Page - Mission Section
            'about_mission_title' => SiteSetting::get('about_mission_title', 'Mission'),
            'about_mission_items' => $this->decodeJsonItems(SiteSetting::get('about_mission_items', null), 'mission'),

            // About Page - Solutions Section
            'about_solutions_title' => SiteSetting::get('about_solutions_title', 'Solutions'),
            'about_solutions_description' => SiteSetting::get('about_solutions_description', 'Di Grapadi International, kami menyediakan layanan konsultasi bisnis yang komprehensif untuk menjawab kebutuhan perusahaan di pasar nasional maupun internasional. Layanan kami mencakup perencanaan strategis, analisis investasi, pengembangan bisnis, hingga implementasi solusi yang berorientasi pada hasil, keberlanjutan, dan penciptaan nilai jangka panjang.'),
            'about_solutions_items' => $this->decodeJsonItems(SiteSetting::get('about_solutions_items', null), 'solutions'),

            // Services Page - Hero Section
            'svc_hero_title' => SiteSetting::get('svc_hero_title', 'Strategic Intelligence for Global Markets'),
            'svc_hero_subtitle' => SiteSetting::get('svc_hero_subtitle', 'Strategic Intelligence for'),
            'svc_hero_highlight' => SiteSetting::get('svc_hero_highlight', 'Global Markets'),
            'svc_hero_description' => SiteSetting::get('svc_hero_description', 'Data-driven insights that empower executive decision-making and drive sustainable growth in an evolving landscape.'),
            'svc_hero_primary_text' => SiteSetting::get('svc_hero_primary_text', 'View Services'),
            'svc_hero_primary_url' => SiteSetting::get('svc_hero_primary_url', '#services'),
            'svc_hero_secondary_text' => SiteSetting::get('svc_hero_secondary_text', 'Request Proposal'),
            'svc_hero_secondary_url' => SiteSetting::get('svc_hero_secondary_url', '/contact'),

            // Services Page - Expertise Section
            'svc_expertise_tagline' => SiteSetting::get('svc_expertise_tagline', 'Our Expertise'),
            'svc_expertise_headline' => SiteSetting::get('svc_expertise_headline', 'Comprehensive solutions tailored to your unique business challenges'),

            // Services Page - Dashboard Section
            'svc_dashboard_title' => SiteSetting::get('svc_dashboard_title', 'Integrated Strategy Platform & Decision Dashboard'),
            'svc_dashboard_description' => SiteSetting::get('svc_dashboard_description', 'Platform terintegrasi untuk memantau kinerja bisnis dan mengambil keputusan strategis'),

            // Services Page - CTA Section
            'svc_cta_title' => SiteSetting::get('svc_cta_title', 'Ready to elevate your strategy?'),
            'svc_cta_description' => SiteSetting::get('svc_cta_description', 'Schedule a consultation with our senior partners to discuss your specific needs and how we can help you achieve your goals.'),
            'svc_cta_primary_text' => SiteSetting::get('svc_cta_primary_text', 'Request a Proposal'),
            'svc_cta_primary_url' => SiteSetting::get('svc_cta_primary_url', '/contact'),

            // Services Page - Grapadi Strategix Section
            'strategix_is_active' => SiteSetting::get('strategix_is_active', true),
            'strategix_logo' => SiteSetting::get('strategix_logo', ''),
            'strategix_title' => SiteSetting::get('strategix_title', 'Grapadi Strategix'),
            'strategix_description' => SiteSetting::get('strategix_description', 'Grapadi Strategix is a flagship product of Grapadi Konsultan, focusing on data- and technology-driven strategic consulting services. Designed to support informed and effective decision-making, Grapadi Strategix helps organizations enhance business process effectiveness, strengthen corporate governance, and achieve sustainable long-term performance.'),
            'strategix_cta_text' => SiteSetting::get('strategix_cta_text', 'Coba Gratis Sekarang'),
            'strategix_cta_url' => SiteSetting::get('strategix_cta_url', 'https://strategix.grapadi.com'),
            'strategix_dashboards' => $this->decodeJsonItems(SiteSetting::get('strategix_dashboards', null), 'dashboards'),
            'strategix_pricing_title' => SiteSetting::get('strategix_pricing_title', 'System Features & Consulting Services'),
            'strategix_pricing_plans' => $this->decodeJsonItems(SiteSetting::get('strategix_pricing_plans', null), 'pricing'),

            // Services Page - Platform Access Steps
            'platform_steps_title' => SiteSetting::get('platform_steps_title', 'Grapadi Strategix Platform Access & Dashboard'),
            'platform_steps' => $this->decodeJsonItems(SiteSetting::get('platform_steps', null), 'steps'),

            // SEO & Titles
            'page_title_home' => SiteSetting::get('page_title_home', 'Home'),
            'page_title_about' => SiteSetting::get('page_title_about', 'About Us'),
            'page_title_services' => SiteSetting::get('page_title_services', 'Our Services'),
            'page_title_portfolio' => SiteSetting::get('page_title_portfolio', 'Portfolio'),
            'page_title_blog' => SiteSetting::get('page_title_blog', 'Blog'),
            'page_title_contact' => SiteSetting::get('page_title_contact', 'Contact Us'),
            'page_title_timeline' => SiteSetting::get('page_title_timeline', 'Company Timeline'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        // Tab 1: General
                        Forms\Components\Tabs\Tab::make('General')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\Section::make('Informasi Perusahaan')
                                    ->description('Pengaturan identitas dasar perusahaan')
                                    ->schema([
                                        Forms\Components\TextInput::make('company_name')
                                            ->label('Nama Perusahaan')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Grapadi'),

                                        Forms\Components\TextInput::make('tagline')
                                            ->label('Tagline / Slogan')
                                            ->maxLength(255)
                                            ->placeholder('Market Intelligence & Consulting'),

                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('email')
                                                ->label('Email Kontak')
                                                ->email()
                                                ->placeholder('info@grapadi.com'),

                                            Forms\Components\TextInput::make('phone')
                                                ->label('Nomor Telepon')
                                                ->tel()
                                                ->placeholder('+62 21 1234567'),
                                        ]),

                                        Forms\Components\TextInput::make('whatsapp')
                                            ->label('Nomor WhatsApp')
                                            ->placeholder('6281234567890')
                                            ->helperText('Format: 628xxx tanpa + atau spasi'),

                                        Forms\Components\Textarea::make('address')
                                            ->label('Alamat Lengkap')
                                            ->rows(3)
                                            ->placeholder('Jl. Contoh No. 123, Jakarta'),
                                    ]),
                                    ]),

                        
                        // Tab: SEO & Titles
                        Forms\Components\Tabs\Tab::make('SEO & Titles')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Section::make('Page Titles')
                                    ->description('Judul halaman yang akan muncul di tab browser')
                                    ->schema([
                                        Forms\Components\TextInput::make('page_title_home')
                                            ->label('Home Page Title')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('page_title_about')
                                            ->label('About Page Title')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('page_title_services')
                                            ->label('Services Page Title')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('page_title_portfolio')
                                            ->label('Portfolio Page Title')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('page_title_blog')
                                            ->label('Blog Page Title')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('page_title_timeline')
                                            ->label('Timeline Page Title')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('page_title_contact')
                                            ->label('Contact Page Title')
                                            ->required()
                                            ->maxLength(255),
                                    ])->columns(2),
                            ]),

                        // Tab 2: Logo & Branding
                        Forms\Components\Tabs\Tab::make('Logo & Branding')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make('Logo')
                                    ->description('Upload logo perusahaan')
                                    ->schema([
                                        Forms\Components\FileUpload::make('logo')
                                            ->label('Logo Utama')
                                            ->image()
                                            ->directory('site')
                                            ->visibility('public')
                                            ->helperText('Logo untuk navbar dan tampilan umum. Disarankan PNG dengan background transparan.'),

                                        Forms\Components\FileUpload::make('logo_dark')
                                            ->label('Logo untuk Dark Mode (Optional)')
                                            ->image()
                                            ->directory('site')
                                            ->visibility('public')
                                            ->helperText('Logo alternatif untuk dark mode. Jika kosong, akan menggunakan logo utama.'),

                                        Forms\Components\Toggle::make('logo_only')
                                            ->label('Tampilkan Logo Saja di Navbar')
                                            ->helperText('Jika aktif, navbar hanya menampilkan logo tanpa nama perusahaan.')
                                            ->default(false),
                                    ]),

                                Forms\Components\Section::make('Favicon')
                                    ->description('Icon yang muncul di tab browser')
                                    ->schema([
                                        Forms\Components\FileUpload::make('favicon')
                                            ->label('Favicon')
                                            ->image()
                                            ->directory('site')
                                            ->visibility('public')
                                            ->helperText('Ukuran ideal: 32x32 atau 64x64 pixels. Format: PNG, ICO.'),
                                    ]),
                            ]),

                        // Tab 3: Social Media
                        Forms\Components\Tabs\Tab::make('Social Media')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\Section::make('Link Social Media')
                                    ->description('URL akun social media perusahaan. Kosongkan jika tidak ada.')
                                    ->schema([
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('social_facebook')
                                                ->label('Facebook')
                                                ->url()
                                                ->placeholder('https://facebook.com/grapadi')
                                                ->prefixIcon('heroicon-o-link'),

                                            Forms\Components\TextInput::make('social_twitter')
                                                ->label('Twitter / X')
                                                ->url()
                                                ->placeholder('https://twitter.com/grapadi')
                                                ->prefixIcon('heroicon-o-link'),

                                            Forms\Components\TextInput::make('social_linkedin')
                                                ->label('LinkedIn')
                                                ->url()
                                                ->placeholder('https://linkedin.com/company/grapadi')
                                                ->prefixIcon('heroicon-o-link'),

                                            Forms\Components\TextInput::make('social_instagram')
                                                ->label('Instagram')
                                                ->url()
                                                ->placeholder('https://instagram.com/grapadi')
                                                ->prefixIcon('heroicon-o-link'),

                                            Forms\Components\TextInput::make('social_youtube')
                                                ->label('YouTube')
                                                ->url()
                                                ->placeholder('https://youtube.com/@grapadi')
                                                ->prefixIcon('heroicon-o-link'),

                                            Forms\Components\TextInput::make('social_tiktok')
                                                ->label('TikTok')
                                                ->url()
                                                ->placeholder('https://tiktok.com/@grapadi')
                                                ->prefixIcon('heroicon-o-link'),
                                        ]),
                                    ]),
                            ]),

                        // Tab 4: Footer
                        Forms\Components\Tabs\Tab::make('Footer')
                            ->icon('heroicon-o-bars-3-bottom-left')
                            ->schema([
                                Forms\Components\Section::make('Konten Footer')
                                    ->description('Pengaturan teks dan konten footer')
                                    ->schema([
                                        Forms\Components\Textarea::make('footer_description')
                                            ->label('Deskripsi Perusahaan')
                                            ->rows(3)
                                            ->placeholder('Deskripsi singkat tentang perusahaan untuk ditampilkan di footer'),

                                        Forms\Components\TextInput::make('footer_copyright')
                                            ->label('Teks Copyright')
                                            ->placeholder('© {year} {company}. All rights reserved.')
                                            ->helperText('Gunakan {year} untuk tahun otomatis, {company} untuk nama perusahaan'),
                                    ]),

                                Forms\Components\Section::make('Newsletter')
                                    ->description('Pengaturan section newsletter di footer')
                                    ->schema([
                                        Forms\Components\TextInput::make('footer_newsletter_title')
                                            ->label('Judul Newsletter')
                                            ->placeholder('Newsletter'),

                                        Forms\Components\Textarea::make('footer_newsletter_description')
                                            ->label('Deskripsi Newsletter')
                                            ->rows(2)
                                            ->placeholder('Subscribe to our Newsletter. Never miss out on an update from us.'),
                                    ]),
                            ]),

                        // Tab 5: Homepage Sections
                        Forms\Components\Tabs\Tab::make('Homepage Sections')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\Placeholder::make('homepage_info')
                                    ->content('Homepage terdiri dari 5 section: Hero, Trusted By, Services, FAQ, dan Final CTA. Trusted By dikelola melalui menu Brands. FAQ dikelola melalui menu FAQ. Services ditampilkan otomatis dari data layanan.')
                                    ->columnSpanFull(),

                                // Hero Section
                                Forms\Components\Section::make('Hero Section')
                                    ->description('Pengaturan section hero di homepage')
                                    ->schema([
                                        Forms\Components\TextInput::make('hero_title')
                                            ->label('Judul Hero')
                                            ->placeholder('Grapadi Konsultan Indonesia')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('hero_subtitle')
                                            ->label('Subtitle Hero')
                                            ->placeholder('Where Data Meets Insight')
                                            ->maxLength(255),

                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('hero_cta_text')
                                                ->label('Teks Tombol CTA')
                                                ->placeholder('Book a Discovery Call'),

                                            Forms\Components\TextInput::make('hero_cta_url')
                                                ->label('URL Tombol CTA')
                                                ->placeholder('/contact'),
                                        ]),

                                        Forms\Components\Toggle::make('hero_show_logo')
                                            ->label('Tampilkan Logo di Hero')
                                            ->default(true),

                                        Forms\Components\Repeater::make('hero_stats')
                                            ->label('Statistik Hero (Kartu di bawah CTA)')
                                            ->schema([
                                                Forms\Components\TextInput::make('icon')
                                                    ->label('Icon (Material Icons)')
                                                    ->placeholder('groups')
                                                    ->helperText('Nama icon dari Material Icons Outlined'),
                                                Forms\Components\TextInput::make('number')
                                                    ->label('Angka')
                                                    ->placeholder('18+'),
                                                Forms\Components\TextInput::make('label')
                                                    ->label('Label')
                                                    ->placeholder('Years Experience'),
                                                Forms\Components\TextInput::make('description')
                                                    ->label('Deskripsi')
                                                    ->placeholder('Pengalaman panjang...'),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(4)
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null),
                                    ])
                                    ->collapsible(),

                                // Director Section (used on About page, kept here for convenience)
                                Forms\Components\Section::make('Director Section')
                                    ->description('Profil direktur (ditampilkan di halaman About)')
                                    ->schema([
                                        Forms\Components\TextInput::make('director_title')
                                            ->label('Jabatan')
                                            ->placeholder('Director')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('director_name')
                                            ->label('Nama Lengkap')
                                            ->placeholder('Muhammad Dwi Andika, SE, M.Ec.Dev, CA, CWA')
                                            ->maxLength(255),

                                        Forms\Components\Textarea::make('director_description')
                                            ->label('Deskripsi')
                                            ->placeholder('Dengan pengalaman 17+ tahun...')
                                            ->rows(4),

                                        Forms\Components\FileUpload::make('director_image')
                                            ->label('Foto Direktur')
                                            ->image()
                                            ->directory('homepage')
                                            ->visibility('public'),

                                        Forms\Components\TextInput::make('director_linkedin')
                                            ->label('URL LinkedIn')
                                            ->url()
                                            ->placeholder('https://linkedin.com/in/username')
                                            ->prefixIcon('heroicon-o-link'),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),
                            ]),

                        // Tab 7: About Page
                        Forms\Components\Tabs\Tab::make('About Page')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                // About Hero Section
                                Forms\Components\Section::make('Hero Section')
                                    ->description('Pengaturan section hero di halaman About')
                                    ->schema([
                                        Forms\Components\TextInput::make('about_hero_tagline')
                                            ->label('Tagline')
                                            ->placeholder('About Us')
                                            ->maxLength(100),

                                        Forms\Components\TextInput::make('about_hero_title')
                                            ->label('Judul Hero')
                                            ->placeholder('Strategic Insights, Digital Advantage')
                                            ->maxLength(255),

                                        Forms\Components\Textarea::make('about_hero_description')
                                            ->label('Deskripsi')
                                            ->placeholder('Grapadi adalah perusahaan...')
                                            ->rows(4),

                                        Forms\Components\FileUpload::make('about_hero_image')
                                            ->label('Gambar Hero')
                                            ->image()
                                            ->directory('about')
                                            ->visibility('public'),
                                    ])
                                    ->collapsible(),

                                // Leadership Team Section
                                Forms\Components\Section::make('Leadership Team Section')
                                    ->description('Pengaturan section tim leadership')
                                    ->schema([
                                        Forms\Components\TextInput::make('about_team_title')
                                            ->label('Judul Section')
                                            ->placeholder('Leadership Team')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('about_team_subtitle')
                                            ->label('Subtitle')
                                            ->placeholder('Meet the minds behind our strategic insights.')
                                            ->maxLength(255),
                                    ])
                                    ->collapsible(),

                                // About CTA Section
                                Forms\Components\Section::make('CTA Section')
                                    ->description('Pengaturan section Call-to-Action di halaman About')
                                    ->schema([
                                        Forms\Components\TextInput::make('about_cta_title')
                                            ->label('Judul CTA')
                                            ->placeholder('Ready to work with us?')
                                            ->maxLength(255),

                                        Forms\Components\Textarea::make('about_cta_description')
                                            ->label('Deskripsi CTA')
                                            ->placeholder('Whether you\'re looking for insights...')
                                            ->rows(2),

                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('about_cta_primary_text')
                                                ->label('Teks Tombol Primary')
                                                ->placeholder('Join Our Team'),

                                            Forms\Components\TextInput::make('about_cta_primary_url')
                                                ->label('URL Tombol Primary')
                                                ->placeholder('/careers'),
                                        ]),

                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('about_cta_secondary_text')
                                                ->label('Teks Tombol Secondary')
                                                ->placeholder('Contact Us'),

                                            Forms\Components\TextInput::make('about_cta_secondary_url')
                                                ->label('URL Tombol Secondary')
                                                ->placeholder('/contact'),
                                        ]),
                                    ])
                                    ->collapsible(),

                                // What We Do Section
                                Forms\Components\Section::make('What We Do Section')
                                    ->description('Pengaturan section layanan/area kerja')
                                    ->schema([
                                        Forms\Components\TextInput::make('about_what_we_do_title')
                                            ->label('Judul Section')
                                            ->placeholder('What We Do?')
                                            ->maxLength(255),
                                        
                                        Forms\Components\Textarea::make('about_what_we_do_description')
                                            ->label('Deskripsi')
                                            ->rows(3),

                                        Forms\Components\Repeater::make('about_what_we_do_items')
                                            ->label('Daftar Layanan')
                                            ->schema([
                                                Forms\Components\TextInput::make('text')
                                                    ->label('Nama Layanan')
                                                    ->required(),
                                            ])
                                            ->grid(2)
                                            ->defaultItems(6),
                                    ])
                                    ->collapsible(),

                                // Vision & Mission Section
                                Forms\Components\Section::make('Vision & Mission Section')
                                    ->description('Pengaturan Visi dan Misi perusahaan')
                                    ->schema([
                                        Forms\Components\TextInput::make('about_vision_title')
                                            ->label('Judul Visi')
                                            ->default('Vision')
                                            ->maxLength(255),
                                        
                                        Forms\Components\Textarea::make('about_vision_description')
                                            ->label('Deskripsi Visi')
                                            ->rows(3),

                                        Forms\Components\TextInput::make('about_mission_title')
                                            ->label('Judul Misi')
                                            ->default('Mission')
                                            ->maxLength(255),

                                        Forms\Components\Repeater::make('about_mission_items')
                                            ->label('Daftar Misi')
                                            ->schema([
                                                Forms\Components\Textarea::make('text')
                                                    ->label('Poin Misi')
                                                    ->required()
                                                    ->rows(2),
                                            ])
                                            ->defaultItems(4),
                                    ])
                                    ->collapsible(),

                                // Solutions Section
                                Forms\Components\Section::make('Solutions Section')
                                    ->description('Pengaturan section solusi perusahaan')
                                    ->schema([
                                        Forms\Components\TextInput::make('about_solutions_title')
                                            ->label('Judul Section')
                                            ->default('Solutions')
                                            ->maxLength(255),
                                        
                                        Forms\Components\Textarea::make('about_solutions_description')
                                            ->label('Deskripsi')
                                            ->rows(3),

                                        Forms\Components\Repeater::make('about_solutions_items')
                                            ->label('Daftar Solusi')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Judul Solusi')
                                                    ->required(),
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Deskripsi Solusi')
                                                    ->rows(2),
                                            ])
                                            ->defaultItems(4),
                                    ])
                                    ->collapsible(),
                            ]),

                        // Tab 7: Services Page
                        Forms\Components\Tabs\Tab::make('Services Page')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                // Services Hero Section
                                Forms\Components\Section::make('Hero Section')
                                    ->description('Pengaturan section utama di halaman Services')
                                    ->schema([
                                        Forms\Components\TextInput::make('svc_hero_title')
                                            ->label('Judul Lengkap (Untuk SEO)')
                                            ->placeholder('Strategic Intelligence for Global Markets')
                                            ->maxLength(255),
                                            
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('svc_hero_subtitle')
                                                ->label('Teks Awal')
                                                ->placeholder('Strategic Intelligence for'),
                                                
                                            Forms\Components\TextInput::make('svc_hero_highlight')
                                                ->label('Teks Sorotan (Highlight)')
                                                ->placeholder('Global Markets'),
                                        ]),

                                        Forms\Components\Textarea::make('svc_hero_description')
                                            ->label('Deskripsi')
                                            ->rows(3),

                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('svc_hero_primary_text')
                                                ->label('Teks Tombol Primary')
                                                ->placeholder('View Services'),
                                            Forms\Components\TextInput::make('svc_hero_primary_url')
                                                ->label('URL Tombol Primary')
                                                ->placeholder('#services'),
                                        ]),

                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('svc_hero_secondary_text')
                                                ->label('Teks Tombol Secondary')
                                                ->placeholder('Request Proposal'),
                                            Forms\Components\TextInput::make('svc_hero_secondary_url')
                                                ->label('URL Tombol Secondary')
                                                ->placeholder('/contact'),
                                        ]),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),

                                // Services Expertise Section
                                Forms\Components\Section::make('Expertise Section')
                                    ->description('Pengaturan headline section expertise')
                                    ->schema([
                                        Forms\Components\TextInput::make('svc_expertise_tagline')
                                            ->label('Tagline')
                                            ->placeholder('Our Expertise')
                                            ->maxLength(255),
                                            
                                        Forms\Components\Textarea::make('svc_expertise_headline')
                                            ->label('Headline Utama')
                                            ->placeholder('Comprehensive solutions tailored to your unique business challenges')
                                            ->rows(2),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),

                                // Dashboard Title Section
                                Forms\Components\Section::make('Dashboard Title Section')
                                    ->description('Pengaturan judul untuk section daftar dashboard')
                                    ->schema([
                                        Forms\Components\TextInput::make('svc_dashboard_title')
                                            ->label('Judul Section')
                                            ->placeholder('Integrated Strategy Platform & Decision Dashboard')
                                            ->maxLength(255),
                                            
                                        Forms\Components\Textarea::make('svc_dashboard_description')
                                            ->label('Deskripsi Section')
                                            ->placeholder('Platform terintegrasi untuk memantau kinerja bisnis dan mengambil keputusan strategis')
                                            ->rows(2),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),

                                // CTA Section
                                Forms\Components\Section::make('Call to Action (CTA) Section')
                                    ->description('Pengaturan section CTA di bagian bawah')
                                    ->schema([
                                        Forms\Components\TextInput::make('svc_cta_title')
                                            ->label('Judul CTA')
                                            ->placeholder('Ready to elevate your strategy?')
                                            ->maxLength(255),

                                        Forms\Components\Textarea::make('svc_cta_description')
                                            ->label('Deskripsi CTA')
                                            ->rows(2),

                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('svc_cta_primary_text')
                                                ->label('Teks Tombol')
                                                ->placeholder('Request a Proposal'),

                                            Forms\Components\TextInput::make('svc_cta_primary_url')
                                                ->label('URL Tombol')
                                                ->placeholder('/contact'),
                                        ]),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),

                                // Grapadi Strategix Section
                                Forms\Components\Section::make('Grapadi Strategix')
                                    ->description('Pengaturan section produk Grapadi Strategix')
                                    ->schema([
                                        Forms\Components\Toggle::make('strategix_is_active')
                                            ->label('Aktifkan Section')
                                            ->default(true),

                                        Forms\Components\FileUpload::make('strategix_logo')
                                            ->label('Logo Produk')
                                            ->image()
                                            ->directory('strategix')
                                            ->maxSize(2048),

                                        Forms\Components\TextInput::make('strategix_title')
                                            ->label('Judul Produk')
                                            ->default('Grapadi Strategix')
                                            ->maxLength(255),

                                        Forms\Components\Textarea::make('strategix_description')
                                            ->label('Deskripsi Produk')
                                            ->rows(4),

                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('strategix_cta_text')
                                                ->label('Teks Tombol CTA')
                                                ->default('Coba Gratis Sekarang'),

                                            Forms\Components\TextInput::make('strategix_cta_url')
                                                ->label('URL Tombol CTA')
                                                ->url()
                                                ->default('https://strategix.grapadi.com'),
                                        ]),
                                    ])
                                    ->collapsible(),

                                // Dashboard Features Section
                                Forms\Components\Section::make('Dashboard Features')
                                    ->description('Daftar fitur dashboard yang ditampilkan')
                                    ->schema([
                                        Forms\Components\Repeater::make('strategix_dashboards')
                                            ->label('Dashboard')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Judul Dashboard')
                                                    ->required(),
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Deskripsi')
                                                    ->rows(2),
                                                Forms\Components\FileUpload::make('image')
                                                    ->label('Screenshot Dashboard')
                                                    ->image()
                                                    ->directory('strategix/dashboards')
                                                    ->maxSize(2048),
                                            ])
                                            ->columns(1)
                                            ->defaultItems(4)
                                            ->maxItems(6),
                                    ])
                                    ->collapsible(),

                                // Pricing Section
                                Forms\Components\Section::make('Pricing Plans')
                                    ->description('Pengaturan tabel harga')
                                    ->schema([
                                        Forms\Components\TextInput::make('strategix_pricing_title')
                                            ->label('Judul Section Harga')
                                            ->default('System Features & Consulting Services'),

                                        Forms\Components\Repeater::make('strategix_pricing_plans')
                                            ->label('Paket Harga')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nama Paket')
                                                    ->required(),
                                                Forms\Components\TextInput::make('price')
                                                    ->label('Harga')
                                                    ->placeholder('Rp. 0,- atau Hubungi Kami'),
                                                Forms\Components\TextInput::make('subtitle')
                                                    ->label('Subtitle')
                                                    ->placeholder('Monthly/Yearly'),
                                                Forms\Components\Toggle::make('is_highlighted')
                                                    ->label('Highlight Paket')
                                                    ->default(false),
                                                Forms\Components\Repeater::make('features')
                                                    ->label('Fitur')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('text')
                                                            ->label('Fitur')
                                                            ->required(),
                                                        Forms\Components\Toggle::make('included')
                                                            ->label('Termasuk')
                                                            ->default(true),
                                                    ])
                                                    ->columns(2)
                                                    ->defaultItems(5),
                                            ])
                                            ->columns(1)
                                            ->defaultItems(4)
                                            ->maxItems(5),
                                    ])
                                    ->collapsible(),

                                // Platform Access Steps Section
                                Forms\Components\Section::make('Platform Access Steps')
                                    ->description('Langkah-langkah cara mengakses platform')
                                    ->schema([
                                        Forms\Components\TextInput::make('platform_steps_title')
                                            ->label('Judul Section')
                                            ->default('Grapadi Strategix Platform Access & Dashboard'),

                                        Forms\Components\Repeater::make('platform_steps')
                                            ->label('Langkah-langkah')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Judul Langkah')
                                                    ->required()
                                                    ->placeholder('Buat akun baru'),
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Deskripsi')
                                                    ->rows(2)
                                                    ->placeholder('Isi data berikut pada halaman pendaftaran:'),
                                                Forms\Components\Repeater::make('bullets')
                                                    ->label('Poin-poin')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('text')
                                                            ->label('Poin')
                                                            ->required(),
                                                    ])
                                                    ->columns(1)
                                                    ->defaultItems(3),
                                                Forms\Components\TextInput::make('action_text')
                                                    ->label('Teks Aksi')
                                                    ->placeholder('Klik BUAT AKUN untuk melanjutkan'),
                                                Forms\Components\FileUpload::make('image')
                                                    ->label('Screenshot')
                                                    ->image()
                                                    ->directory('strategix/steps')
                                                    ->maxSize(2048),
                                                Forms\Components\TextInput::make('image_label')
                                                    ->label('Label Gambar')
                                                    ->placeholder('Halaman Pendaftaran'),
                                            ])
                                            ->columns(1)
                                            ->defaultItems(2)
                                            ->maxItems(4),
                                    ])
                                    ->collapsible(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Helper function to handle file uploads
        $processFile = function($value, $existingKey) {
            if (is_array($value) && !empty($value)) {
                return reset($value);
            } elseif (empty($value)) {
                return SiteSetting::get($existingKey, '');
            }
            return $value;
        };

        // General
        SiteSetting::set('site_company_name', $data['company_name'], 'general', 'text');
        SiteSetting::set('site_tagline', $data['tagline'], 'general', 'text');
        SiteSetting::set('site_email', $data['email'], 'general', 'text');
        SiteSetting::set('site_phone', $data['phone'], 'general', 'text');
        SiteSetting::set('site_whatsapp', $data['whatsapp'], 'general', 'text');
        SiteSetting::set('site_address', $data['address'], 'general', 'text');

        // Logo & Branding
        SiteSetting::set('site_logo', $processFile($data['logo'], 'site_logo'), 'branding', 'text');
        SiteSetting::set('site_logo_dark', $processFile($data['logo_dark'], 'site_logo_dark'), 'branding', 'text');
        SiteSetting::set('site_logo_only', $data['logo_only'], 'branding', 'boolean');
        SiteSetting::set('site_favicon', $processFile($data['favicon'], 'site_favicon'), 'branding', 'text');

        // Social Media
        SiteSetting::set('social_facebook', $data['social_facebook'], 'social', 'text');
        SiteSetting::set('social_twitter', $data['social_twitter'], 'social', 'text');
        SiteSetting::set('social_linkedin', $data['social_linkedin'], 'social', 'text');
        SiteSetting::set('social_instagram', $data['social_instagram'], 'social', 'text');
        SiteSetting::set('social_youtube', $data['social_youtube'], 'social', 'text');
        SiteSetting::set('social_tiktok', $data['social_tiktok'], 'social', 'text');

        // Footer
        SiteSetting::set('footer_copyright', $data['footer_copyright'], 'footer', 'text');
        SiteSetting::set('footer_description', $data['footer_description'], 'footer', 'text');
        SiteSetting::set('footer_newsletter_title', $data['footer_newsletter_title'], 'footer', 'text');
        SiteSetting::set('footer_newsletter_description', $data['footer_newsletter_description'], 'footer', 'text');

        // Hero Section
        SiteSetting::set('hero_title', $data['hero_title'], 'homepage', 'text');
        SiteSetting::set('hero_subtitle', $data['hero_subtitle'], 'homepage', 'text');
        SiteSetting::set('hero_cta_text', $data['hero_cta_text'], 'homepage', 'text');
        SiteSetting::set('hero_cta_url', $data['hero_cta_url'], 'homepage', 'text');
        SiteSetting::set('hero_show_logo', $data['hero_show_logo'], 'homepage', 'boolean');
        SiteSetting::set('hero_stats', is_array($data['hero_stats']) ? json_encode($data['hero_stats']) : $data['hero_stats'], 'homepage', 'json');

        // Director Section
        SiteSetting::set('director_title', $data['director_title'], 'homepage', 'text');
        SiteSetting::set('director_name', $data['director_name'], 'homepage', 'text');
        SiteSetting::set('director_description', $data['director_description'], 'homepage', 'text');
        SiteSetting::set('director_image', $processFile($data['director_image'], 'director_image'), 'homepage', 'text');
        SiteSetting::set('director_linkedin', $data['director_linkedin'], 'homepage', 'text');

        // About Page - Hero Section
        SiteSetting::set('about_hero_tagline', $data['about_hero_tagline'], 'about_page', 'text');
        SiteSetting::set('about_hero_title', $data['about_hero_title'], 'about_page', 'text');
        SiteSetting::set('about_hero_description', $data['about_hero_description'], 'about_page', 'text');
        SiteSetting::set('about_hero_image', $processFile($data['about_hero_image'], 'about_hero_image'), 'about_page', 'text');

        // About Page - Leadership Team Section
        SiteSetting::set('about_team_title', $data['about_team_title'], 'about_page', 'text');
        SiteSetting::set('about_team_subtitle', $data['about_team_subtitle'], 'about_page', 'text');

        // About Page - CTA Section
        SiteSetting::set('about_cta_title', $data['about_cta_title'], 'about_page', 'text');
        SiteSetting::set('about_cta_description', $data['about_cta_description'], 'about_page', 'text');
        SiteSetting::set('about_cta_primary_text', $data['about_cta_primary_text'], 'about_page', 'text');
        SiteSetting::set('about_cta_primary_url', $data['about_cta_primary_url'], 'about_page', 'text');
        SiteSetting::set('about_cta_secondary_text', $data['about_cta_secondary_text'], 'about_page', 'text');
        SiteSetting::set('about_cta_secondary_text', $data['about_cta_secondary_text'], 'about_page', 'text');
        SiteSetting::set('about_cta_secondary_url', $data['about_cta_secondary_url'], 'about_page', 'text');

        // About Page - What We Do Section
        SiteSetting::set('about_what_we_do_title', $data['about_what_we_do_title'], 'about_page', 'text');
        SiteSetting::set('about_what_we_do_description', $data['about_what_we_do_description'], 'about_page', 'text');
        SiteSetting::set('about_what_we_do_items', json_encode($data['about_what_we_do_items']), 'about_page', 'json');

        // About Page - Vision Section
        SiteSetting::set('about_vision_title', $data['about_vision_title'], 'about_page', 'text');
        SiteSetting::set('about_vision_description', $data['about_vision_description'], 'about_page', 'text');

        // About Page - Mission Section
        SiteSetting::set('about_mission_title', $data['about_mission_title'], 'about_page', 'text');
        SiteSetting::set('about_mission_items', json_encode($data['about_mission_items']), 'about_page', 'json');

        // About Page - Solutions Section
        SiteSetting::set('about_solutions_title', $data['about_solutions_title'], 'about_page', 'text');
        SiteSetting::set('about_solutions_description', $data['about_solutions_description'], 'about_page', 'text');
        SiteSetting::set('about_solutions_items', json_encode($data['about_solutions_items']), 'about_page', 'json');

        // Services Page - Hero Section
        SiteSetting::set('svc_hero_title', $data['svc_hero_title'], 'services_page', 'text');
        SiteSetting::set('svc_hero_subtitle', $data['svc_hero_subtitle'], 'services_page', 'text');
        SiteSetting::set('svc_hero_highlight', $data['svc_hero_highlight'], 'services_page', 'text');
        SiteSetting::set('svc_hero_description', $data['svc_hero_description'], 'services_page', 'text');
        SiteSetting::set('svc_hero_primary_text', $data['svc_hero_primary_text'], 'services_page', 'text');
        SiteSetting::set('svc_hero_primary_url', $data['svc_hero_primary_url'], 'services_page', 'text');
        SiteSetting::set('svc_hero_secondary_text', $data['svc_hero_secondary_text'], 'services_page', 'text');
        SiteSetting::set('svc_hero_secondary_url', $data['svc_hero_secondary_url'], 'services_page', 'text');

        // Services Page - Expertise Section
        SiteSetting::set('svc_expertise_tagline', $data['svc_expertise_tagline'], 'services_page', 'text');
        SiteSetting::set('svc_expertise_headline', $data['svc_expertise_headline'], 'services_page', 'text');

        // Services Page - Dashboard Title Section
        SiteSetting::set('svc_dashboard_title', $data['svc_dashboard_title'], 'services_page', 'text');
        SiteSetting::set('svc_dashboard_description', $data['svc_dashboard_description'], 'services_page', 'text');

        // Services Page - CTA Section
        SiteSetting::set('svc_cta_title', $data['svc_cta_title'], 'services_page', 'text');
        SiteSetting::set('svc_cta_description', $data['svc_cta_description'], 'services_page', 'text');
        SiteSetting::set('svc_cta_primary_text', $data['svc_cta_primary_text'], 'services_page', 'text');
        SiteSetting::set('svc_cta_primary_url', $data['svc_cta_primary_url'], 'services_page', 'text');

        // Services Page - Grapadi Strategix Section
        SiteSetting::set('strategix_is_active', $data['strategix_is_active'], 'services_page', 'boolean');
        SiteSetting::set('strategix_logo', $processFile($data['strategix_logo'], 'strategix_logo'), 'services_page', 'text');
        SiteSetting::set('strategix_title', $data['strategix_title'], 'services_page', 'text');
        SiteSetting::set('strategix_description', $data['strategix_description'], 'services_page', 'text');
        SiteSetting::set('strategix_cta_text', $data['strategix_cta_text'], 'services_page', 'text');
        SiteSetting::set('strategix_cta_url', $data['strategix_cta_url'], 'services_page', 'text');
        
        // Process dashboards with images
        $dashboards = $data['strategix_dashboards'] ?? [];
        foreach ($dashboards as $key => $dashboard) {
            if (isset($dashboard['image'])) {
                $dashboards[$key]['image'] = $processFile($dashboard['image'], 'strategix_dashboard_' . $key);
            }
        }
        SiteSetting::set('strategix_dashboards', json_encode($dashboards), 'services_page', 'json');
        
        SiteSetting::set('strategix_pricing_title', $data['strategix_pricing_title'], 'services_page', 'text');
        SiteSetting::set('strategix_pricing_plans', json_encode($data['strategix_pricing_plans'] ?? []), 'services_page', 'json');

        // Services Page - Platform Access Steps
        SiteSetting::set('platform_steps_title', $data['platform_steps_title'], 'services_page', 'text');
        $steps = $data['platform_steps'] ?? [];
        foreach ($steps as $key => $step) {
            if (isset($step['image'])) {
                $steps[$key]['image'] = $processFile($step['image'], 'platform_step_' . $key);
            }
        }
        SiteSetting::set('platform_steps', json_encode($steps), 'services_page', 'json');

        // SEO & Titles
        SiteSetting::set('page_title_home', $data['page_title_home'], 'seo', 'text');
        SiteSetting::set('page_title_about', $data['page_title_about'], 'seo', 'text');
        SiteSetting::set('page_title_services', $data['page_title_services'], 'seo', 'text');
        SiteSetting::set('page_title_portfolio', $data['page_title_portfolio'], 'seo', 'text');
        SiteSetting::set('page_title_blog', $data['page_title_blog'], 'seo', 'text');
        SiteSetting::set('page_title_timeline', $data['page_title_timeline'], 'seo', 'text');
        SiteSetting::set('page_title_contact', $data['page_title_contact'], 'seo', 'text');

        // Clear cache
        SiteSetting::clearCache();

        Notification::make()
            ->title('Settings saved successfully!')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    /**
     * Decode JSON string to array for Repeater field
     */
    protected function decodeJsonStats(string|array|null $json): array
    {
        // If already an array, return it
        if (is_array($json)) {
            return $json;
        }

        if (empty($json)) {
            return [
                ['icon' => 'person_outline', 'label' => '30+ tahun pengalaman untuk kesuksesan Anda.'],
                ['icon' => 'cloud_queue', 'label' => 'Jaringan luas nasional & internasional untuk dukung bisnis Anda.'],
                ['icon' => 'groups', 'label' => 'Tim ahli berpengalaman 10th+ dan berlatar belakang Magister dibidangnya siap mendukung Bisnis Anda'],
                ['icon' => 'menu_book', 'label' => 'Solusi berbasis data dengan harga terbaik untuk Anda'],
            ];
        }

        $decoded = json_decode($json, true);
        
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Decode JSON string to array for Items Repeater
     */
    protected function decodeJsonItems(string|array|null $json, string $type = ''): array
    {
        // If already an array, return it
        if (is_array($json)) {
            return $json;
        }

        if (!empty($json)) {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // Return defaults if empty
        if ($type === 'what_we_do') {
            return [
                ['text' => 'Strategic Business Advisor'],
                ['text' => 'Strategic Marketing & Growth'],
                ['text' => 'Strategic Property Advisory'],
                ['text' => 'Corporate Management Solutions'],
                ['text' => 'Bisnis plan PLATFORM'],
                ['text' => 'Feasibility Study & Investment Analysis'],
            ];
        } elseif ($type === 'mission') {
            return [
                ['text' => 'Memberikan solusi manajemen berbasis data dan digital dengan dukungan riset empiris untuk mendukung pengambilan keputusan yang strategis dan berkelanjutan.'],
                ['text' => 'Meningkatkan daya saing klien di pasar nasional maupun internasional melalui strategi yang efektif, efisiensi energi, dan prinsip ekonomi hijau.'],
                ['text' => 'Mengoptimalkan operasional klien melalui integrasi teknologi dan standar ESG guna menciptakan tata kelola yang hemat energi, minim limbah, dan bertanggung jawab sosial.'],
                ['text' => 'Membangun kemitraan strategis yang terpercaya dengan memadukan keahlian lokal 30 tahun dan jaringan global ITIALUS.'],
            ];
        } elseif ($type === 'solutions') {
            return [
                [
                    'title' => 'Pioneering Technology and Solutions', 
                    'description' => 'Kami menggabungkan software business plan dengan keahlian para profesional berpengalaman untuk mendukung perencanaan dan keputusan strategis.'
                ],
                [
                    'title' => 'Sustainable Practices and Environmental Stewardship', 
                    'description' => 'Kami membantu membangun strategi bisnis berkelanjutan yang adaptif terhadap perubahan pasar.'
                ],
                [
                    'title' => 'Safety and Operational Excellence', 
                    'description' => 'Pendekatan kami berfokus pada efisiensi, kinerja, dan tata kelola bisnis yang kuat.'
                ],
                [
                    'title' => 'Strategic Partnerships and Collaborations', 
                    'description' => 'Kami membangun kolaborasi untuk menciptakan nilai tambah dan mencapai tujuan bersama.'
                ],
            ];
        }

        return [];
    }
}
