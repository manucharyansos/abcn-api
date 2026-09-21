<?php

namespace Database\Seeders;

use App\Models\ContactRequest;
use App\Models\Media;
use App\Models\NewsArticle;
use App\Models\Project;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PreviewContentSeeder extends Seeder
{
    public function run(): void
    {
        // Categories + products + filters + product images/documents.
        $this->call(DemoCatalogSeeder::class);

        // Media library examples used by the preview editorial entries below.
        $media = $this->seedMedia();

        foreach ($this->services($media) as $item) {
            Service::query()->updateOrCreate(['slug' => $item['slug']], $item);
        }

        foreach ($this->projects($media) as $item) {
            Project::query()->updateOrCreate(['slug' => $item['slug']], $item);
        }

        foreach ($this->news($media) as $item) {
            NewsArticle::query()->updateOrCreate(['slug' => $item['slug']], $item);
        }

        foreach ($this->team($media) as $item) {
            TeamMember::query()->updateOrCreate(['slug' => $item['slug']], $item);
        }

        foreach ($this->inquiries() as $item) {
            ContactRequest::query()->updateOrCreate(
                ['email' => $item['email'], 'message' => $item['message']],
                $item,
            );
        }
    }

    /**
     * Create a small reusable media library directly on Laravel's public disk.
     * The seeder is idempotent: running it again updates the same records/files.
     *
     * @return array<string, string>
     */
    private function seedMedia(): array
    {
        $adminId = User::query()->where('role', 'admin')->value('id');

        $assets = [
            'service-design' => ['service-design.svg', 'Electrical design', 'service'],
            'service-panels' => ['service-panels.svg', 'Panel assembly and integration', 'panels'],
            'service-audit' => ['service-audit.svg', 'Technical site audit', 'audit'],
            'project-industrial' => ['project-industrial.svg', 'Industrial power distribution project', 'industrial'],
            'project-building' => ['project-building.svg', 'Commercial building electrical project', 'building'],
            'project-metering' => ['project-metering.svg', 'Energy metering and monitoring project', 'metering'],
            'project-ev' => ['project-ev.svg', 'EV charging infrastructure project', 'ev'],
            'project-automation' => ['project-automation.svg', 'Automation control project', 'automation'],
            'project-retail' => ['project-retail.svg', 'Retail power distribution project', 'retail'],
            'news-engineering' => ['news-engineering.svg', 'Engineering update', 'engineering'],
            'news-partnership' => ['news-partnership.svg', 'Partnership update', 'partnership'],
            'news-catalog' => ['news-catalog.svg', 'Catalog update', 'catalog'],
            'news-team' => ['news-team.svg', 'ABCN team update', 'team'],
            'team-01' => ['team-01.svg', 'Preview team member portrait 1', 'portrait-1'],
            'team-02' => ['team-02.svg', 'Preview team member portrait 2', 'portrait-2'],
            'team-03' => ['team-03.svg', 'Preview team member portrait 3', 'portrait-3'],
            'team-04' => ['team-04.svg', 'Preview team member portrait 4', 'portrait-4'],
        ];

        $urls = [];

        foreach ($assets as $key => [$filename, $label, $variant]) {
            $path = 'media/preview/'.$filename;
            $svg = $this->previewSvg($label, $variant);

            Storage::disk('public')->put($path, $svg);

            $media = Media::query()->updateOrCreate(
                ['path' => $path],
                [
                    'uploaded_by' => $adminId,
                    'disk' => 'public',
                    'original_name' => $filename,
                    'mime_type' => 'image/svg+xml',
                    'kind' => 'image',
                    'size' => strlen($svg),
                    'alt' => [
                        'hy' => $this->armenianAlt($key),
                        'en' => $label,
                    ],
                ],
            );

            $urls[$key] = $media->url;
        }

        return $urls;
    }

    /**
     * @param array<string, string> $media
     */
    private function services(array $media): array
    {
        return [
            $this->editorial(
                'preview-electrical-design',
                'Էլեկտրական նախագծում',
                'Electrical design',
                'Նախագծային լուծումներ՝ բեռների հաշվարկից և սխեմաներից մինչև սարքավորումների տեխնիկական ընտրություն։',
                'Engineering design from load calculations and diagrams to technical equipment selection.',
                101,
                $media['service-design'],
                'Electrical design preview'
            ),
            $this->editorial(
                'preview-panel-integration',
                'Վահանակների հավաքում և ինտեգրում',
                'Panel assembly & integration',
                'Բաշխիչ և կառավարման վահանակների կառուցվածք, սարքավորումների ընտրություն և համակարգային ինտեգրում։',
                'Distribution and control panel structure, equipment selection and system integration.',
                102,
                $media['service-panels'],
                'Panel integration preview'
            ),
            $this->editorial(
                'preview-site-audit',
                'Տեխնիկական աուդիտ և տեղազննում',
                'Technical audit & site survey',
                'Օբյեկտի տեխնիկական ուսումնասիրություն՝ առկա վիճակը, սահմանափակումներն ու հաջորդ ինժեներական քայլերը հստակեցնելու համար։',
                'Technical site review to clarify existing conditions, constraints and the next engineering steps.',
                103,
                $media['service-audit'],
                'Technical audit preview'
            ),
            $this->editorial(
                'preview-commissioning',
                'Գործարկում և համակարգերի ստուգում',
                'Commissioning & system verification',
                'Փորձնական ծառայություն՝ գործարկման, ստուգումների և վերջնական տեխնիկական հանձնման քարտի տեսքը ստուգելու համար։',
                'Preview service for commissioning, verification and final technical handover.',
                104,
                $media['project-automation'],
                'Commissioning preview'
            ),
        ];
    }

    /**
     * @param array<string, string> $media
     */
    private function projects(array $media): array
    {
        $items = [
            ['preview-project-01', 'Արտադրական գծի էլեկտրամատակարարում', 'Industrial line power distribution', 'Արտադրական գծի համար բաշխման, պաշտպանության և տեխնիկական համակարգման փորձնական նախագիծ։', 'Preview project for distribution, protection and technical coordination of an industrial line.', 'project-industrial'],
            ['preview-project-02', 'Գրասենյակային շենքի վահանակներ', 'Office building distribution panels', 'Բազմահարկ գրասենյակային շենքի բաշխիչ վահանակների և սնուցման կառուցվածքի փորձնական նախագիծ։', 'Preview project for distribution panels and power architecture in a multi-storey office building.', 'project-building'],
            ['preview-project-03', 'Էներգիայի հաշվառման համակարգ', 'Energy metering system', 'Հաշվառման, տվյալների հավաքագրման և մոնիթորինգի փորձնական համակարգ։', 'Preview project for metering, data collection and monitoring.', 'project-metering'],
            ['preview-project-04', 'EV լիցքավորման ենթակառուցվածք', 'EV charging infrastructure', 'Լիցքավորման կայանների, պաշտպանության սարքավորումների և սնուցման փորձնական լուծում։', 'Preview solution for EV chargers, protection equipment and power supply.', 'project-ev'],
            ['preview-project-05', 'Ավտոմատացման վահանակ', 'Automation control panel', 'Կառավարման, ազդանշանների և ավտոմատացման սարքավորումների ինտեգրման փորձնական նախագիծ։', 'Preview project for control, signaling and automation equipment integration.', 'project-automation'],
            ['preview-project-06', 'Առևտրային տարածքի էներգաբաշխում', 'Retail facility power distribution', 'Առևտրային տարածքի հիմնական և վերջնական էներգաբաշխման փորձնական նախագիծ։', 'Preview project for main and final power distribution in a retail facility.', 'project-retail'],
        ];

        return array_map(function ($item, $index) use ($media) {
            return [
                ...$this->editorial(
                    $item[0],
                    $item[1],
                    $item[2],
                    $item[3],
                    $item[4],
                    200 + $index,
                    $media[$item[5]],
                    'ABCN preview project'
                ),
                'completed_at' => now()->subDays($index + 2)->toDateString(),
            ];
        }, $items, array_keys($items));
    }

    /**
     * @param array<string, string> $media
     */
    private function news(array $media): array
    {
        $items = [
            ['preview-news-01', 'ABCN-ի ինժեներական ուղղությունների թարմացում', 'ABCN engineering directions update', 'Փորձնական նորություն՝ ծառայությունների և տեխնիկական ուղղությունների զարգացման մասին։', 'Preview news item about the development of engineering services and technical directions.', 'news-engineering'],
            ['preview-news-02', 'Նոր տեխնիկական գործընկերության օրինակ', 'New technical partnership example', 'Փորձնական հրապարակում՝ գործընկերային նորությունների քարտի և մանրամասն էջի տեսքը ստուգելու համար։', 'Preview article for testing a partnership news card and detail page.', 'news-partnership'],
            ['preview-news-03', 'Ապրանքային կատալոգի թարմացում', 'Product catalog update', 'Փորձնական նորություն նոր կատեգորիաների, ապրանքների և տեխնիկական տվյալների ավելացման մասին։', 'Preview news item about new catalog categories, products and technical data.', 'news-catalog'],
            ['preview-news-04', 'Էներգախնայողության լուծումների թարմացում', 'Energy efficiency solutions update', 'Ավելի երկար փորձնական տեքստ՝ բջջային էջում տողադարձը և քարտերի հավասարեցումը ստուգելու համար։', 'Longer preview text for checking wrapping and card alignment on mobile screens.', 'project-metering'],
            ['preview-news-05', 'EV ենթակառուցվածքի նոր ուղղություն', 'EV infrastructure direction update', 'Փորձնական հրապարակում էլեկտրամոբիլների լիցքավորման տեխնիկական լուծումների մասին։', 'Preview article about technical solutions for EV charging infrastructure.', 'project-ev'],
            ['preview-news-06', 'ABCN թիմի թարմացում', 'ABCN team update', 'Փորձնական հրապարակում՝ թիմային և կորպորատիվ նորությունների բաժնի տեսքը ստուգելու համար։', 'Preview article for checking team and corporate news presentation.', 'news-team'],
        ];

        return array_map(function ($item, $index) use ($media) {
            return [
                ...$this->editorial(
                    $item[0],
                    $item[1],
                    $item[2],
                    $item[3],
                    $item[4],
                    300 + $index,
                    $media[$item[5]],
                    'ABCN preview news'
                ),
                'published_at' => now()->subHours($index + 1),
            ];
        }, $items, array_keys($items));
    }

    /**
     * @param array<string, string> $media
     */
    private function team(array $media): array
    {
        $items = [
            ['preview-team-01', 'Թիմի անդամ 1', 'Team member 1', 'Տեխնիկական ղեկավար', 'Technical lead', 'team-01'],
            ['preview-team-02', 'Թիմի անդամ 2', 'Team member 2', 'Նախագծերի ղեկավար', 'Project manager', 'team-02'],
            ['preview-team-03', 'Թիմի անդամ 3', 'Team member 3', 'Էլեկտրատեխնիկայի ինժեներ', 'Electrical engineer', 'team-03'],
            ['preview-team-04', 'Թիմի անդամ երկար անունով', 'Team member with a longer name', 'Տեխնիկական վաճառքի և լուծումների մասնագետ', 'Technical sales & solutions specialist', 'team-04'],
        ];

        return array_map(fn ($item, $index) => [
            'slug' => $item[0],
            'status' => 'published',
            'show_on_homepage' => false,
            'sort_order' => 400 + $index,
            'translations' => [
                'hy' => ['title' => $item[1], 'summary' => $item[3], 'body' => ''],
                'en' => ['title' => $item[2], 'summary' => $item[4], 'body' => ''],
            ],
            'images' => $this->image($media[$item[5]], 'ABCN preview team member'),
        ], $items, array_keys($items));
    }

    private function inquiries(): array
    {
        return [
            $this->inquiry('Արամ Մկրտչյան', 'Demo Electric', 'preview1@example.com', 'Խնդրում եմ առաջարկ ուղարկել ավտոմատ անջատիչների համար։', 'new'),
            $this->inquiry('Անի Սարգսյան', 'Demo Systems', 'preview2@example.com', 'Հետաքրքրված ենք էներգիայի հաշվառման համակարգով։', 'in_progress'),
            $this->inquiry('Դավիթ Գրիգորյան', 'Demo Project', 'preview3@example.com', 'Պետք է տեխնիկական խորհրդատվություն նոր նախագծի համար։', 'new'),
            $this->inquiry('Մարի Հովհաննիսյան', 'Demo Build', 'preview4@example.com', 'Կցանկանայինք քննարկել էլեկտրական վահանակների լուծումը։', 'completed'),
        ];
    }

    private function editorial(
        string $slug,
        string $hyTitle,
        string $enTitle,
        string $hySummary,
        string $enSummary,
        int $sortOrder,
        string $imageUrl,
        string $imageName,
    ): array {
        return [
            'slug' => $slug,
            'status' => 'published',
            'show_on_homepage' => true,
            'sort_order' => $sortOrder,
            'translations' => [
                'hy' => [
                    'title' => $hyTitle,
                    'summary' => $hySummary,
                    'body' => $hySummary."\n\nՍա փորձնական բովանդակություն է՝ էջի կառուցվածքը և դիզայնը ստուգելու համար։ Հետագայում այն կարող եք խմբագրել կամ ջնջել ադմինից։",
                ],
                'en' => [
                    'title' => $enTitle,
                    'summary' => $enSummary,
                    'body' => $enSummary."\n\nThis is preview content for checking the page structure and design. It can later be edited or removed from the admin panel.",
                ],
            ],
            'images' => $this->image($imageUrl, $imageName),
        ];
    }

    private function image(string $url, string $name): array
    {
        return [[
            'url' => $url,
            'name' => $name,
            'alt' => ['hy' => $name, 'en' => $name],
        ]];
    }

    private function inquiry(string $name, string $company, string $email, string $message, string $status): array
    {
        return [
            'locale' => 'hy',
            'request_type' => 'general',
            'name' => $name,
            'company' => $company,
            'email' => $email,
            'phone' => '+374 00 000000',
            'message' => $message,
            'status' => $status,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'ABCN Preview Seeder',
        ];
    }

    private function armenianAlt(string $key): string
    {
        return match ($key) {
            'service-design' => 'Էլեկտրական նախագծման փորձնական պատկեր',
            'service-panels' => 'Էլեկտրական վահանակների փորձնական պատկեր',
            'service-audit' => 'Տեխնիկական աուդիտի փորձնական պատկեր',
            'project-industrial' => 'Արտադրական էներգաբաշխման փորձնական նախագիծ',
            'project-building' => 'Շենքի էներգաբաշխման փորձնական նախագիծ',
            'project-metering' => 'Էներգիայի հաշվառման փորձնական նախագիծ',
            'project-ev' => 'EV լիցքավորման փորձնական նախագիծ',
            'project-automation' => 'Ավտոմատացման փորձնական նախագիծ',
            'project-retail' => 'Առևտրային տարածքի փորձնական նախագիծ',
            'news-engineering' => 'Ինժեներական նորության փորձնական պատկեր',
            'news-partnership' => 'Գործընկերային նորության փորձնական պատկեր',
            'news-catalog' => 'Կատալոգի նորության փորձնական պատկեր',
            'news-team' => 'Թիմային նորության փորձնական պատկեր',
            'team-01', 'team-02', 'team-03', 'team-04' => 'Թիմի անդամի փորձնական նկար',
            default => 'ABCN փորձնական պատկեր',
        };
    }

    private function previewSvg(string $label, string $variant): string
    {
        $art = match ($variant) {
            'service' => '<path d="M160 530 L360 330 L505 435 L720 220 L1030 500" fill="none" stroke="#f17a12" stroke-width="24" stroke-linecap="round" stroke-linejoin="round"/><circle cx="360" cy="330" r="30" fill="#2d69ab"/><circle cx="720" cy="220" r="30" fill="#2d69ab"/>',
            'panels' => '<rect x="190" y="150" width="820" height="450" rx="18" fill="#ffffff" stroke="#2d69ab" stroke-width="12"/><g fill="#e8f0f8" stroke="#225b9d" stroke-width="8"><rect x="260" y="230" width="170" height="120" rx="8"/><rect x="510" y="230" width="170" height="120" rx="8"/><rect x="760" y="230" width="170" height="120" rx="8"/></g><path d="M345 410 V520 M595 410 V520 M845 410 V520" stroke="#f17a12" stroke-width="18" stroke-linecap="round"/>',
            'audit' => '<circle cx="500" cy="350" r="190" fill="#ffffff" stroke="#2d69ab" stroke-width="18"/><path d="M630 490 L840 630" stroke="#f17a12" stroke-width="42" stroke-linecap="round"/><path d="M390 355 L470 430 L625 275" fill="none" stroke="#225b9d" stroke-width="26" stroke-linecap="round" stroke-linejoin="round"/>',
            'industrial' => '<path d="M120 570 H1080" stroke="#2d69ab" stroke-width="16"/><path d="M190 570 V330 L360 250 V570 M360 570 V380 L540 300 V570 M540 570 V230 H820 V570" fill="#e8f0f8" stroke="#225b9d" stroke-width="12"/><path d="M880 210 V570 M955 260 V570" stroke="#f17a12" stroke-width="20"/>',
            'building' => '<rect x="300" y="110" width="600" height="520" fill="#ffffff" stroke="#225b9d" stroke-width="14"/><g fill="#e8f0f8"><rect x="370" y="190" width="100" height="90"/><rect x="550" y="190" width="100" height="90"/><rect x="730" y="190" width="100" height="90"/><rect x="370" y="350" width="100" height="90"/><rect x="550" y="350" width="100" height="90"/><rect x="730" y="350" width="100" height="90"/></g><rect x="555" y="505" width="90" height="125" fill="#f17a12"/>',
            'metering' => '<circle cx="600" cy="365" r="225" fill="#ffffff" stroke="#225b9d" stroke-width="16"/><path d="M420 420 A190 190 0 0 1 780 420" fill="none" stroke="#e8f0f8" stroke-width="42"/><path d="M600 365 L730 270" stroke="#f17a12" stroke-width="22" stroke-linecap="round"/><circle cx="600" cy="365" r="28" fill="#2d69ab"/>',
            'ev' => '<rect x="430" y="115" width="340" height="480" rx="32" fill="#ffffff" stroke="#225b9d" stroke-width="16"/><rect x="505" y="190" width="190" height="110" rx="16" fill="#e8f0f8"/><path d="M598 355 L540 445 H610 L575 525 L680 405 H610 L645 355 Z" fill="#f17a12"/><path d="M770 250 C930 260 940 420 885 510" fill="none" stroke="#2d69ab" stroke-width="22" stroke-linecap="round"/>',
            'automation' => '<g fill="#ffffff" stroke="#225b9d" stroke-width="12"><circle cx="350" cy="250" r="90"/><circle cx="760" cy="250" r="90"/><circle cx="555" cy="500" r="90"/></g><path d="M430 270 L680 270 M405 325 L510 440 M705 325 L610 440" stroke="#f17a12" stroke-width="20" stroke-linecap="round"/><g fill="#2d69ab"><circle cx="350" cy="250" r="28"/><circle cx="760" cy="250" r="28"/><circle cx="555" cy="500" r="28"/></g>',
            'retail' => '<path d="M190 300 H1010 L940 180 H260 Z" fill="#ffffff" stroke="#225b9d" stroke-width="14"/><rect x="250" y="300" width="700" height="300" fill="#e8f0f8" stroke="#225b9d" stroke-width="14"/><path d="M380 600 V390 H520 V600 M650 390 H820" stroke="#f17a12" stroke-width="18"/>',
            'engineering' => '<path d="M190 540 L360 370 L495 455 L700 240 L1010 490" fill="none" stroke="#2d69ab" stroke-width="24" stroke-linecap="round"/><circle cx="360" cy="370" r="34" fill="#f17a12"/><circle cx="700" cy="240" r="34" fill="#f17a12"/><path d="M170 585 H1030" stroke="#225b9d" stroke-width="12"/>',
            'partnership' => '<circle cx="430" cy="360" r="150" fill="#ffffff" stroke="#225b9d" stroke-width="16"/><circle cx="770" cy="360" r="150" fill="#ffffff" stroke="#2d69ab" stroke-width="16"/><path d="M520 360 H680" stroke="#f17a12" stroke-width="32" stroke-linecap="round"/><circle cx="600" cy="360" r="45" fill="#f17a12"/>',
            'catalog' => '<rect x="260" y="140" width="680" height="500" rx="22" fill="#ffffff" stroke="#225b9d" stroke-width="14"/><path d="M360 260 H840 M360 360 H840 M360 460 H700" stroke="#2d69ab" stroke-width="22" stroke-linecap="round"/><circle cx="330" cy="260" r="16" fill="#f17a12"/><circle cx="330" cy="360" r="16" fill="#f17a12"/><circle cx="330" cy="460" r="16" fill="#f17a12"/>',
            'team' => '<g fill="#ffffff" stroke="#225b9d" stroke-width="14"><circle cx="430" cy="300" r="92"/><circle cx="770" cy="300" r="92"/></g><path d="M250 610 C270 455 355 415 430 415 C505 415 590 455 610 610 M590 610 C610 455 695 415 770 415 C845 415 930 455 950 610" fill="#e8f0f8" stroke="#2d69ab" stroke-width="14"/><path d="M560 510 H640" stroke="#f17a12" stroke-width="22" stroke-linecap="round"/>',
            'portrait-1', 'portrait-2', 'portrait-3', 'portrait-4' => '<circle cx="600" cy="300" r="135" fill="#ffffff" stroke="#225b9d" stroke-width="14"/><path d="M340 650 C365 470 470 435 600 435 C730 435 835 470 860 650" fill="#e8f0f8" stroke="#2d69ab" stroke-width="14"/><circle cx="600" cy="300" r="58" fill="#f17a12" opacity=".92"/>',
            default => '<path d="M180 560 H1020 M260 480 L480 260 L610 390 L820 180 L960 320" fill="none" stroke="#2d69ab" stroke-width="22" stroke-linecap="round" stroke-linejoin="round"/><circle cx="610" cy="390" r="30" fill="#f17a12"/>',
        };

        $safeLabel = htmlspecialchars($label, ENT_QUOTES | ENT_XML1, 'UTF-8');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 760" role="img" aria-label="{$safeLabel}">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#071426"/>
      <stop offset="1" stop-color="#143a67"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="760" fill="url(#bg)"/>
  <circle cx="1090" cy="80" r="250" fill="#2d69ab" opacity=".16"/>
  <circle cx="90" cy="720" r="220" fill="#f17a12" opacity=".10"/>
  {$art}
  <rect x="56" y="54" width="118" height="8" rx="4" fill="#f17a12"/>
  <text x="56" y="105" fill="#ffffff" font-family="Arial, sans-serif" font-size="26" font-weight="700">ABCN</text>
  <text x="56" y="690" fill="#b9cce0" font-family="Arial, sans-serif" font-size="18">{$safeLabel}</text>
</svg>
SVG;
    }
}
