<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Brand;
use App\Models\ExecutiveTeam;
use App\Models\Faq;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Homepage - menampilkan hero, trusted brands, services, FAQ, dan CTA
     */
    public function home()
    {
        $services = Service::orderBy('service_name')->take(8)->get();
        
        // Get trusted brands
        $trustedBrands = Brand::active()->ordered()
            ->where('type', Brand::TYPE_TRUSTED)
            ->get();

        // Get Hero Section settings
        $heroStats = SiteSetting::get('hero_stats', null);
        if ($heroStats && is_string($heroStats)) {
            $heroStats = json_decode($heroStats, true);
        }
        if (empty($heroStats)) {
            $heroStats = [
                ['icon' => 'groups', 'number' => '18+', 'label' => 'Years Experience', 'description' => 'Pengalaman panjang dalam memberikan solusi strategis untuk berbagai industri.'],
                ['icon' => 'public', 'number' => '500+', 'label' => 'Projects Completed', 'description' => 'Proyek studi kelayakan, bisnis, dan investasi yang berhasil kami selesaikan.'],
                ['icon' => 'bar_chart', 'number' => '120+', 'label' => 'Corporate Clients', 'description' => 'Klien korporasi dan institusi dari berbagai sektor di Indonesia.'],
                ['icon' => 'account_balance', 'number' => 'Rp5T+', 'label' => 'Investment Analysed', 'description' => 'Total nilai investasi yang telah kami analisis dan berikan rekomendasi strategis.'],
            ];
        }

        $hero = [
            'title' => SiteSetting::get('hero_title', 'Grapadi Konsultan Indonesia'),
            'subtitle' => SiteSetting::get('hero_subtitle', 'Where Data Meets Insight'),
            'cta_text' => SiteSetting::get('hero_cta_text', 'Book a Discovery Call'),
            'cta_url' => SiteSetting::get('hero_cta_url', '/contact'),
            'show_logo' => SiteSetting::get('hero_show_logo', true),
            'stats' => $heroStats,
        ];

        // Get FAQs for home page
        $faqs = Faq::active()->ordered()->get()->map(function ($faq) {
            return [
                'question' => $faq->question,
                'answer' => $faq->answer,
            ];
        })->toArray();

        // Grapadi Strategix Section
        $strategix = [
            'is_active' => SiteSetting::get('strategix_is_active', true),
            'logo' => SiteSetting::get('strategix_logo', ''),
            'title' => SiteSetting::get('strategix_title', 'Grapadi Strategix'),
            'description' => SiteSetting::get('strategix_description', 'Grapadi Strategix is a flagship product of Grapadi Konsultan, focusing on data- and technology-driven strategic consulting services. Designed to support informed and effective decision-making, Grapadi Strategix helps organizations enhance business process effectiveness, strengthen corporate governance, and achieve sustainable long-term performance.'),
            'cta_text' => SiteSetting::get('strategix_cta_text', 'Coba Gratis Sekarang'),
            'cta_url' => SiteSetting::get('strategix_cta_url', 'https://strategix.grapadi.com'),
        ];

        // Director Section
        $director = [
            'title' => SiteSetting::get('director_title', 'Director'),
            'name' => SiteSetting::get('director_name', 'Andika Pujangkoro, SE, M.Ec.Dev'),
            'description' => SiteSetting::get('director_description', 'Andika Pujangkoro, SE., M.Ec.Dev merupakan Director dari Grapadi International yang berpengalaman dalam bidang studi kelayakan, market research, business plan, dan konsultasi bisnis. Berbekal latar belakang akademik dan pengalaman profesional dalam berbagai proyek lintas sektor, beliau membantu perusahaan, investor, dan pengembang dalam mengambil keputusan bisnis yang lebih tepat melalui pendekatan berbasis data dan analisis yang komprehensif.'),
            'image' => SiteSetting::get('director_image', 'image/about/person/image.png'),
            'linkedin' => SiteSetting::get('director_linkedin', ''),
        ];

        return view('pages.home', compact(
            'services', 
            'trustedBrands', 
            'hero',
            'faqs',
            'strategix',
            'director'
        ));
    }

    /**
     * About page - menampilkan executive team
     */
    public function about()
    {

        $executiveTeam = ExecutiveTeam::orderBy('id')->get();

        // Get About Page - Hero Section settings
        $aboutHero = [
            'tagline' => SiteSetting::get('about_hero_tagline', 'About Us'),
            'title' => SiteSetting::get('about_hero_title', 'Strategic Insights, Digital Advantage'),
            'description' => SiteSetting::get('about_hero_description', 'Grapadi adalah perusahaan Business Advisory dan Riset Strategis yang mendampingi pengambilan keputusan bisnis dan investasi melalui studi kelayakan, business plan, dan perencanaan strategis berbasis riset. Berpengalaman puluhan tahun, Grapadi melayani berbagai sektor industri sebagai mitra strategis bisnis dan institusi.'),
            'image' => SiteSetting::get('about_hero_image', 'image/about/company/image.png'),
        ];

        // Get Director settings (reuse from homepage)
        $director = [
            'title' => SiteSetting::get('director_title', 'Director'),
            'name' => SiteSetting::get('director_name', 'Muhammad Dwi Andika, SE, M.Ec.Dev, CA, CWA'),
            'description' => SiteSetting::get('director_description', 'Dengan pengalaman 17+ tahun, Andika membantu klien mengatasi hambatan bisnis, memahami pasar, dan mengambil keputusan strategis berbasis data. Banyak perusahaan di Asia Tenggara telah merasakan dampak langsung dari pendekatan konsultasi yang praktis dan menyeluruh yang ia pimpin.'),
            'image' => SiteSetting::get('director_image', 'image/about/person/image.png'),
            'linkedin' => SiteSetting::get('director_linkedin', ''),
        ];

        // Get About Page - Leadership Team Section settings
        $teamSection = [
            'title' => SiteSetting::get('about_team_title', 'Leadership Team'),
            'subtitle' => SiteSetting::get('about_team_subtitle', 'Meet the minds behind our strategic insights.'),
        ];

        // Get About Page - CTA Section settings
        $aboutCta = [
            'title' => SiteSetting::get('about_cta_title', 'Ready to work with us?'),
            'description' => SiteSetting::get('about_cta_description', "Whether you're looking for insights to grow your business or a career to grow your potential, we want to hear from you."),
            'primary_text' => SiteSetting::get('about_cta_primary_text', 'Join Our Team'),
            'primary_url' => SiteSetting::get('about_cta_primary_url', '/careers'),
            'secondary_text' => SiteSetting::get('about_cta_secondary_text', 'Contact Us'),
            'secondary_url' => SiteSetting::get('about_cta_secondary_url', '/contact'),
        ];

        // Get What We Do Section settings
        $whatWeDoItemsVal = SiteSetting::get('about_what_we_do_items', null);
        $whatWeDoItems = [];
        if (!empty($whatWeDoItemsVal)) {
            $whatWeDoItems = is_array($whatWeDoItemsVal) ? $whatWeDoItemsVal : json_decode($whatWeDoItemsVal, true);
        }
        if (empty($whatWeDoItems)) {
            $whatWeDoItems = [
                ['text' => 'Strategic Business Advisor'],
                ['text' => 'Strategic Marketing & Growth'],
                ['text' => 'Strategic Property Advisory'],
                ['text' => 'Corporate Management Solutions'],
                ['text' => 'Bisnis plan PLATFORM'],
                ['text' => 'Feasibility Study & Investment Analysis'],
            ];
        }

        $aboutWhatWeDo = [
            'title' => SiteSetting::get('about_what_we_do_title', 'What We Do?'),
            'description' => SiteSetting::get('about_what_we_do_description', 'We provide technology-enabled business consulting services with a data-driven approach and digital tools, supported by long and proven experience across industries, to strengthen business processes, corporate governance, and sustainable decision-making toward better performance.'),
            'items' => $whatWeDoItems,
        ];

        // Get Vision Section settings
        $aboutVision = [
            'title' => SiteSetting::get('about_vision_title', 'Vision'),
            'description' => SiteSetting::get('about_vision_description', 'Menjadi mitra konsultan manajemen yang memadukan keunggulan digital dan jaringan global ITIALUS untuk merancang solusi bisnis yang strategis, berkelanjutan, dan berstandar internasional.'),
        ];

        // Get Mission Section settings
        $missionItemsVal = SiteSetting::get('about_mission_items', null);
        $missionItems = [];
        if (!empty($missionItemsVal)) {
            $missionItems = is_array($missionItemsVal) ? $missionItemsVal : json_decode($missionItemsVal, true);
        }
        if (empty($missionItems)) {
            $missionItems = [
                ['text' => 'Memberikan solusi manajemen berbasis data dan digital dengan dukungan riset empiris untuk mendukung pengambilan keputusan yang strategis dan berkelanjutan.'],
                ['text' => 'Meningkatkan daya saing klien di pasar nasional maupun internasional melalui strategi yang efektif, efisiensi energi, dan prinsip ekonomi hijau.'],
                ['text' => 'Mengoptimalkan operasional klien melalui integrasi teknologi dan standar ESG guna menciptakan tata kelola yang hemat energi, minim limbah, dan bertanggung jawab sosial.'],
                ['text' => 'Membangun kemitraan strategis yang terpercaya dengan memadukan keahlian lokal 30 tahun dan jaringan global ITIALUS.'],
            ];
        }

        $aboutMission = [
            'title' => SiteSetting::get('about_mission_title', 'Mission'),
            'items' => $missionItems,
        ];

        // Get Solutions Section settings
        $solutionsItemsVal = SiteSetting::get('about_solutions_items', null);
        $solutionsItems = [];
        if (!empty($solutionsItemsVal)) {
            $solutionsItems = is_array($solutionsItemsVal) ? $solutionsItemsVal : json_decode($solutionsItemsVal, true);
        }
        if (empty($solutionsItems)) {
            $solutionsItems = [
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

        $aboutSolutions = [
            'title' => SiteSetting::get('about_solutions_title', 'Solutions'),
            'description' => SiteSetting::get('about_solutions_description', 'Di Grapadi International, kami menyediakan layanan konsultasi bisnis yang komprehensif untuk menjawab kebutuhan perusahaan di pasar nasional maupun internasional. Layanan kami mencakup perencanaan strategis, analisis investasi, pengembangan bisnis, hingga implementasi solusi yang berorientasi pada hasil, keberlanjutan, dan penciptaan nilai jangka panjang.'),
            'items' => $solutionsItems,
        ];

        // Get FAQs
        $faqs = Faq::active()->ordered()->get();

        return view('pages.about', compact(
            'executiveTeam',
            'aboutHero',
            'director',
            'teamSection',
            'aboutCta',
            'aboutWhatWeDo',
            'aboutVision',
            'aboutMission',
            'aboutSolutions',
            'faqs'
        ));
    }

    /**
 * Services page - menampilkan semua services
 */
public function services()
{

    $services = Service::orderBy('service_name')->get();

    // Grapadi Strategix Section
    $dashboardsVal = SiteSetting::get('strategix_dashboards', '[]');
    $dashboards = is_array($dashboardsVal) ? $dashboardsVal : (json_decode($dashboardsVal, true) ?? []);
    
    $pricingPlansVal = SiteSetting::get('strategix_pricing_plans', '[]');
    $pricingPlans = is_array($pricingPlansVal) ? $pricingPlansVal : (json_decode($pricingPlansVal, true) ?? []);

    $strategix = [
        'is_active' => SiteSetting::get('strategix_is_active', true),
        'logo' => SiteSetting::get('strategix_logo', ''),
        'title' => SiteSetting::get('strategix_title', 'Grapadi Strategix'),
        'description' => SiteSetting::get('strategix_description', 'Grapadi Strategix is a flagship product of Grapadi Konsultan, focusing on data- and technology-driven strategic consulting services. Designed to support informed and effective decision-making, Grapadi Strategix helps organizations enhance business process effectiveness, strengthen corporate governance, and achieve sustainable long-term performance.'),
        'cta_text' => SiteSetting::get('strategix_cta_text', 'Coba Gratis Sekarang'),
        'cta_url' => SiteSetting::get('strategix_cta_url', 'https://strategix.grapadi.com'),
        'dashboards' => $dashboards,
        'pricing_title' => SiteSetting::get('strategix_pricing_title', 'System Features & Consulting Services'),
        'pricing_plans' => $pricingPlans,
    ];

    // Platform Access Steps
    $stepsVal = SiteSetting::get('platform_steps', '[]');
    $steps = is_array($stepsVal) ? $stepsVal : (json_decode($stepsVal, true) ?? []);
    
    $platformSteps = [
        'title' => SiteSetting::get('platform_steps_title', 'Grapadi Strategix Platform Access & Dashboard'),
        'steps' => $steps,
    ];

    // Services Page - Hero Section
    $heroSection = [
        'title' => SiteSetting::get('svc_hero_title', 'Strategic Intelligence for Global Markets'),
        'subtitle' => SiteSetting::get('svc_hero_subtitle', 'Strategic Intelligence for'),
        'highlight' => SiteSetting::get('svc_hero_highlight', 'Global Markets'),
        'description' => SiteSetting::get('svc_hero_description', 'Data-driven insights that empower executive decision-making and drive sustainable growth in an evolving landscape.'),
        'primary_text' => SiteSetting::get('svc_hero_primary_text', 'View Services'),
        'primary_url' => SiteSetting::get('svc_hero_primary_url', '#services'),
        'secondary_text' => SiteSetting::get('svc_hero_secondary_text', 'Request Proposal'),
        'secondary_url' => SiteSetting::get('svc_hero_secondary_url', '/contact'),
    ];

    // Services Page - Expertise Section
    $expertiseSection = [
        'tagline' => SiteSetting::get('svc_expertise_tagline', 'Our Expertise'),
        'headline' => SiteSetting::get('svc_expertise_headline', 'Comprehensive solutions tailored to your unique business challenges'),
    ];

    // Services Page - Dashboard Title Section
    $dashboardSection = [
        'title' => SiteSetting::get('svc_dashboard_title', 'Integrated Strategy Platform & Decision Dashboard'),
        'description' => SiteSetting::get('svc_dashboard_description', 'Platform terintegrasi untuk memantau kinerja bisnis dan mengambil keputusan strategis'),
    ];

    // Services Page - CTA Section
    $ctaSection = [
        'title' => SiteSetting::get('svc_cta_title', 'Ready to elevate your strategy?'),
        'description' => SiteSetting::get('svc_cta_description', 'Schedule a consultation with our senior partners to discuss your specific needs and how we can help you achieve your goals.'),
        'primary_text' => SiteSetting::get('svc_cta_primary_text', 'Request a Proposal'),
        'primary_url' => SiteSetting::get('svc_cta_primary_url', '/contact'),
    ];

    return view('pages.services', compact(
        'services', 
        'strategix', 
        'platformSteps',
        'heroSection',
        'expertiseSection',
        'dashboardSection',
        'ctaSection'
    ));
}

    /**
     * Portfolio page - menampilkan semua portfolio dengan relasi service dan category
     */
    public function portfolio(Request $request)
    {
        $categorySlug = $request->get('category');

        // Get all portfolio categories for filter
        $portfolioCategories = \App\Models\PortfolioCategory::withCount('portfolios')
            ->orderBy('name')
            ->get();

        // Build portfolio query with filters
        $portfoliosQuery = Portfolio::with(['service', 'category'])
            ->when($categorySlug, function ($query) use ($categorySlug) {
                return $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            })
            ->orderBy('project_year', 'desc');

        $portfolios = $portfoliosQuery->get();

        // Get current category filter info
        $currentCategory = $categorySlug 
            ? \App\Models\PortfolioCategory::where('slug', $categorySlug)->first() 
            : null;

        return view('pages.portfolio', compact(
            'portfolios', 
            'portfolioCategories', 
            'currentCategory'
        ));
    }

    /**
     * Blog page - menampilkan articles dengan pagination, search, dan filter
     */
    public function blog(Request $request)
    {
        $search = $request->get('q');
        $categorySlug = $request->get('category');
        $tagSlug = $request->get('tag');

        // Get featured article (only if no filters applied)
        $featuredArticle = null;
        if (!$search && !$categorySlug && !$tagSlug) {
            $featuredArticle = Article::with(['category', 'author'])
                ->published()
                ->where(function($query) {
                    $query->where('is_featured', true)
                        ->orWhereNull('is_featured');
                })
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        // Build articles query
        $articlesQuery = Article::with(['category', 'author', 'tags'])
            ->published()
            ->when($featuredArticle, function ($query) use ($featuredArticle) {
                return $query->where('id', '!=', $featuredArticle->id);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->when($categorySlug, function ($query) use ($categorySlug) {
                return $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            })
            ->when($tagSlug, function ($query) use ($tagSlug) {
                return $query->whereHas('tags', function ($q) use ($tagSlug) {
                    $q->where('slug', $tagSlug);
                });
            })
            ->orderBy('created_at', 'desc');

        $articles = $articlesQuery->paginate(9)->appends($request->query());

        // Get categories with article count
        $categories = \App\Models\Category::withCount(['articles' => function ($query) {
            $query->where('is_published', true);
        }])->orderBy('category_name')->get();

        // Get popular tags
        $popularTags = \App\Models\Tag::popular(15)->get();

        // Get current filter info
        $currentCategory = $categorySlug ? \App\Models\Category::where('slug', $categorySlug)->first() : null;
        $currentTag = $tagSlug ? \App\Models\Tag::where('slug', $tagSlug)->first() : null;

        return view('pages.blog', compact(
            'featuredArticle', 
            'articles', 
            'categories', 
            'popularTags',
            'search',
            'currentCategory',
            'currentTag'
        ));
    }


    /**
     * Contact page
     */
    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * Timeline page
     */
    public function timeline()
    {
        $timelines = \App\Models\CompanyTimeline::active()->ordered()->get();
        
        // Convert to array format expected by timeline component
        $history = $timelines->map(function ($item) {
            $imagePath = null;
            if ($item->image) {
                if (str_starts_with($item->image, 'http')) {
                    $imagePath = $item->image;
                } else {
                    $imagePath = asset('storage/' . $item->image);
                }
            }
            
            return [
                'year' => $item->year,
                'title' => $item->title,
                'description' => $item->description,
                'image' => $imagePath,
            ];
        })->toArray();
        
        return view('pages.timeline', compact('history'));
    }

    /**
     * Service detail page - menampilkan detail layanan berdasarkan slug
     */
    public function serviceDetail(string $slug)
    {
        $service = Service::where('slug', $slug)->firstOrFail();
        $relatedServices = Service::where('id', '!=', $service->id)
            ->take(3)
            ->get();
        $portfolios = Portfolio::where('service_id', $service->id)
            ->orderBy('project_year', 'desc')
            ->take(4)
            ->get();

        return view('pages.service-detail', compact('service', 'relatedServices', 'portfolios'));
    }

    /**
     * Article detail page - menampilkan detail artikel berdasarkan slug
     */
    public function articleDetail(string $slug)
    {
        $article = Article::with(['category', 'author', 'tags'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count
        $article->incrementViewCount();

        // Get related articles (same category)
        $relatedArticles = Article::with(['category', 'tags'])
            ->published()
            ->where('id', '!=', $article->id)
            ->when($article->category_id, function ($query) use ($article) {
                return $query->where('category_id', $article->category_id);
            })
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Get previous article
        $previousArticle = Article::published()
            ->where('created_at', '<', $article->created_at)
            ->orderBy('created_at', 'desc')
            ->first();

        // Get next article
        $nextArticle = Article::published()
            ->where('created_at', '>', $article->created_at)
            ->orderBy('created_at', 'asc')
            ->first();

        return view('pages.article-detail', compact(
            'article', 
            'relatedArticles',
            'previousArticle',
            'nextArticle'
        ));
    }
}

