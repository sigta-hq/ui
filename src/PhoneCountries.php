<?php

namespace Sigta\Ui;

use Collator;
use Locale;

/**
 * Every country a phone number can belong to, for <x-sigta::phone-input>.
 */
final class PhoneCountries
{
    // ISO code => [calling code, English name], from the metadata in Google's
    // libphonenumber (giggsey/libphonenumber-for-php 9.0.44). The English name
    // is shown only when PHP has no intl extension to name it in the app's locale.
    private const array ALL = [
        'AC' => ['+247', 'Ascension Island'],
        'AD' => ['+376', 'Andorra'],
        'AE' => ['+971', 'United Arab Emirates'],
        'AF' => ['+93', 'Afghanistan'],
        'AG' => ['+1', 'Antigua & Barbuda'],
        'AI' => ['+1', 'Anguilla'],
        'AL' => ['+355', 'Albania'],
        'AM' => ['+374', 'Armenia'],
        'AO' => ['+244', 'Angola'],
        'AR' => ['+54', 'Argentina'],
        'AS' => ['+1', 'American Samoa'],
        'AT' => ['+43', 'Austria'],
        'AU' => ['+61', 'Australia'],
        'AW' => ['+297', 'Aruba'],
        'AX' => ['+358', 'Åland Islands'],
        'AZ' => ['+994', 'Azerbaijan'],
        'BA' => ['+387', 'Bosnia & Herzegovina'],
        'BB' => ['+1', 'Barbados'],
        'BD' => ['+880', 'Bangladesh'],
        'BE' => ['+32', 'Belgium'],
        'BF' => ['+226', 'Burkina Faso'],
        'BG' => ['+359', 'Bulgaria'],
        'BH' => ['+973', 'Bahrain'],
        'BI' => ['+257', 'Burundi'],
        'BJ' => ['+229', 'Benin'],
        'BL' => ['+590', 'St. Barthélemy'],
        'BM' => ['+1', 'Bermuda'],
        'BN' => ['+673', 'Brunei'],
        'BO' => ['+591', 'Bolivia'],
        'BQ' => ['+599', 'Caribbean Netherlands'],
        'BR' => ['+55', 'Brazil'],
        'BS' => ['+1', 'Bahamas'],
        'BT' => ['+975', 'Bhutan'],
        'BW' => ['+267', 'Botswana'],
        'BY' => ['+375', 'Belarus'],
        'BZ' => ['+501', 'Belize'],
        'CA' => ['+1', 'Canada'],
        'CC' => ['+61', 'Cocos (Keeling) Islands'],
        'CD' => ['+243', 'Congo - Kinshasa'],
        'CF' => ['+236', 'Central African Republic'],
        'CG' => ['+242', 'Congo - Brazzaville'],
        'CH' => ['+41', 'Switzerland'],
        'CI' => ['+225', 'Côte d’Ivoire'],
        'CK' => ['+682', 'Cook Islands'],
        'CL' => ['+56', 'Chile'],
        'CM' => ['+237', 'Cameroon'],
        'CN' => ['+86', 'China'],
        'CO' => ['+57', 'Colombia'],
        'CR' => ['+506', 'Costa Rica'],
        'CU' => ['+53', 'Cuba'],
        'CV' => ['+238', 'Cape Verde'],
        'CW' => ['+599', 'Curaçao'],
        'CX' => ['+61', 'Christmas Island'],
        'CY' => ['+357', 'Cyprus'],
        'CZ' => ['+420', 'Czechia'],
        'DE' => ['+49', 'Germany'],
        'DJ' => ['+253', 'Djibouti'],
        'DK' => ['+45', 'Denmark'],
        'DM' => ['+1', 'Dominica'],
        'DO' => ['+1', 'Dominican Republic'],
        'DZ' => ['+213', 'Algeria'],
        'EC' => ['+593', 'Ecuador'],
        'EE' => ['+372', 'Estonia'],
        'EG' => ['+20', 'Egypt'],
        'EH' => ['+212', 'Western Sahara'],
        'ER' => ['+291', 'Eritrea'],
        'ES' => ['+34', 'Spain'],
        'ET' => ['+251', 'Ethiopia'],
        'FI' => ['+358', 'Finland'],
        'FJ' => ['+679', 'Fiji'],
        'FK' => ['+500', 'Falkland Islands'],
        'FM' => ['+691', 'Micronesia'],
        'FO' => ['+298', 'Faroe Islands'],
        'FR' => ['+33', 'France'],
        'GA' => ['+241', 'Gabon'],
        'GB' => ['+44', 'United Kingdom'],
        'GD' => ['+1', 'Grenada'],
        'GE' => ['+995', 'Georgia'],
        'GF' => ['+594', 'French Guiana'],
        'GG' => ['+44', 'Guernsey'],
        'GH' => ['+233', 'Ghana'],
        'GI' => ['+350', 'Gibraltar'],
        'GL' => ['+299', 'Greenland'],
        'GM' => ['+220', 'Gambia'],
        'GN' => ['+224', 'Guinea'],
        'GP' => ['+590', 'Guadeloupe'],
        'GQ' => ['+240', 'Equatorial Guinea'],
        'GR' => ['+30', 'Greece'],
        'GT' => ['+502', 'Guatemala'],
        'GU' => ['+1', 'Guam'],
        'GW' => ['+245', 'Guinea-Bissau'],
        'GY' => ['+592', 'Guyana'],
        'HK' => ['+852', 'Hong Kong SAR China'],
        'HN' => ['+504', 'Honduras'],
        'HR' => ['+385', 'Croatia'],
        'HT' => ['+509', 'Haiti'],
        'HU' => ['+36', 'Hungary'],
        'ID' => ['+62', 'Indonesia'],
        'IE' => ['+353', 'Ireland'],
        'IL' => ['+972', 'Israel'],
        'IM' => ['+44', 'Isle of Man'],
        'IN' => ['+91', 'India'],
        'IO' => ['+246', 'British Indian Ocean Territory'],
        'IQ' => ['+964', 'Iraq'],
        'IR' => ['+98', 'Iran'],
        'IS' => ['+354', 'Iceland'],
        'IT' => ['+39', 'Italy'],
        'JE' => ['+44', 'Jersey'],
        'JM' => ['+1', 'Jamaica'],
        'JO' => ['+962', 'Jordan'],
        'JP' => ['+81', 'Japan'],
        'KE' => ['+254', 'Kenya'],
        'KG' => ['+996', 'Kyrgyzstan'],
        'KH' => ['+855', 'Cambodia'],
        'KI' => ['+686', 'Kiribati'],
        'KM' => ['+269', 'Comoros'],
        'KN' => ['+1', 'St. Kitts & Nevis'],
        'KP' => ['+850', 'North Korea'],
        'KR' => ['+82', 'South Korea'],
        'KW' => ['+965', 'Kuwait'],
        'KY' => ['+1', 'Cayman Islands'],
        'KZ' => ['+7', 'Kazakhstan'],
        'LA' => ['+856', 'Laos'],
        'LB' => ['+961', 'Lebanon'],
        'LC' => ['+1', 'St. Lucia'],
        'LI' => ['+423', 'Liechtenstein'],
        'LK' => ['+94', 'Sri Lanka'],
        'LR' => ['+231', 'Liberia'],
        'LS' => ['+266', 'Lesotho'],
        'LT' => ['+370', 'Lithuania'],
        'LU' => ['+352', 'Luxembourg'],
        'LV' => ['+371', 'Latvia'],
        'LY' => ['+218', 'Libya'],
        'MA' => ['+212', 'Morocco'],
        'MC' => ['+377', 'Monaco'],
        'MD' => ['+373', 'Moldova'],
        'ME' => ['+382', 'Montenegro'],
        'MF' => ['+590', 'St. Martin'],
        'MG' => ['+261', 'Madagascar'],
        'MH' => ['+692', 'Marshall Islands'],
        'MK' => ['+389', 'North Macedonia'],
        'ML' => ['+223', 'Mali'],
        'MM' => ['+95', 'Myanmar (Burma)'],
        'MN' => ['+976', 'Mongolia'],
        'MO' => ['+853', 'Macao SAR China'],
        'MP' => ['+1', 'Northern Mariana Islands'],
        'MQ' => ['+596', 'Martinique'],
        'MR' => ['+222', 'Mauritania'],
        'MS' => ['+1', 'Montserrat'],
        'MT' => ['+356', 'Malta'],
        'MU' => ['+230', 'Mauritius'],
        'MV' => ['+960', 'Maldives'],
        'MW' => ['+265', 'Malawi'],
        'MX' => ['+52', 'Mexico'],
        'MY' => ['+60', 'Malaysia'],
        'MZ' => ['+258', 'Mozambique'],
        'NA' => ['+264', 'Namibia'],
        'NC' => ['+687', 'New Caledonia'],
        'NE' => ['+227', 'Niger'],
        'NF' => ['+672', 'Norfolk Island'],
        'NG' => ['+234', 'Nigeria'],
        'NI' => ['+505', 'Nicaragua'],
        'NL' => ['+31', 'Netherlands'],
        'NO' => ['+47', 'Norway'],
        'NP' => ['+977', 'Nepal'],
        'NR' => ['+674', 'Nauru'],
        'NU' => ['+683', 'Niue'],
        'NZ' => ['+64', 'New Zealand'],
        'OM' => ['+968', 'Oman'],
        'PA' => ['+507', 'Panama'],
        'PE' => ['+51', 'Peru'],
        'PF' => ['+689', 'French Polynesia'],
        'PG' => ['+675', 'Papua New Guinea'],
        'PH' => ['+63', 'Philippines'],
        'PK' => ['+92', 'Pakistan'],
        'PL' => ['+48', 'Poland'],
        'PM' => ['+508', 'St. Pierre & Miquelon'],
        'PR' => ['+1', 'Puerto Rico'],
        'PS' => ['+970', 'Palestinian Territories'],
        'PT' => ['+351', 'Portugal'],
        'PW' => ['+680', 'Palau'],
        'PY' => ['+595', 'Paraguay'],
        'QA' => ['+974', 'Qatar'],
        'RE' => ['+262', 'Réunion'],
        'RO' => ['+40', 'Romania'],
        'RS' => ['+381', 'Serbia'],
        'RU' => ['+7', 'Russia'],
        'RW' => ['+250', 'Rwanda'],
        'SA' => ['+966', 'Saudi Arabia'],
        'SB' => ['+677', 'Solomon Islands'],
        'SC' => ['+248', 'Seychelles'],
        'SD' => ['+249', 'Sudan'],
        'SE' => ['+46', 'Sweden'],
        'SG' => ['+65', 'Singapore'],
        'SH' => ['+290', 'St. Helena'],
        'SI' => ['+386', 'Slovenia'],
        'SJ' => ['+47', 'Svalbard & Jan Mayen'],
        'SK' => ['+421', 'Slovakia'],
        'SL' => ['+232', 'Sierra Leone'],
        'SM' => ['+378', 'San Marino'],
        'SN' => ['+221', 'Senegal'],
        'SO' => ['+252', 'Somalia'],
        'SR' => ['+597', 'Suriname'],
        'SS' => ['+211', 'South Sudan'],
        'ST' => ['+239', 'São Tomé & Príncipe'],
        'SV' => ['+503', 'El Salvador'],
        'SX' => ['+1', 'Sint Maarten'],
        'SY' => ['+963', 'Syria'],
        'SZ' => ['+268', 'Eswatini'],
        'TA' => ['+290', 'Tristan da Cunha'],
        'TC' => ['+1', 'Turks & Caicos Islands'],
        'TD' => ['+235', 'Chad'],
        'TG' => ['+228', 'Togo'],
        'TH' => ['+66', 'Thailand'],
        'TJ' => ['+992', 'Tajikistan'],
        'TK' => ['+690', 'Tokelau'],
        'TL' => ['+670', 'Timor-Leste'],
        'TM' => ['+993', 'Turkmenistan'],
        'TN' => ['+216', 'Tunisia'],
        'TO' => ['+676', 'Tonga'],
        'TR' => ['+90', 'Türkiye'],
        'TT' => ['+1', 'Trinidad & Tobago'],
        'TV' => ['+688', 'Tuvalu'],
        'TW' => ['+886', 'Taiwan'],
        'TZ' => ['+255', 'Tanzania'],
        'UA' => ['+380', 'Ukraine'],
        'UG' => ['+256', 'Uganda'],
        'US' => ['+1', 'United States'],
        'UY' => ['+598', 'Uruguay'],
        'UZ' => ['+998', 'Uzbekistan'],
        'VA' => ['+39', 'Vatican City'],
        'VC' => ['+1', 'St. Vincent & Grenadines'],
        'VE' => ['+58', 'Venezuela'],
        'VG' => ['+1', 'British Virgin Islands'],
        'VI' => ['+1', 'U.S. Virgin Islands'],
        'VN' => ['+84', 'Vietnam'],
        'VU' => ['+678', 'Vanuatu'],
        'WF' => ['+681', 'Wallis & Futuna'],
        'WS' => ['+685', 'Samoa'],
        'XK' => ['+383', 'Kosovo'],
        'YE' => ['+967', 'Yemen'],
        'YT' => ['+262', 'Mayotte'],
        'ZA' => ['+27', 'South Africa'],
        'ZM' => ['+260', 'Zambia'],
        'ZW' => ['+263', 'Zimbabwe'],
    ];

