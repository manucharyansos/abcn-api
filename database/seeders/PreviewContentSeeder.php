<?php

namespace Database\Seeders;

use App\Models\ContactRequest;
use App\Models\NewsArticle;
use App\Models\Project;
use App\Models\Service;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class PreviewContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DemoCatalogSeeder::class);

        foreach ($this->services() as $item) {
            Service::query()->updateOrCreate(['slug' => $item['slug']], $item);
        }

        foreach ($this->projects() as $item) {
            Project::query()->updateOrCreate(['slug' => $item['slug']], $item);
        }

        foreach ($this->news() as $item) {
            NewsArticle::query()->updateOrCreate(['slug' => $item['slug']], $item);
        }

        foreach ($this->team() as $item) {
            TeamMember::query()->updateOrCreate(['slug' => $item['slug']], $item);
        }

        foreach ($this->inquiries() as $item) {
            ContactRequest::query()->updateOrCreate(
                ['email' => $item['email'], 'message' => $item['message']],
                $item,
            );
        }
    }

    private function image(string $name): array
    {
        return [[
            'url' => '/images/abcn-logo.png',
            'name' => $name,
            'alt' => ['hy' => $name, 'en' => $name],
        ]];
    }

    private function services(): array
    {
        return [
            $this->editorial(
                'preview-electrical-design',
                'Էլեկտրական նախագծում',
                'Electrical design',
                'Փորձնական ծառայություն՝ տարբեր երկարության վերնագրերով և նկարագրություններով քարտերի դասավորությունը ստուգելու համար։',
                'Preview service for testing card layout with different title and description lengths.',
                101
            ),
            $this->editorial(
                'preview-panel-integration',
                'Վահանակների հավաքում և ինտեգրում',
                'Panel assembly & integration',
                'Փորձնական ծառայություն՝ ծառայությունների ցուցակի և մանրամասն էջի տեսքը ստուգելու համար։',
                'Preview service for checking the service directory and detail page presentation.',
                102
            ),
            $this->editorial(
                'preview-site-audit',
                'Տեխնիկական աուդիտ և տեղազննում',
                'Technical audit & site survey',
                'Փորձնական ծառայություն երկար տեքստով՝ բջջային տարբերակում տողադարձը և բարձրությունը տեսնելու համար։',
                'Preview service with a longer description to verify wrapping and card height on mobile screens.',
                103
            ),
        ];
    }

    private function projects(): array
    {
        $items = [
            ['preview-project-01', 'Արտադրական գծի էլեկտրամատակարարում', 'Industrial line power distribution', 'Արտադրական գծի համար բաշխման և պաշտպանության փորձնական նախագիծ։', 'Preview project for power distribution and protection of an industrial line.'],
            ['preview-project-02', 'Գրասենյակային շենքի վահանակներ', 'Office building distribution panels', 'Փորձնական նախագիծ՝ մի քանի բաշխիչ վահանակների ինտեգրմամբ։', 'Preview project featuring integration of several distribution panels.'],
            ['preview-project-03', 'Էներգիայի հաշվառման համակարգ', 'Energy metering system', 'Հաշվառման, տվյալների հավաքագրման և մոնիթորինգի փորձնական նախագիծ։', 'Preview project for metering, data collection and monitoring.'],
            ['preview-project-04', 'EV լիցքավորման ենթակառուցվածք', 'EV charging infrastructure', 'Փորձնական նախագիծ՝ լիցքավորման կայանների և պաշտպանության սարքավորումների համար։', 'Preview project for EV chargers and electrical protection equipment.'],
            ['preview-project-05', 'Ավտոմատացման վահանակ', 'Automation control panel', 'Փորձնական ավտոմատացման նախագիծ՝ կառավարման և ազդանշանների ինտեգրմամբ։', 'Preview automation project with control and signal integration.'],
            ['preview-project-06', 'Առևտրային տարածքի էներգաբաշխում', 'Retail facility power distribution', 'Փորձնական նախագիծ՝ քարտերի ցանցը տարբեր քանակներով ստուգելու համար։', 'Preview project used to verify responsive card grids with multiple entries.'],
        ];

        return array_map(function ($item, $index) {
            return [
                ...$this->editorial($item[0], $item[1], $item[2], $item[3], $item[4], 200 + $index),
                'completed_at' => now()->subDays($index + 2)->toDateString(),
            ];
        }, $items, array_keys($items));
    }

    private function news(): array
    {
        $items = [
            ['preview-news-01', 'ABCN-ի փորձնական նորություն', 'ABCN preview news item', 'Փորձնական նորություն՝ գլխավոր էջի և նորությունների ցանցի տեսքը ստուգելու համար։', 'Preview news entry for testing the home page and news grid.'],
            ['preview-news-02', 'Նոր տեխնիկական ուղղություն', 'New technical direction', 'Կարճ փորձնական հրապարակում՝ տարբեր երկարության վերնագրերի համար։', 'Short preview article for testing different title lengths.'],
            ['preview-news-03', 'Նոր գործընկերային նախագիծ', 'New partner project', 'Փորձնական հրապարակում՝ նկարով քարտերի դասավորությունը տեսնելու համար։', 'Preview article for testing cards with images.'],
            ['preview-news-04', 'Էներգախնայողության լուծումների թարմացում', 'Energy efficiency solutions update', 'Ավելի երկար փորձնական տեքստ՝ բջջային էջում տողադարձը ստուգելու համար։', 'Longer preview text for testing wrapping on narrow mobile screens.'],
            ['preview-news-05', 'Ապրանքային կատալոգի թարմացում', 'Product catalog update', 'Փորձնական նորություն կատալոգի զարգացման մասին։', 'Preview news entry about catalog development.'],
            ['preview-news-06', 'ABCN թիմի նոր թարմացում', 'ABCN team update', 'Փորձնական հրապարակում՝ մի քանի նորություններով էջի ամբողջական տեսքը ստուգելու համար։', 'Preview entry for checking a full news page with several cards.'],
        ];

        return array_map(function ($item, $index) {
            return [
                ...$this->editorial($item[0], $item[1], $item[2], $item[3], $item[4], 300 + $index),
                'published_at' => now()->subHours($index + 1),
            ];
        }, $items, array_keys($items));
    }

    private function team(): array
    {
        $items = [
            ['preview-team-01', 'Թիմի անդամ 1', 'Team member 1', 'Տեխնիկական ղեկավար', 'Technical lead'],
            ['preview-team-02', 'Թիմի անդամ 2', 'Team member 2', 'Նախագծերի ղեկավար', 'Project manager'],
            ['preview-team-03', 'Թիմի անդամ 3', 'Team member 3', 'Ինժեներ', 'Engineer'],
            ['preview-team-04', 'Թիմի անդամ երկար անունով', 'Team member with a longer name', 'Տեխնիկական վաճառքի մասնագետ', 'Technical sales specialist'],
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
            'images' => $this->image('ABCN preview team'),
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

    private function editorial(string $slug, string $hyTitle, string $enTitle, string $hySummary, string $enSummary, int $sortOrder): array
    {
        return [
            'slug' => $slug,
            'status' => 'published',
            'show_on_homepage' => true,
            'sort_order' => $sortOrder,
            'translations' => [
                'hy' => [
                    'title' => $hyTitle,
                    'summary' => $hySummary,
                    'body' => $hySummary . ' Սա փորձնական բովանդակություն է և կարող է ջնջվել ադմինից։',
                ],
                'en' => [
                    'title' => $enTitle,
                    'summary' => $enSummary,
                    'body' => $enSummary . ' This is preview content and can be removed from the admin panel.',
                ],
            ],
            'images' => $this->image('ABCN preview content'),
        ];
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
}
