<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2012 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */


/* User Contact TAB */
$table= 'user';
$o = 0;

/* Phone */
$field = 'us_phone';
$default_fd[$table][$field]['fd_order']          = $o++;
$default_fd[$table][$field]['fd_type']           = 'text';
$default_fd[$table][$field]['fd_required']       = 'no';
$default_fd[$table][$field]['fd_size']           = '30';
$default_fd[$table][$field]['fd_help']           = '';
$default_fd[$table][$field]['fd_tabname']        = 'Contact';
$default_fd[$table][$field]['fd_flags']          = 'PROFILE,PRIVACY,PRIVATE';

/* Phone */
$field = 'us_company';
$default_fd[$table][$field]['fd_order']          = $o++;
$default_fd[$table][$field]['fd_type']           = 'text';
$default_fd[$table][$field]['fd_required']       = 'no';
$default_fd[$table][$field]['fd_size']           = '30';
$default_fd[$table][$field]['fd_help']           = '';
$default_fd[$table][$field]['fd_tabname']        = 'Contact';
$default_fd[$table][$field]['fd_flags']          = 'PROFILE,PRIVACY,PRIVATE';

/* Address 1 */
$field = 'us_address1';
$default_fd[$table][$field]['fd_order']          = $o++;
$default_fd[$table][$field]['fd_type']           = 'text';
$default_fd[$table][$field]['fd_required']       = 'no';
$default_fd[$table][$field]['fd_size']           = '30';
$default_fd[$table][$field]['fd_help']           = '';
$default_fd[$table][$field]['fd_tabname']        = 'Contact';
$default_fd[$table][$field]['fd_flags']          = 'PROFILE,PRIVACY,PRIVATE';

/* Address 2 */
$field = 'us_address2';
$default_fd[$table][$field]['fd_order']          = $o++;
$default_fd[$table][$field]['fd_type']           = 'text';
$default_fd[$table][$field]['fd_required']       = 'no';
$default_fd[$table][$field]['fd_size']           = '30';
$default_fd[$table][$field]['fd_help']           = '';
$default_fd[$table][$field]['fd_tabname']        = 'Contact';
$default_fd[$table][$field]['fd_flags']          = 'PROFILE,PRIVACY,PRIVATE';

/* Address 2 */
$field = 'us_address3';
$default_fd[$table][$field]['fd_order']          = $o++;
$default_fd[$table][$field]['fd_type']           = 'text';
$default_fd[$table][$field]['fd_required']       = 'no';
$default_fd[$table][$field]['fd_size']           = '30';
$default_fd[$table][$field]['fd_help']           = '';
$default_fd[$table][$field]['fd_tabname']        = 'Contact';
$default_fd[$table][$field]['fd_flags']          = 'PROFILE,PRIVACY,PRIVATE';

/* Suburb */
$field = 'us_suburb';
$default_fd[$table][$field]['fd_order']          = $o++;
$default_fd[$table][$field]['fd_type']           = 'text';
$default_fd[$table][$field]['fd_required']       = 'no';
$default_fd[$table][$field]['fd_size']           = '30';
$default_fd[$table][$field]['fd_help']           = '';
$default_fd[$table][$field]['fd_tabname']        = 'Contact';
$default_fd[$table][$field]['fd_flags']          = 'PROFILE,PRIVACY,PRIVATE';

/* City */
$field = 'us_city';
$default_fd[$table][$field]['fd_order']          = $o++;
$default_fd[$table][$field]['fd_type']           = 'text';
$default_fd[$table][$field]['fd_required']       = 'no';
$default_fd[$table][$field]['fd_size']           = '30';
$default_fd[$table][$field]['fd_help']           = '';
$default_fd[$table][$field]['fd_tabname']        = 'Contact';
$default_fd[$table][$field]['fd_flags']          = 'PROFILE,PRIVACY';

/* State */
$field = 'us_state';
$default_fd[$table][$field]['fd_order']          = $o++;
$default_fd[$table][$field]['fd_name']           = 'State / Region';
$default_fd[$table][$field]['fd_type']           = 'text';
$default_fd[$table][$field]['fd_required']       = 'no';
$default_fd[$table][$field]['fd_size']           = '30';
$default_fd[$table][$field]['fd_help']           = '';
$default_fd[$table][$field]['fd_tabname']        = 'Contact';
$default_fd[$table][$field]['fd_flags']          = 'PROFILE,PRIVACY';

/* Postcode */
$field = 'us_postcode';
$default_fd[$table][$field]['fd_order']          = $o++;
$default_fd[$table][$field]['fd_type']           = 'text';
$default_fd[$table][$field]['fd_required']       = 'no';
$default_fd[$table][$field]['fd_size']           = '10';
$default_fd[$table][$field]['fd_help']           = '';
$default_fd[$table][$field]['fd_tabname']        = 'Contact';
$default_fd[$table][$field]['fd_flags']          = 'PROFILE,PRIVACY,PRIVATE';