    // Where countries share a calling code, a number with only that code is
    // shown as the country libphonenumber names first for it.
    private const array SHOWN_AS = [
        '+1' => 'US',
        '+7' => 'RU',
        '+39' => 'IT',
        '+44' => 'GB',
        '+47' => 'NO',
        '+61' => 'AU',
        '+212' => 'MA',
        '+262' => 'RE',
        '+290' => 'SH',
        '+358' => 'FI',
        '+590' => 'GP',
        '+599' => 'CW',
    ];

    /**
     * Every country, named in the locale and listed in its alphabetical order.
     *
     * @return array<string, array{calling_code: string, name: string}>
     */
    public static function all(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        $countries = [];

        foreach (self::ALL as $code => [$callingCode, $englishName]) {
            $countries[$code] = ['calling_code' => $callingCode, 'name' => self::name($code, $englishName, $locale)];
        }

        $names = array_map(fn (array $country): string => $country['name'], $countries);

        if (class_exists(Collator::class)) {
            (new Collator($locale))->asort($names);
        } else {
            asort($names, SORT_NATURAL | SORT_FLAG_CASE);
        }

        return array_replace($names, $countries);
    }

    /**
     * The country a number with this calling code is shown as, when nothing
     * says which of the countries sharing the code it belongs to.
     */
    public static function shownAs(string $callingCode): ?string
    {
        if (isset(self::SHOWN_AS[$callingCode])) {
            return self::SHOWN_AS[$callingCode];
        }

        foreach (self::ALL as $code => [$countryCallingCode]) {
            if ($countryCallingCode === $callingCode) {
                return $code;
            }
        }

        return null;
    }

    private static function name(string $code, string $englishName, string $locale): string
    {
        if (! class_exists(Locale::class)) {
            return $englishName;
        }

        $name = Locale::getDisplayRegion('-'.$code, $locale);

        // ICU answers with the bare code for a region it cannot name.
        return $name === '' || $name === $code ? $englishName : $name;
    }
}
