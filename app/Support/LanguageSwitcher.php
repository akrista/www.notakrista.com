<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\File;

final class LanguageSwitcher
{
    /** @var array<int, array{code: string, name?: string}>|null */
    private static ?array $locales = null;

    /**
     * @return array{otherLanguages: array<int, array{code: string, name: string}>, currentLanguage: array{code: string, name: string}|null, shouldShow: bool}
     */
    public static function getViewData(): array
    {
        $locales = self::getLocales();
        $currentLocale = app()->getLocale();
        $currentLanguage = collect($locales)->firstWhere('code', $currentLocale);

        return [
            'otherLanguages' => $locales,
            'currentLanguage' => $currentLanguage,
            'shouldShow' => count($locales) > 1,
        ];
    }

    public static function shouldShow(): bool
    {
        return count(self::getLocales()) > 1;
    }

    /**
     * @param  array<int, array{code: string, name?: string}>  $locales
     */
    public static function setLocales(array $locales): void
    {
        self::$locales = $locales;
    }

    /**
     * @return array<int, array{code: string, name: string}>
     */
    private static function getLocales(): array
    {
        $locales = self::$locales ?? config('fillakit.locales', [['code' => 'en']]);

        if ($locales !== []) {
            return array_map(function (array $locale): array {
                if (! isset($locale['name'])) {
                    $locale['name'] = self::getLanguageName($locale['code']);
                }

                return $locale;
            }, $locales);
        }

        return self::getFilamentLocales();
    }

    /**
     * @return array<int, array{code: string, name: string}>
     */
    private static function getFilamentLocales(): array
    {
        $filamentLangPath = base_path('vendor/filament/filament/resources/lang');
        $locales = [];

        if (! File::isDirectory($filamentLangPath)) {
            return $locales;
        }

        $directories = File::directories($filamentLangPath);

        foreach ($directories as $directory) {
            $localeCode = basename((string) $directory);

            if ($localeCode === 'vendor') {
                continue;
            }

            $locales[] = [
                'code' => $localeCode,
                'name' => self::getLanguageName($localeCode),
            ];
        }

        return $locales;
    }