/* Country */
$field = 'us_country';
$default_fd[$table][$field]['fd_order']          = $o++;
$default_fd[$table][$field]['fd_type']           = 'list';
$default_fd[$table][$field]['fd_name']           = 'Country';
$default_fd[$table][$field]['fd_required']       = 'no';
$default_fd[$table][$field]['fd_size']           = '2';
$default_fd[$table][$field]['fd_help']           = 'A 2 letter code representing the country, eg US, UK, NZ, CN, DE etc';
$default_fd[$table][$field]['fd_tabname']        = 'Contact';
$default_fd[$table][$field]['fd_flags']          = 'PROFILE,PRIVACY';
$default_fd[$table][$field]['fd_options']        =
"AF:Afghanistan
AL:Albania
DZ:Algeria
AS:American Samoa
AD:Andorra
AO:Angola
AI:Anguilla
AQ:Antarctica
AG:Antigua and Barbuda
AR:Argentina
AM:Armenia
AW:Aruba
AU:Australia
AT:Austria
AZ:Azerbaidjan
BS:Bahamas
BH:Bahrain
BD:Bangladesh
BB:Barbados
BY:Belarus
BE:Belgium
BZ:Belize
BJ:Benin
BM:Bermuda
BT:Bhutan
BO:Bolivia
BA:Bosnia-Herzegovina
BW:Botswana
BV:Bouvet Island
BR:Brazil
IO:British Indian Ocean Territory
BN:Brunei Darussalam
BG:Bulgaria
BF:Burkina Faso
BI:Burundi
KH:Cambodia
CM:Cameroon
CA:Canada
CV:Cape Verde
KY:Cayman Islands
CF:Central African Republic
TD:Chad
CL:Chile
CN:China
CX:Christmas Island
CC:Cocos (Keeling) Islands
CO:Colombia
KM:Comoros
CG:Congo
CK:Cook Islands
CR:Costa Rica
HR:Croatia
CU:Cuba
CY:Cyprus
CZ:Czech Republic
DK:Denmark
DJ:Djibouti
DM:Dominica
DO:Dominican Republic
TP:East Timor
EC:Ecuador
EG:Egypt
SV:El Salvador
GQ:Equatorial Guinea
ER:Eritrea
EE:Estonia
ET:Ethiopia
FK:Falkland Islands
FO:Faroe Islands
FJ:Fiji
FI:Finland
CS:Former Czechoslovakia
SU:Former USSR
FR:France
FX:France (European Territory)
GF:French Guyana
TF:French Southern Territories
GA:Gabon
GM:Gambia
GE:Georgia
DE:Germany
GH:Ghana
GI:Gibraltar
GB:Great Britain
GR:Greece
GL:Greenland
GD:Grenada
GP:Guadeloupe (French)
GU:Guam (USA)
GT:Guatemala
GN:Guinea
GW:Guinea Bissau
GY:Guyana
HT:Haiti
HM:Heard and McDonald Islands
HN:Honduras
HK:Hong Kong
HU:Hungary
IS:Iceland
IN:India
ID:Indonesia
IR:Iran
IQ:Iraq
IE:Ireland
IL:Israel
IT:Italy
CI:Ivory Coast (Cote DIvoire)
JM:Jamaica
JP:Japan
JO:Jordan
KZ:Kazakhstan
KE:Kenya
KI:Kiribati
KW:Kuwait
KG:Kyrgyzstan
LA:Laos
LV:Latvia
LB:Lebanon
LS:Lesotho
LR:Liberia
LY:Libya
LI:Liechtenstein
LT:Lithuania
LU:Luxembourg
MO:Macau
MK:Macedonia
MG:Madagascar
MW:Malawi
MY:Malaysia
MV:Maldives
ML:Mali
MT:Malta
MH:Marshall Islands
MQ:Martinique (French)
MR:Mauritania
MU:Mauritius
YT:Mayotte
MX:Mexico
FM:Micronesia
MD:Moldavia
MC:Monaco
MN:Mongolia
MS:Montserrat
MA:Morocco
MZ:Mozambique
MM:Myanmar
NA:Namibia
NR:Nauru
NP:Nepal
NL:Netherlands
AN:Netherlands Antilles
NT:Neutral Zone
NC:New Caledonia (French)
NZ:New Zealand
NI:Nicaragua
NE:Niger
NG:Nigeria
NU:Niue
NF:Norfolk Island
KP:North Korea
MP:Northern Mariana Islands
NO:Norway
OM:Oman
PK:Pakistan
PW:Palau
PA:Panama
PG:Papua New Guinea
PY:Paraguay
PE:Peru
PH:Philippines
PN:Pitcairn Island
PL:Poland
PF:Polynesia (French)
PT:Portugal
PR:Puerto Rico
QA:Qatar
RE:Reunion (French)
RO:Romania
RU:Russian Federation
RW:Rwanda
GS:S. Georgia & S. Sandwich Isls.
SH:Saint Helena
KN:Saint Kitts & Nevis Anguilla
LC:Saint Lucia
PM:Saint Pierre and Miquelon
ST:Saint Tome (Sao Tome) and Principe
VC:Saint Vincent & Grenadines
WS:Samoa
SM:San Marino
SA:Saudi Arabia
SN:Senegal
SC:Seychelles
SL:Sierra Leone
SG:Singapore
SK:Slovak Republic
SI:Slovenia
SB:Solomon Islands
SO:Somalia
ZA:South Africa
KR:South Korea
ES:Spain
LK:Sri Lanka
SD:Sudan
SR:Suriname
SJ:Svalbard and Jan Mayen Islands
SZ:Swaziland
SE:Sweden
CH:Switzerland
SY:Syria
TJ:Tajikistan
TW:Taiwan
TZ:Tanzania
TH:Thailand
TG:Togo
TK:Tokelau
TO:Tonga
TT:Trinidad and Tobago
TN:Tunisia
TR:Turkey
TM:Turkmenistan
TC:Turks and Caicos Islands
TV:Tuvalu
UG:Uganda
UA:Ukraine
AE:United Arab Emirates
US:United States of America
UY:Uruguay
UM:USA Minor Outlying Islands
UZ:Uzbekistan
VU:Vanuatu
VA:Vatican City State
VE:Venezuela
VN:Vietnam
VG:Virgin Islands (British)
VI:Virgin Islands (USA)
WF:Wallis and Futuna Islands
EH:Western Sahara
YE:Yemen
YU:Yugoslavia
ZR:Zaire
ZM:Zambia
ZW:Zimbabwe";
