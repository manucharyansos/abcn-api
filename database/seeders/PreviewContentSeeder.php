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
                'Էլեկտրական նախագծում և հաշվարկներ',
                'Electrical design & calculations',
                'Բեռների հաշվարկ, միագիծ սխեմաներ, մալուխների և պաշտպանիչ սարքերի ընտրություն՝ նախագծի պահանջներին համապատասխան։',
                'Load calculations, single-line diagrams, cable sizing and protection-device selection aligned with project requirements.',
                101,
                '/images/abcn-hero.webp',
                'Էլեկտրական նախագծում'
            ),
            $this->editorial(
                'preview-panel-integration',
                'Էլեկտրական վահանակների լուծումներ',
                'Electrical panel solutions',
                'Գլխավոր և վերջնական բաշխիչ վահանակների, կառավարման վահանակների և դրանց բաղադրիչների տեխնիկական ընտրություն ու համակարգում։',
                'Technical selection and coordination of main and final distribution panels, control panels and their components.',
                102,
                '/images/products/demo-low-voltage.webp',
                'Էլեկտրական վահանակների լուծումներ'
            ),
            $this->editorial(
                'preview-automation-control',
                'Ավտոմատացում և կառավարում',
                'Automation & control',
                'Կառավարման, պաշտպանության և ավտոմատացման սարքերի ընտրություն՝ արտադրական ու ինժեներական համակարգերի համար։',
                'Selection of control, protection and automation equipment for industrial and engineering systems.',
                103,
                '/images/products/demo-low-voltage.webp',
                'Ավտոմատացում և կառավարում'
            ),
            $this->editorial(
                'preview-metering-monitoring',
                'Էներգիայի հաշվառում և մոնիթորինգ',
                'Energy metering & monitoring',
                'Սպառման, բեռների և համակարգի աշխատանքի տվյալների հավաքագրում՝ հաշվիչների և մոնիթորինգի լուծումների միջոցով։',
                'Collection of consumption, load and system-performance data using metering and monitoring solutions.',
                104,
                '/images/products/demo-smart-meter.webp',
                'Էներգիայի հաշվառում և մոնիթորինգ'
            ),
            $this->editorial(
                'preview-equipment-selection',
                'Տեխնիկական խորհրդատվություն և սարքավորումների ընտրություն',
                'Technical consulting & equipment selection',
                'Տեխնիկական պահանջների հստակեցում, համատեղելի սարքավորումների ընտրություն և առաջարկվող լուծման փաստաթղթավորում։',
                'Clarification of technical requirements, selection of compatible equipment and documentation of the proposed solution.',
                105,
                '/images/products/demo-low-voltage.webp',
                'Տեխնիկական խորհրդատվություն'
            ),
            $this->editorial(
                'preview-commissioning',
                'Գործարկում և տեխնիկական ստուգում',
                'Commissioning & technical verification',
                'Հավաքված համակարգերի և վահանակների գործարկման նախապատրաստում, հիմնական ստուգումներ և տեխնիկական հանձնման աջակցություն։',
                'Commissioning preparation, essential verification and technical handover support for assembled systems and panels.',
                106,
                '/images/abcn-hero.webp',
                'Գործարկում և տեխնիկական ստուգում'
            ),
        ];
    }

    /**
     * @param array<string, string> $media
     */
    private function projects(array $media): array
    {
        $items = [
            ['preview-project-01', 'Արտադրական օբյեկտի գլխավոր էներգաբաշխում', 'Main power distribution for an industrial facility', 'Գլխավոր մուտքից մինչև բաշխիչ վահանակներ՝ բեռների բաժանում, պաշտպանություն և համակարգի կառուցվածքի ընտրություն։', 'From the main incomer to distribution panels: load allocation, protection and system architecture.', '/images/abcn-hero.webp'],
            ['preview-project-02', 'Բիզնես կենտրոնի էլեկտրական ենթակառուցվածք', 'Electrical infrastructure for a business center', 'Գլխավոր և հարկային վահանակների, մալուխային գծերի ու պաշտպանիչ սարքերի համակցված լուծում։', 'Integrated solution for main and floor panels, cable routes and protective devices.', '/images/products/demo-low-voltage.webp'],
            ['preview-project-03', 'Էներգիայի հաշվառման և մոնիթորինգի համակարգ', 'Energy metering and monitoring system', 'Եռաֆազ հաշվիչներ, տվյալների հավաքագրում և սպառման վերահսկման կառուցվածք՝ մեկ միասնական համակարգում։', 'Three-phase meters, data collection and consumption monitoring within one integrated system.', '/images/products/demo-smart-meter.webp'],
            ['preview-project-04', 'EV լիցքավորման կայանների լուծում', 'EV charging station solution', 'Լիցքավորման կետերի հզորության ընտրություն, պաշտպանություն, բեռների բաշխում և կայանների միացման սխեմա։', 'Charger power selection, protection, load distribution and connection architecture.', '/images/products/demo-ev-charger.webp'],
            ['preview-project-05', 'Ավտոմատացման կառավարման վահանակ', 'Automation control panel', 'Կոնտակտորների, պաշտպանիչ սարքերի և կառավարման բաղադրիչների համադրում մեկ կառավարման վահանակում։', 'Integration of contactors, protection and control components in a single automation panel.', '/images/products/demo-low-voltage.webp'],
            ['preview-project-06', 'Առևտրային տարածքի վերջնական էներգաբաշխում', 'Final power distribution for a retail facility', 'Լուսավորության, վարդակային խմբերի և սարքավորումների սնուցման վերջնական բաշխման կառուցվածք։', 'Final distribution architecture for lighting, socket circuits and equipment power supply.', '/images/products/demo-low-voltage.webp'],
        ];

        return array_map(function ($item, $index) {
            return [
                ...$this->editorial(
                    $item[0],
                    $item[1],
                    $item[2],
                    $item[3],
                    $item[4],
                    200 + $index,
                    $item[5],
                    'ABCN engineering project'
                ),
                'completed_at' => now()->subDays(30 + ($index * 18))->toDateString(),
            ];
        }, $items, array_keys($items));
    }

    /**
     * @param array<string, string> $media
     */
    private function news(array $media): array
    {
        $items = [
            ['preview-news-01', 'Ինչ հաշվի առնել գլխավոր բաշխիչ վահանակ ընտրելիս', 'What to consider when selecting a main distribution panel', 'Հզորությունը միայն մեկնարկային կետն է․ կարևոր են նաև կարճ միացման մակարդակը, սելեկտիվությունը, պահուստը և սպասարկման պայմանները։', 'Power rating is only the starting point; short-circuit level, selectivity, reserve capacity and maintenance conditions also matter.', '/images/products/demo-low-voltage.webp'],
            ['preview-news-02', 'Էլեկտրաէներգիայի հաշվառում․ ինչ տվյալներ են իրականում կարևոր', 'Energy metering: which data actually matters', 'Ակտիվ էներգիայից բացի համակարգերը կարող են տալ բեռի, լարման, հոսանքի և աշխատանքի պատմության օգտակար տվյալներ։', 'Beyond active energy, modern systems can provide useful load, voltage, current and operating-history data.', '/images/products/demo-smart-meter.webp'],
            ['preview-news-03', 'EV լիցքավորման կայան՝ 7.4, 11 թե 22 kW', 'EV charging: 7.4, 11 or 22 kW', 'Հզորության ընտրությունը կախված է ցանցի հնարավորությունից, մեքենայի onboard charger-ից և օգտագործման սցենարից։', 'Power selection depends on grid capacity, the vehicle onboard charger and the expected usage scenario.', '/images/products/demo-ev-charger-angle.webp'],
            ['preview-news-04', 'Ավտոմատ անջատիչների ընտրության հիմնական չափանիշները', 'Key criteria for circuit-breaker selection', 'Նոմինալ հոսանքը բավարար չէ․ պետք է գնահատել նաև անջատման ունակությունը, բնութագիրը և համակարգի սելեկտիվությունը։', 'Rated current is not enough; breaking capacity, trip characteristics and system selectivity must also be evaluated.', '/images/products/demo-low-voltage.webp'],
            ['preview-news-05', 'Արդյունաբերական ավտոմատացում․ որտեղից սկսել', 'Industrial automation: where to start', 'Լավ ավտոմատացումը սկսվում է գործընթացի, ազդանշանների, անվտանգության պահանջների և սպասվող արդյունքի հստակ նկարագրությունից։', 'Good automation starts with a clear description of the process, signals, safety requirements and expected result.', '/images/abcn-hero.webp'],
            ['preview-news-06', 'Տեխնիկական փաստաթղթերի դերը սարքավորումների ընտրության մեջ', 'The role of technical documentation in equipment selection', 'Datasheet-ը, սխեմաներն ու սերտիֆիկատները օգնում են համեմատել սարքավորումները ոչ թե միայն գնով, այլ կիրառելիությամբ և ռիսկով։', 'Datasheets, diagrams and certificates help compare equipment by application and risk, not only by price.', '/images/products/demo-ev-charger-detail.webp'],
        ];

        return array_map(function ($item, $index) {
            return [
                ...$this->editorial(
                    $item[0],
                    $item[1],
                    $item[2],
                    $item[3],
                    $item[4],
                    300 + $index,
                    $item[5],
                    'ABCN technical article'
                ),
                'published_at' => now()->subDays(7 + ($index * 9)),
            ];
        }, $items, array_keys($items));
    }

    /**
     * @param array<string, string> $media
     */
    private function team(array $media): array
    {
        $items = [
            ['preview-team-01', 'ABCN տեխնիկական թիմ', 'ABCN Engineering Team', 'Նախագծում և տեխնիկական լուծումներ', 'Engineering design & technical solutions'],
            ['preview-team-02', 'Նախագծերի համակարգում', 'Project Coordination', 'Պահանջների, ժամկետների և տեխնիկական փուլերի համակարգում', 'Coordination of requirements, timelines and technical stages'],
            ['preview-team-03', 'Տեխնիկական վաճառք', 'Technical Sales', 'Սարքավորումների ընտրություն և կոմերցիոն առաջարկներ', 'Equipment selection & commercial proposals'],
            ['preview-team-04', 'Հաճախորդների աջակցություն', 'Customer Support', 'Հարցումների ընդունում և հաջորդ քայլերի կազմակերպում', 'Inquiry handling & next-step coordination'],
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
            'images' => $this->image('/images/abcn-logo.png', 'ABCN'),
        ], $items, array_keys($items));
    }

    private function inquiries(): array
    {
        return [
            $this->inquiry('Արամ Մկրտչյան', 'Armat Engineering', 'demo+panel@example.com', 'Պետք է առաջարկ 1600A գլխավոր բաշխիչ վահանակի համար։ Կարո՞ղ եք նաև առաջարկել պաշտպանիչ սարքերը։', 'new'),
            $this->inquiry('Անի Սարգսյան', 'North Business Center', 'demo+metering@example.com', 'Հետաքրքրված ենք բազմակետ էներգիայի հաշվառման և Modbus մոնիթորինգի լուծմամբ։', 'in_progress'),
            $this->inquiry('Դավիթ Գրիգորյան', 'ProLine Systems', 'demo+automation@example.com', 'Նոր արտադրական գծի համար պետք է կառավարման վահանակի տեխնիկական ընտրություն և առաջարկ։', 'new'),
            $this->inquiry('Մարի Հովհաննիսյան', 'Urban Retail', 'demo+distribution@example.com', 'Պետք է վերջնական էներգաբաշխման սարքավորումների ընտրություն առևտրային տարածքի համար։', 'completed'),
        ];
    }

    private function editorial(
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
                    'body' => $hySummary."\n\nՆմուշային բովանդակություն․ այս նկարագրությունը ստեղծված է կայքի տեսքը ստուգելու համար և չի ներկայացնում կոնկրետ ավարտված հաճախորդի նախագիծ։",
                ],
                'en' => [
                    'title' => $enTitle,
                    'summary' => $enSummary,
                    'body' => $enSummary."\n\nSample content: this description is provided to preview the website and does not represent a specific completed client project.",
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