    private static function getLanguageName(string $localeCode): string
    {
        $languageNames = [
            'af' => 'Afrikaans',
            'am' => 'አማርኛ',
            'ar' => 'العربية',
            'as' => 'অসমীয়া',
            'az' => 'Azərbaycan',
            'be' => 'Беларуская',
            'bg' => 'Български',
            'bn' => 'বাংলা',
            'bo' => 'བོད་ཡིག',
            'bs' => 'Bosanski',
            'ca' => 'Català',
            'ckb' => 'کوردی',
            'cs' => 'Čeština',
            'cy' => 'Cymraeg',
            'da' => 'Dansk',
            'de' => 'Deutsch',
            'dv' => 'ދިވެހި',
            'el' => 'Ελληνικά',
            'en' => 'English',
            'eo' => 'Esperanto',
            'es' => 'Español',
            'et' => 'Eesti',
            'eu' => 'Euskera',
            'fa' => 'فارسی',
            'fi' => 'Suomi',
            'fo' => 'Føroyskt',
            'fr' => 'Français',
            'fy' => 'Frysk',
            'ga' => 'Gaeilge',
            'gd' => 'Gàidhlig',
            'gl' => 'Galego',
            'gu' => 'ગુજરાતી',
            'ha' => 'Hausa',
            'he' => 'עברית',
            'hi' => 'हिन्दी',
            'hr' => 'Hrvatski',
            'hu' => 'Magyar',
            'hy' => 'Հայdelays',
            'id' => 'Indonesia',
            'ig' => 'Igbo',
            'is' => 'Íslenska',
            'it' => 'Italiano',
            'ja' => '日本語',
            'jv' => 'Basa Jawa',
            'ka' => 'ქართული',
            'kk' => 'Қазақ',
            'km' => 'ខ្មែរ',
            'kn' => 'ಕನ್ನಡ',
            'ko' => '한국어',
            'ku' => 'کوردی',
            'ky' => 'Кыргызча',
            'la' => 'Latina',
            'lb' => 'Lëtzebuergesch',
            'lo' => 'ລາວ',
            'lt' => 'Lietuvių',
            'lv' => 'Latviešu',
            'mg' => 'Malagasy',
            'mk' => 'Македонски',
            'ml' => 'മലയാളം',
            'mn' => 'Монгол',
            'mr' => 'मराठी',
            'ms' => 'Bahasa Malaysia',
            'mt' => 'Malti',
            'my' => 'မြန်မာ',
            'nb' => 'Norsk (Bokmål)',
            'nd' => 'isiNdebele',
            'ne' => 'नेपाली',
            'nl' => 'Nederlands',
            'nn' => 'Norsk (Nynorsk)',
            'no' => 'Norsk',
            'ny' => 'Chichewa',
            'or' => 'ଓଡ଼ିଆ',
            'pa' => 'ਪੰਜਾਬੀ',
            'pl' => 'Polski',
            'ps' => 'پښتو',
            'pt' => 'Português',
            'qu' => 'Runa Simi',
            'ro' => 'Română',
            'ru' => 'Русский',
            'rw' => 'Kinyarwanda',
            'sa' => 'संस्कृतम्',
            'sd' => 'سنڌي',
            'se' => 'Davvisámegiella',
            'si' => 'සිංහල',
            'sk' => 'Slovenčina',
            'sl' => 'Slovenščina',
            'sm' => 'Gagana Samoa',
            'sn' => 'ChiShona',
            'so' => 'Soomaali',
            'sq' => 'Shqip',
            'sr' => 'Српски',
            'st' => 'Sesotho',
            'su' => 'Basa Sunda',
            'sv' => 'Svenska',
            'sw' => 'Kiswahili',
            'ta' => 'தமிழ்',
            'te' => 'తెలుగు',
            'tg' => 'Тоҷикӣ',
            'th' => 'ไทย',
            'tk' => 'Türkmen',
            'tl' => 'Filipino',
            'tn' => 'Setswana',
            'to' => 'Lea Faka-Tonga',
            'tr' => 'Türkçe',
            'ts' => 'Xitsonga',
            'tt' => 'Татар',
            'tw' => 'Twi',
            'ty' => 'Reo Tahiti',
            'ug' => 'ئۇيغۇر',
            'uk' => 'Українська',
            'ur' => 'اردو',
            'uz' => "O'zbek",
            've' => 'Tshivenḓa',
            'vi' => 'Tiếng Việt',
            'wo' => 'Wolof',
            'xh' => 'isiXhosa',
            'yi' => 'ייִדיש',
            'yo' => 'Yorùbá',
            'zh' => '中文',
            'zu' => 'isiZulu',
            'zh_CN' => '简体中文',
            'zh_TW' => '繁體中文',
            'zh_HK' => '繁體中文 (香港)',
            'zh_SG' => '简体中文 (新加坡)',
            'zh_MO' => '繁體中文 (澳門)',
            'en_US' => 'English (United States)',
            'en_GB' => 'English (United Kingdom)',
            'en_AU' => 'English (Australia)',
            'en_CA' => 'English (Canada)',
            'en_IE' => 'English (Ireland)',
            'en_NZ' => 'English (New Zealand)',
            'en_ZA' => 'English (South Africa)',
            'en_IN' => 'English (India)',
            'fr_CA' => 'Français (Canada)',
            'fr_CH' => 'Français (Suisse)',
            'fr_BE' => 'Français (Belgique)',
            'es_MX' => 'Español (México)',
            'es_AR' => 'Español (Argentina)',
            'es_CO' => 'Español (Colombia)',
            'es_CL' => 'Español (Chile)',
            'es_PE' => 'Español (Perú)',
            'es_VE' => 'Español (Venezuela)',
            'es_UY' => 'Español (Uruguay)',
            'es_PY' => 'Español (Paraguay)',
            'es_BO' => 'Español (Bolivia)',
            'es_EC' => 'Español (Ecuador)',
            'es_GT' => 'Español (Guatemala)',
            'es_HN' => 'Español (Honduras)',
            'es_SV' => 'Español (El Salvador)',
            'es_NI' => 'Español (Nicaragua)',
            'es_CR' => 'Español (Costa Rica)',
            'es_PA' => 'Español (Panamá)',
            'es_DO' => 'Español (República Dominicana)',
            'es_PR' => 'Español (Puerto Rico)',
            'es_CU' => 'Español (Cuba)',
            'pt_BR' => 'Português (Brasil)',
            'pt_PT' => 'Português (Portugal)',
            'pt_AO' => 'Português (Angola)',
            'pt_MZ' => 'Português (Moçambique)',
            'de_AT' => 'Deutsch (Österreich)',
            'de_CH' => 'Deutsch (Schweiz)',
            'de_LU' => 'Deutsch (Luxemburg)',
            'de_LI' => 'Deutsch (Liechtenstein)',
            'it_CH' => 'Italiano (Svizzera)',
            'it_SM' => 'Italiano (San Marino)',
            'it_VA' => 'Italiano (Vaticano)',
            'nl_BE' => 'Nederlands (België)',
            'nl_SR' => 'Nederlands (Suriname)',
            'ar_EG' => 'العربية (مصر)',
            'ar_SA' => 'العربية (السعودية)',
            'ar_AE' => 'العربية (الإمارات)',
            'ar_JO' => 'العربية (الأردن)',
            'ar_LB' => 'العربية (لبنان)',
            'ar_SY' => 'العربية (سوريا)',
            'ar_IQ' => 'العربية (العراق)',
            'ar_KW' => 'العربية (الكويت)',
            'ar_QA' => 'العربية (قطر)',
            'ar_BH' => 'العربية (البحرين)',
            'ar_OM' => 'العربية (عمان)',
            'ar_YE' => 'العربية (اليمن)',
            'ar_MA' => 'العربية (المغرب)',
            'ar_TN' => 'العربية (تونس)',
            'ar_DZ' => 'العربية (الجزائر)',
            'ar_LY' => 'العربية (ليبيا)',
            'ar_SD' => 'العربية (السودان)',
            'ru_RU' => 'Русский (Россия)',
            'ru_BY' => 'Русский (Беларусь)',
            'ru_KZ' => 'Русский (Казахстан)',
            'ru_KG' => 'Русский (Кыргызстан)',
            'ru_UA' => 'Русский (Украина)',
            'hi_IN' => 'हिन्दी (भारत)',
            'bn_BD' => 'বাংলা (বাংলাদেশ)',
            'bn_IN' => 'বাংলা (ভারত)',
            'ta_IN' => 'தமிழ் (இந்தியா)',
            'ta_LK' => 'தமிழ் (இலங்கை)',
            'te_IN' => 'తెలుగు (భారతదేశం)',
            'ml_IN' => 'മലയാളം (ഇന്ത്യ)',
            'kn_IN' => 'ಕನ್ನಡ (ಭಾರತ)',
            'gu_IN' => 'ગુજરાતી (ભારત)',
            'pa_IN' => 'ਪੰਜਾਬੀ (ਭਾਰਤ)',
            'or_IN' => 'ଓଡ଼ିଆ (ଭାରତ)',
            'as_IN' => 'অসমীয়া (ভাৰত)',
            'mr_IN' => 'मराठी (भारत)',
            'ur_PK' => 'اردو (پاکستان)',
            'ur_IN' => 'اردو (بھارت)',
            'fa_IR' => 'فارسی (ایران)',
            'fa_AF' => 'فارسی (افغانستان)',
            'ps_AF' => 'پښتو (افغانستان)',
            'ps_PK' => 'پښتو (پاکستان)',
            'sw_KE' => 'Kiswahili (Kenya)',
            'sw_TZ' => 'Kiswahili (Tanzania)',
            'am_ET' => 'አማርኛ (ኢትዮጵያ)',
            'ha_NG' => 'Hausa (Nigeria)',
            'yo_NG' => 'Yorùbá (Nigeria)',
            'ig_NG' => 'Igbo (Nigeria)',
            'zu_ZA' => 'isiZulu (South Africa)',
            'xh_ZA' => 'isiXhosa (South Africa)',
            'af_ZA' => 'Afrikaans (Suid-Afrika)',
            'st_ZA' => 'Sesotho (Afrika Borwa)',
            'tn_ZA' => 'Setswana (Afrika Borwa)',
            'ts_ZA' => 'Xitsonga (Afrika Dzonga)',
            've_ZA' => 'Tshivenḓa (Afurika Tshipembe)',
            'nd_ZA' => 'isiNdebele (iSewula Afrika)',
            'ss_ZA' => 'siSwati (iNingizimu Afrika)',
            'nr_ZA' => 'isiNdebele (iSewula Afrika)',
        ];

        return $languageNames[$localeCode] ?? ucfirst($localeCode);
    }
}
