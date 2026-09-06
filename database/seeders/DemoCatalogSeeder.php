<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoCatalogSeeder extends Seeder
{
    /**
     * Seed clearly labelled, replaceable catalog content for local development.
     */
    public function run(): void
    {
        $categoryIds = [];

        foreach ($this->rootCategories() as $categoryData) {
            $category = ProductCategory::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                [...$categoryData, 'parent_id' => null],
            );

            $categoryIds[$category->slug] = $category->id;
        }

        foreach ($this->childCategories() as $categoryData) {
            $parentSlug = $categoryData['parent'];
            unset($categoryData['parent']);

            $category = ProductCategory::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                [...$categoryData, 'parent_id' => $categoryIds[$parentSlug]],
            );

            $categoryIds[$category->slug] = $category->id;
        }

        foreach ($this->products() as $productData) {
            $categorySlug = $productData['category'];
            unset($productData['category']);

            $product = Product::query()->updateOrCreate(
                ['slug' => $productData['slug']],
                [...$productData, 'product_category_id' => $categoryIds[$categorySlug]],
            );

            $product->filterAttributes()->delete();
            $product->filterAttributes()->createMany($this->filterAttributesFor($product->slug));
        }
    }

    private function rootCategories(): array
    {
        return [
            [
                'slug' => 'demo-low-voltage',
                'status' => 'published',
                'sort_order' => 10,
                'translations' => [
                    'hy' => ['name' => 'Ցածր լարման սարքավորումներ'],
                    'en' => ['name' => 'Low-voltage equipment'],
                ],
            ],
            [
                'slug' => 'demo-metering-monitoring',
                'status' => 'published',
                'sort_order' => 20,
                'translations' => [
                    'hy' => ['name' => 'Հաշվառում և մոնիթորինգ'],
                    'en' => ['name' => 'Metering & monitoring'],
                ],
            ],
            [
                'slug' => 'demo-ev-charging',
                'status' => 'published',
                'sort_order' => 30,
                'translations' => [
                    'hy' => ['name' => 'Էլեկտրամոբիլների լիցքավորում'],
                    'en' => ['name' => 'EV charging'],
                ],
            ],
        ];
    }

    private function childCategories(): array
    {
        return [
            [
                'parent' => 'demo-low-voltage',
                'slug' => 'demo-main-power-distribution',
                'status' => 'published',
                'sort_order' => 10,
                'translations' => [
                    'hy' => ['name' => 'Գլխավոր էներգաբաշխում'],
                    'en' => ['name' => 'Main power distribution'],
                ],
            ],
            [
                'parent' => 'demo-low-voltage',
                'slug' => 'demo-final-power-distribution',
                'status' => 'published',
                'sort_order' => 20,
                'translations' => [
                    'hy' => ['name' => 'Վերջնական էներգաբաշխում'],
                    'en' => ['name' => 'Final power distribution'],
                ],
            ],
            [
                'parent' => 'demo-low-voltage',
                'slug' => 'demo-industrial-control',
                'status' => 'published',
                'sort_order' => 30,
                'translations' => [
                    'hy' => ['name' => 'Արդյունաբերական կառավարում'],
                    'en' => ['name' => 'Industrial control'],
                ],
            ],
            [
                'parent' => 'demo-metering-monitoring',
                'slug' => 'demo-electricity-meters',
                'status' => 'published',
                'sort_order' => 10,
                'translations' => [
                    'hy' => ['name' => 'Էլեկտրաէներգիայի հաշվիչներ'],
                    'en' => ['name' => 'Electricity meters'],
                ],
            ],
            [
                'parent' => 'demo-ev-charging',
                'slug' => 'demo-ac-chargers',
                'status' => 'published',
                'sort_order' => 10,
                'translations' => [
                    'hy' => ['name' => 'AC լիցքավորման կայաններ'],
                    'en' => ['name' => 'AC charging stations'],
                ],
            ],
        ];
    }

    private function products(): array
    {
        $lowVoltageImage = $this->image(
            '/images/products/demo-low-voltage.webp',
            'Փորձնական ցածր լարման սարքավորումներ',
            'Demo low-voltage equipment',
        );
        $meterImage = $this->image(
            '/images/products/demo-smart-meter.webp',
            'Փորձնական խելացի եռաֆազ հաշվիչ',
            'Demo three-phase smart meter',
        );
        $chargerImages = [
            ['url' => '/images/products/demo-ev-charger.webp', 'alt' => ['hy' => 'Փորձնական պատի լիցքավորման կայան', 'en' => 'Demo wall-mounted EV charger']],
            ['url' => '/images/products/demo-ev-charger-angle.webp', 'alt' => ['hy' => 'Լիցքավորման կայանի կողային տեսք', 'en' => 'Side view of the charging station']],
            ['url' => '/images/products/demo-ev-charger-front.webp', 'alt' => ['hy' => 'Լիցքավորման կայանի դիմային տեսք', 'en' => 'Front view of the charging station']],
            ['url' => '/images/products/demo-ev-charger-detail.webp', 'alt' => ['hy' => 'Լիցքավորման միակցիչի խոշորացված տեսք', 'en' => 'Close-up of the charging connector']],
        ];

        return [
            [
                'category' => 'demo-main-power-distribution',
                'slug' => 'demo-acb-4000',
                'sku' => 'ABCN-DEMO-ACB-4000',
                'status' => 'published',
                'featured' => true,
                'sort_order' => 10,
                'translations' => $this->translations(
                    'DEMO · ACB-4000 օդային ավտոմատ անջատիչ',
                    'DEMO · ACB-4000 air circuit breaker',
                    'Փորձնական կատալոգային գրառում գլխավոր բաշխման հանգույցների էջը ստուգելու համար։ Իրական տեխնիկական տվյալ չէ։',
                    'Demo catalog entry for testing a main-distribution product page. Not approved technical data.',
                ),
                'specifications' => $this->specifications(
                    ['Տիպ' => 'Օդային ավտոմատ անջատիչ', 'Նոմինալ հոսանք' => 'մինչև 4000 A', 'Բևեռներ' => '3P / 4P', 'Աշխատանքային լարում' => 'մինչև 690 V', 'Կատարում' => 'ֆիքսված / հանովի'],
                    ['Type' => 'Air circuit breaker', 'Rated current' => 'up to 4000 A', 'Poles' => '3P / 4P', 'Operating voltage' => 'up to 690 V', 'Installation' => 'fixed / withdrawable'],
                ),
                'images' => $lowVoltageImage,
                'documents' => $this->documents(),
            ],
            [
                'category' => 'demo-main-power-distribution',
                'slug' => 'demo-mccb-250',
                'sku' => 'ABCN-DEMO-MCCB-250',
                'status' => 'published',
                'featured' => false,
                'sort_order' => 20,
                'translations' => $this->translations(
                    'DEMO · MCCB-250 իրանային ավտոմատ անջատիչ',
                    'DEMO · MCCB-250 moulded-case circuit breaker',
                    'Փորձնական ապրանք՝ ցուցակի քարտի, ֆիլտրի և բնութագրերի աղյուսակի աշխատանքի համար։',
                    'Demo product for testing catalog cards, filters and the specifications table.',
                ),
                'specifications' => $this->specifications(
                    ['Նոմինալ հոսանք' => '100-250 A', 'Անջատման ունակություն' => '36 kA', 'Բևեռներ' => '3P / 4P', 'Պաշտպանություն' => 'ջերմամագնիսական'],
                    ['Rated current' => '100-250 A', 'Breaking capacity' => '36 kA', 'Poles' => '3P / 4P', 'Protection' => 'thermal-magnetic'],
                ),
                'images' => $lowVoltageImage,
                'documents' => $this->documents(),
            ],
            [
                'category' => 'demo-final-power-distribution',
                'slug' => 'demo-mcb-63',
                'sku' => 'ABCN-DEMO-MCB-63',
                'status' => 'published',
                'featured' => false,
                'sort_order' => 30,
                'translations' => $this->translations(
                    'DEMO · MCB-63 մոդուլային ավտոմատ անջատիչ',
                    'DEMO · MCB-63 miniature circuit breaker',
                    'Փորձնական գրառում վերջնական էներգաբաշխման ենթակատեգորիայի ցուցադրության համար։',
                    'Demo entry for displaying the final power distribution subcategory.',
                ),
                'specifications' => $this->specifications(
                    ['Նոմինալ հոսանք' => '6-63 A', 'Կորեր' => 'B / C / D', 'Բևեռներ' => '1P-4P', 'Անջատման ունակություն' => '6 kA'],
                    ['Rated current' => '6-63 A', 'Tripping curves' => 'B / C / D', 'Poles' => '1P-4P', 'Breaking capacity' => '6 kA'],
                ),
                'images' => $lowVoltageImage,
                'documents' => $this->documents(),
            ],
            [
                'category' => 'demo-final-power-distribution',
                'slug' => 'demo-rccb-63',
                'sku' => 'ABCN-DEMO-RCCB-63',
                'status' => 'published',
                'featured' => false,
                'sort_order' => 40,
                'translations' => $this->translations(
                    'DEMO · RCCB-63 պաշտպանիչ անջատիչ',
                    'DEMO · RCCB-63 residual-current circuit breaker',
                    'Փորձնական ապրանք՝ նույն ենթակատեգորիայում մի քանի քարտերի դասավորությունը ստուգելու համար։',
                    'Demo product for testing multiple cards within the same subcategory.',
                ),
                'specifications' => $this->specifications(
                    ['Նոմինալ հոսանք' => '25-63 A', 'Դիֆերենցիալ հոսանք' => '30 / 100 / 300 mA', 'Տիպ' => 'AC / A', 'Բևեռներ' => '2P / 4P'],
                    ['Rated current' => '25-63 A', 'Residual current' => '30 / 100 / 300 mA', 'Type' => 'AC / A', 'Poles' => '2P / 4P'],
                ),
                'images' => $lowVoltageImage,
                'documents' => $this->documents(),
            ],
            [
                'category' => 'demo-industrial-control',
                'slug' => 'demo-contactor-c95',
                'sku' => 'ABCN-DEMO-C95',
                'status' => 'published',
                'featured' => false,
                'sort_order' => 50,
                'translations' => $this->translations(
                    'DEMO · C-95 կոնտակտոր',
                    'DEMO · C-95 contactor',
                    'Փորձնական արդյունաբերական կառավարման ապրանք՝ տեխնիկական էջի փորձարկման համար։',
                    'Demo industrial-control product for testing the technical detail page.',
                ),
                'specifications' => $this->specifications(
                    ['Աշխատանքային հոսանք' => '9-95 A', 'Օգտագործման կատեգորիա' => 'AC-3', 'Կոճի լարում' => '24 / 110 / 230 / 400 V', 'Օժանդակ կոնտակտներ' => '1NO + 1NC'],
                    ['Operational current' => '9-95 A', 'Utilization category' => 'AC-3', 'Coil voltage' => '24 / 110 / 230 / 400 V', 'Auxiliary contacts' => '1NO + 1NC'],
                ),
                'images' => $lowVoltageImage,
                'documents' => $this->documents(),
            ],
            [
                'category' => 'demo-industrial-control',
                'slug' => 'demo-vfd-15',
                'sku' => 'ABCN-DEMO-VFD-15',
                'status' => 'published',
                'featured' => false,
                'sort_order' => 60,
                'translations' => $this->translations(
                    'DEMO · VFD-15 հաճախականային փոխակերպիչ',
                    'DEMO · VFD-15 variable-frequency drive',
                    'Փորձնական ապրանք շարժիչների կառավարման լուծումների կատալոգային տեսքը ստուգելու համար։',
                    'Demo product for testing the catalog presentation of motor-control solutions.',
                ),
                'specifications' => $this->specifications(
                    ['Հզորություն' => 'մինչև 15 kW', 'Մուտքային լարում' => '3 × 400 V', 'Կառավարում' => 'V/F և վեկտորային', 'Կապ' => 'RS-485 / Modbus'],
                    ['Power' => 'up to 15 kW', 'Input voltage' => '3 × 400 V', 'Control' => 'V/F and vector', 'Communication' => 'RS-485 / Modbus'],
                ),
                'images' => $lowVoltageImage,
                'documents' => $this->documents(),
            ],
            [
                'category' => 'demo-electricity-meters',
                'slug' => 'demo-smart-meter-sm320',
                'sku' => 'ABCN-DEMO-SM320',
                'status' => 'published',
                'featured' => true,
                'sort_order' => 70,
                'translations' => $this->translations(
                    'DEMO · SM-320 խելացի եռաֆազ հաշվիչ',
                    'DEMO · SM-320 three-phase smart meter',
                    'Փորձնական հաշվիչ՝ չափման և մոնիթորինգի կատեգորիայի էջը տեսնելու համար։',
                    'Demo meter for previewing the metering and monitoring category page.',
                ),
                'specifications' => $this->specifications(
                    ['Ցանց' => 'եռաֆազ', 'Միացում' => 'ուղղակի / CT', 'Ճշտության դաս' => 'Class 1', 'Կապ' => 'RS-485 / Modbus'],
                    ['Network' => 'three-phase', 'Connection' => 'direct / CT', 'Accuracy class' => 'Class 1', 'Communication' => 'RS-485 / Modbus'],
                ),
                'images' => $meterImage,
                'documents' => $this->documents(),
            ],
            [
                'category' => 'demo-ac-chargers',
                'slug' => 'demo-ev-wallbox-22',
                'sku' => 'ABCN-DEMO-EV22',
                'status' => 'published',
                'featured' => true,
                'sort_order' => 80,
                'translations' => $this->translations(
                    'DEMO · EV-22 պատի լիցքավորման կայան',
                    'DEMO · EV-22 wall-mounted charging station',
                    'Փորձնական պատի AC կայան․ մալուխը պատի պահիչի վրա է և չի անցնում հատակով։',
                    'Demo wall-mounted AC charger with the cable stored on its wall holder, clear of the floor.',
                ),
                'specifications' => $this->specifications(
                    ['Հզորություն' => '7.4 / 11 / 22 kW', 'Միակցիչ' => 'Type 2', 'Պաշտպանության աստիճան' => 'IP54', 'Մուտքի տարբերակներ' => 'RFID / հավելված'],
                    ['Power' => '7.4 / 11 / 22 kW', 'Connector' => 'Type 2', 'Protection degree' => 'IP54', 'Access options' => 'RFID / mobile app'],
                ),
                'images' => $chargerImages,
                'documents' => $this->documents(),
            ],
        ];
    }

    private function translations(string $hyName, string $enName, string $hyDescription, string $enDescription): array
    {
        return [
            'hy' => ['name' => $hyName, 'description' => $hyDescription],
            'en' => ['name' => $enName, 'description' => $enDescription],
        ];
    }

    private function specifications(array $hy, array $en): array
    {
        return ['hy' => $hy, 'en' => $en];
    }

    private function filterAttributesFor(string $productSlug): array
    {
        return match ($productSlug) {
            'demo-acb-4000' => $this->filters([
                ['rated-current', 'Նոմինալ հոսանք', 'Rated current', 'մինչև 4000 A', 'up to 4000 A'],
                ['poles', 'Բևեռներ', 'Poles', '3P / 4P', '3P / 4P'],
            ]),
            'demo-mccb-250' => $this->filters([
                ['rated-current', 'Նոմինալ հոսանք', 'Rated current', '100-250 A', '100-250 A'],
                ['poles', 'Բևեռներ', 'Poles', '3P / 4P', '3P / 4P'],
            ]),
            'demo-mcb-63' => $this->filters([
                ['rated-current', 'Նոմինալ հոսանք', 'Rated current', '6-63 A', '6-63 A'],
                ['poles', 'Բևեռներ', 'Poles', '1P-4P', '1P-4P'],
            ]),
            'demo-rccb-63' => $this->filters([
                ['rated-current', 'Նոմինալ հոսանք', 'Rated current', '25-63 A', '25-63 A'],
                ['poles', 'Բևեռներ', 'Poles', '2P / 4P', '2P / 4P'],
            ]),
            'demo-contactor-c95' => $this->filters([
                ['operational-current', 'Աշխատանքային հոսանք', 'Operational current', '9-95 A', '9-95 A'],
                ['utilization-category', 'Օգտագործման կատեգորիա', 'Utilization category', 'AC-3', 'AC-3'],
            ]),
            'demo-vfd-15' => $this->filters([
                ['power', 'Հզորություն', 'Power', 'մինչև 15 kW', 'up to 15 kW'],
                ['communication', 'Կապ', 'Communication', 'RS-485 / Modbus', 'RS-485 / Modbus'],
            ]),
            'demo-smart-meter-sm320' => $this->filters([
                ['network', 'Ցանց', 'Network', 'եռաֆազ', 'three-phase'],
                ['communication', 'Կապ', 'Communication', 'RS-485 / Modbus', 'RS-485 / Modbus'],
            ]),
            'demo-ev-wallbox-22' => $this->filters([
                ['power', 'Հզորություն', 'Power', '7.4 / 11 / 22 kW', '7.4 / 11 / 22 kW'],
                ['connector', 'Միակցիչ', 'Connector', 'Type 2', 'Type 2'],
            ]),
            default => [],
        };
    }

    private function filters(array $definitions): array
    {
        return array_map(
            fn (array $definition, int $index) => [
                'key' => $definition[0],
                'option' => Str::slug($definition[4]),
                'label' => ['hy' => $definition[1], 'en' => $definition[2]],
                'value' => ['hy' => $definition[3], 'en' => $definition[4]],
                'sort_order' => $index,
            ],
            $definitions,
            array_keys($definitions),
        );
    }

    private function image(string $url, string $hyAlt, string $enAlt): array
    {
        return [['url' => $url, 'alt' => ['hy' => $hyAlt, 'en' => $enAlt]]];
    }

    private function documents(): array
    {
        return [[
            'url' => '/documents/abcn-demo-product-sheet.pdf',
            'name' => 'Demo product sheet / Փորձնական թերթիկ',
        ]];
    }
}
