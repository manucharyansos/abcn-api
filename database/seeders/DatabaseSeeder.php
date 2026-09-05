<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if ($email && $password) {
            User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => env('ADMIN_NAME', 'ABCN Administrator'),
                    'password' => $password,
                    'role' => 'admin',
                ],
            );
        }

        foreach ($this->pages() as $slug => $content) {
            $page = Page::query()->firstOrNew(['slug' => $slug]);

            if (! $page->exists || $this->isPlaceholder($page->content)) {
                $page->fill([
                    'status' => 'published',
                    'content' => $content,
                    'meta' => [
                        'hy' => ['title' => $content['hy']['title'], 'description' => $content['hy']['lead']],
                        'en' => ['title' => $content['en']['title'], 'description' => $content['en']['lead']],
                    ],
                ])->save();
            }
        }
    }

    private function isPlaceholder(?array $content): bool
    {
        return count($content['hy'] ?? []) <= 1 && count($content['en'] ?? []) <= 1;
    }

    private function pages(): array
    {
        return [
            'home' => [
                'hy' => [
                    'eyebrow' => 'ԻՆԺԵՆԵՐԱԿԱՆ ԵՎ ԷՆԵՐԳԵՏԻԿ ԼՈՒԾՈՒՄՆԵՐ',
                    'title' => 'Հուսալի կապ՝ կարևոր համակարգերի համար։',
                    'lead' => 'ABCN-ը ինժեներական մոտեցմամբ միավորում է էլեկտրական ենթակառուցվածքները, արդյունաբերական համակարգերն ու դրանք կապող տեխնոլոգիաները։',
                    'body' => 'Յուրաքանչյուր աշխատանք կառուցում ենք նախագծի իրական պայմանների շուրջ՝ կիրառություն, անվտանգություն, համատեղելիություն և երկարաժամկետ շահագործում։',
                ],
                'en' => [
                    'eyebrow' => 'ENGINEERING & ENERGY SOLUTIONS',
                    'title' => 'Reliable connections for systems that matter.',
                    'lead' => 'ABCN brings an engineering mindset to electrical infrastructure, industrial systems and the technologies that connect them.',
                    'body' => 'We structure every engagement around the real conditions of the project: application, safety, compatibility and long-term operation.',
                ],
            ],
            'about' => [
                'hy' => [
                    'eyebrow' => 'ABCN-Ի ՄԱՍԻՆ',
                    'title' => 'Ինժեներական հստակություն։ Պատասխանատու կապեր։',
                    'lead' => 'ABCN-ը Հայաստանում գործող ընկերություն է, որը կենտրոնանում է էլեկտրական տեխնոլոգիաների, ենթակառուցվածքային պահանջների և նախագծերի գործնական իրականացման կապի վրա։',
                    'body' => 'Մեր դերն է տեխնիկական բաղադրիչները միավորել մեկ հստակ, համապատասխան և հուսալի համակարգում։',
                ],
                'en' => [
                    'eyebrow' => 'ABOUT ABCN',
                    'title' => 'Engineering clarity. Responsible connections.',
                    'lead' => 'ABCN is an Armenia-based company focused on the connection between electrical technologies, infrastructure requirements and practical project execution.',
                    'body' => 'Our role is to connect technical components into one clear, appropriate and dependable system.',
                ],
            ],
            'solutions' => [
                'hy' => [
                    'eyebrow' => 'ԼՈՒԾՈՒՄՆԵՐ',
                    'title' => 'Սկսում ենք խնդրից, ոչ թե ապրանքների ցանկից։',
                    'lead' => 'Լուծումների բաժինը նախագծված է իրական կարիքը համապատասխան ինժեներական ուղղության, ապրանքների և փաստաթղթերի հետ կապելու համար։',
                    'body' => 'Յուրաքանչյուր ուղղություն ձևավորվում է կիրառության, շահագործման միջավայրի և ակնկալվող արդյունքի շուրջ՝ նախագծին համապատասխան սարքավորումներով ու տեխնիկական փաստաթղթերով։',
                ],
                'en' => [
                    'eyebrow' => 'SOLUTIONS',
                    'title' => 'Start from the challenge, not from a product list.',
                    'lead' => 'The solution section connects a real project need with the relevant engineering direction, products and documentation.',
                    'body' => 'Each direction is shaped around the application, operating environment and required result, with equipment and technical documents selected for the project.',
                ],
            ],
            'products' => [
                'hy' => [
                    'eyebrow' => 'ԱՊՐԱՆՔՆԵՐ',
                    'title' => 'Տեխնիկական կատալոգ՝ հիմնավորված ընտրության համար։',
                    'lead' => 'Կատալոգը նախատեսված է կատեգորիաների, բնութագրերի, սերտիֆիկատների, ձեռնարկների և գնային առաջարկի հարցումների համար։',
                    'body' => 'Յուրաքանչյուր ապրանքի էջից հնարավոր կլինի հարցնել առկայության, ընտրության կամ կոմերցիոն առաջարկի մասին՝ առանց օնլայն վճարման։',
                ],
                'en' => [
                    'eyebrow' => 'PRODUCTS',
                    'title' => 'A technical catalog designed for informed selection.',
                    'lead' => 'The catalog supports categories, specifications, certificates, manuals and quote requests.',
                    'body' => 'Each product page will provide a direct way to ask about availability, selection support or a commercial offer without online payment.',
                ],
            ],
            'contact' => [
                'hy' => [
                    'eyebrow' => 'ԿԱՊ',
                    'title' => 'Միասին հստակեցնենք ճիշտ հաջորդ քայլը։',
                    'lead' => 'Ուղարկեք ձեր հարցի կամ նախագծի կարճ նկարագրությունը։ Մեր թիմը կուսումնասիրի այն և կկապվի ձեզ հետ՝ հաջորդ քայլը հստակեցնելու համար։',
                    'body' => 'Կարող եք նաև կապվել մեզ հետ հեռախոսով կամ էլ․ փոստով։',
                ],
                'en' => [
                    'eyebrow' => 'CONTACT',
                    'title' => 'Let’s define the right next step.',
                    'lead' => 'Send a short description of your question or project. Our team will review it and contact you to clarify the next step.',
                    'body' => 'You can also contact us by phone or email.',
                ],
            ],
        ];
    }
}
