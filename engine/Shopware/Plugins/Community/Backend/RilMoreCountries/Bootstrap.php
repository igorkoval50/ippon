<?php
/**
 * Plugin bootstrapping class.
 *
 * @package    RIL_Plugins
 * @subpackage Länder Plugin
 * @copyright  Copyright (c) 2017, ratzinger-internetlösungen (http://www.ratzinger-internetloesungen.de)
 * @version    5.5
 * @author     Ratzinger
 */ 
class Shopware_Plugins_Backend_RilMoreCountries_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
	/**
	 * Returns plugin manager setup rights
	 *
	 * @return array
	 */
	public function getCapabilities()
    {
    	return array(
        	'install' => true,
    		'enable' => true
    	);
    }
	
	/**
	 * Returns plugin name
	 *
	 * @return string
	 */
	public function getLabel()
	{
   		return 'Länder AddOn';
	}
	
	/**
	 * Returns plugin version
	 *
	 * @return string
	 */
	public function getVersion() {
	    return '5.5';
    }
	
	/**
	 * Returns plugin info
	 *
	 * @return array
	 */
	public function getInfo()
	{
   	 	return array(
        	'version' => $this->getVersion(),
        	'label' => $this->getLabel(),
        	'author' => 'ratzinger-internetlösungen',
        	'copyright' => '2013 - ratzinger-internetlösungen',
        	'description' => 'Länder Plugin das die bestehenden 33 Länder um 207 auf 240 Länder erweitert.',
        	'support' => 'Shopware Forum',
        	'link' => 'http://forum.shopware.de'
   		);
	} 
	
	/**
	 * Install plugin method
	 *
	 */
	public function install()
    {        
        $this->createDatabase();
                
        return true;
    }
    
    /**
	 * UnInstall plugin method
	 *
	 */
    public function uninstall()
	{
    	$this->removeDatabase();
 
   		return true;
	} 	

	/**
	 * This function create the database entries
	 * 
	 */
	private function createDatabase()
    {
        $sql = "
            INSERT INTO `s_core_countries` (`id`, `countryname`, `countryiso`, `areaID`, `countryen`, `position`, `notice`, `taxfree`, `taxfree_ustid`, `taxfree_ustid_checked`, `active`, `iso3`, `display_state_in_registration`, `force_state_in_registration`) VALUES
(38, 'Afghanistan', 'AF', 2, 'Afghanistan', 10, '', 0, 0, 0, 0, 'AFG', 0, 0),
(39, 'Albanien', 'AL', 2, 'Albania', 10, '', 0, 0, 0, 0, 'ALB', 0, 0),
(40, 'Algerien', 'DZ', 2, 'Algeria', 10, '', 0, 0, 0, 0, 'DZA', 0, 0),
(41, 'Amerikanisch-Samoa', 'AS', 2, 'American Samoa', 10, '', 0, 0, 0, 0, 'ASM', 0, 0),
(42, 'Andorra', 'AD', 2, 'Andorra', 10, '', 0, 0, 0, 0, 'AND', 0, 0),
(43, 'Angola', 'AO', 2, 'Angola', 10, '', 0, 0, 0, 0, 'AGO', 0, 0),
(44, 'Anguilla', 'AI', 2, 'Anguilla', 10, '', 0, 0, 0, 0, 'AIA', 0, 0),
(45, 'Antarktik', 'AQ', 2, 'Antarctica', 10, '', 0, 0, 0, 0, 'ATA', 0, 0),
(46, 'Antigua und Barbuda', 'AG', 2, 'Antigua and Barbuda', 10, '', 0, 0, 0, 0, 'ATG', 0, 0),
(47, 'Argentinien', 'AR', 2, 'Argentina', 10, '', 0, 0, 0, 0, 'ARG', 0, 0),
(48, 'Armenien', 'AM', 2, 'Armenia', 10, '', 0, 0, 0, 0, 'ARM', 0, 0),
(49, 'Aruba', 'AW', 2, 'Aruba', 10, '', 0, 0, 0, 0, 'ABW', 0, 0),
(50, 'Aserbaidschan', 'AZ', 2, 'Azerbaijan', 10, '', 0, 0, 0, 0, 'AZE', 0, 0),
(51, 'Bahamas', 'BS', 2, 'Bahamas', 10, '', 0, 0, 0, 0, 'BHS', 0, 0),
(52, 'Bahrain', 'BH', 2, 'Bahrain', 10, '', 0, 0, 0, 0, 'BHR', 0, 0),
(53, 'Bangladesch', 'BD', 2, 'Bangladesh', 10, '', 0, 0, 0, 0, 'BGD', 0, 0),
(54, 'Barbados', 'BB', 2, 'Barbados', 10, '', 0, 0, 0, 0, 'BRB', 0, 0),
(55, 'Weissrussland', 'BY', 2, 'Belarus', 10, '', 0, 0, 0, 0, 'BLR', 0, 0),
(56, 'Belize', 'BZ', 2, 'Belize', 10, '', 0, 0, 0, 0, 'BLZ', 0, 0),
(57, 'Benin', 'BJ', 2, 'Benin', 10, '', 0, 0, 0, 0, 'BEN', 0, 0),
(58, 'Bermudas', 'BM', 2, 'Bermuda', 10, '', 0, 0, 0, 0, 'BMU', 0, 0),
(59, 'Bhutan', 'BT', 2, 'Bhutan', 10, '', 0, 0, 0, 0, 'BTN', 0, 0),
(60, 'Bolivien', 'BO', 2, 'Bolivia', 10, '', 0, 0, 0, 0, 'BOL', 0, 0),
(61, 'Bosnien und Herzegowina', 'BA', 3, 'Bosnia and Herzegowina', 10, '', 0, 0, 0, 0, 'BIH', 0, 0),
(62, 'Botswana', 'BW', 2, 'Botswana', 10, '', 0, 0, 0, 0, 'BWA', 0, 0),
(63, 'Bouvet-Inseln', 'BV', 2, 'Bouvet Island', 10, '', 0, 0, 0, 0, 'BVT', 0, 0),
(64, 'Britisches Territorium im Indischen Ozean', 'IO', 2, 'British Indian Ocean Territory', 10, '', 0, 0, 0, 0, 'IOT', 0, 0),
(65, 'Brunei Darussalam', 'BN', 2, 'Brunei Darussalam', 10, '', 0, 0, 0, 0, 'BRN', 0, 0),
(66, 'Bulgarien', 'BG', 3, 'Bulgaria', 10, '', 0, 0, 0, 0, 'BGR', 0, 0),
(67, 'Burkina Faso', 'BF', 2, 'Burkina Faso', 10, '', 0, 0, 0, 0, 'BFA', 0, 0),
(68, 'Burundi', 'BI', 2, 'Burundi', 10, '', 0, 0, 0, 0, 'BDI', 0, 0),
(69, 'Kambodscha', 'KH', 2, 'Cambodia', 10, '', 0, 0, 0, 0, 'KHM', 0, 0),
(70, 'Kamerun', 'CM', 2, 'Cameroon', 10, '', 0, 0, 0, 0, 'CMR', 0, 0),
(71, 'Cape Verde', 'CV', 2, 'Cape Verde', 10, '', 0, 0, 0, 0, 'CPV', 0, 0),
(72, 'Cayman Inseln', 'KY', 2, 'Cayman Islands', 10, '', 0, 0, 0, 0, 'CYM', 0, 0),
(73, 'Zentralafrikanische Republik', 'CF', 2, 'Central African Republic', 10, '', 0, 0, 0, 0, 'CAF', 0, 0),
(74, 'Tschad', 'TD', 2, 'Chad', 10, '', 0, 0, 0, 0, 'TCD', 0, 0),
(75, 'Chile', 'CL', 2, 'Chile', 10, '', 0, 0, 0, 0, 'CHL', 0, 0),
(76, 'China', 'CN', 2, 'China', 10, '', 0, 0, 0, 0, 'CHN', 0, 0),
(77, 'Weihnachts-Inseln', 'CX', 2, 'Christmas Island', 10, '', 0, 0, 0, 0, 'CXR', 0, 0),
(78, 'Kokosinseln', 'CC', 2, 'Cocos (Keeling) Islands', 10, '', 0, 0, 0, 0, 'CCK', 0, 0),
(79, 'Kolumbien', 'CO', 2, 'Colombia', 10, '', 0, 0, 0, 0, 'COL', 0, 0),
(80, 'Komoren', 'KM', 2, 'Comoros', 10, '', 0, 0, 0, 0, 'COM', 0, 0),
(81, 'Kongo', 'CG', 2, 'Congo', 10, '', 0, 0, 0, 0, 'COG', 0, 0),
(82, 'Cook-Inseln', 'CK', 2, 'Cook Islands', 10, '', 0, 0, 0, 0, 'COK', 0, 0),
(83, 'Costa Rica', 'CR', 2, 'Costa Rica', 10, '', 0, 0, 0, 0, 'CRI', 0, 0),
(84, 'Elfenbeinküste', 'CI', 2, 'Cote D''Ivoire', 10, '', 0, 0, 0, 0, 'CIV', 0, 0),
(85, 'Kroatien', 'HR', 3, 'Croatia', 10, '', 0, 0, 0, 0, 'HRV', 0, 0),
(86, 'Kuba', 'CU', 2, 'Cuba', 10, '', 0, 0, 0, 0, 'CUB', 0, 0),
(87, 'Zypern', 'CY', 3, 'Cyprus', 10, '', 0, 0, 0, 0, 'CYP', 0, 0),
(88, 'Dschibuti', 'DJ', 2, 'Djibouti', 10, '', 0, 0, 0, 0, 'DJI', 0, 0),
(89, 'Dominica', 'DM', 2, 'Dominica', 10, '', 0, 0, 0, 0, 'DMA', 0, 0),
(90, 'Dominikanische Republik', 'DO', 2, 'Dominican Republic', 10, '', 0, 0, 0, 0, 'DOM', 0, 0),
(91, 'Osttimor', 'TP', 2, 'East Timor', 10, '', 0, 0, 0, 0, 'TMP', 0, 0),
(92, 'Ecuador', 'EC', 2, 'Ecuador', 10, '', 0, 0, 0, 0, 'ECU', 0, 0),
(93, 'Ägypten', 'EG', 2, 'Egypt', 10, '', 0, 0, 0, 0, 'EGY', 0, 0),
(94, 'El Salvador', 'SV', 2, 'El Salvador', 10, '', 0, 0, 0, 0, 'SLV', 0, 0),
(95, 'Äquatorialguinea', 'GQ', 2, 'Equatorial Guinea', 10, '', 0, 0, 0, 0, 'GNQ', 0, 0),
(96, 'Eritrea', 'ER', 2, 'Eritrea', 10, '', 0, 0, 0, 0, 'ERI', 0, 0),
(97, 'Estland', 'EE', 3, 'Estonia', 10, '', 0, 0, 0, 0, 'EST', 0, 0),
(98, 'Äthiopien', 'ET', 2, 'Ethiopia', 10, '', 0, 0, 0, 0, 'ETH', 0, 0),
(99, 'Falkland-Inseln', 'FK', 2, 'Falkland Islands (Malvinas)', 10, '', 0, 0, 0, 0, 'FLK', 0, 0),
(100, 'Färöer-Inseln', 'FO', 2, 'Faroe Islands', 10, '', 0, 0, 0, 0, 'FRO', 0, 0),
(101, 'Fiji', 'FJ', 2, 'Fiji', 10, '', 0, 0, 0, 0, 'FJI', 0, 0),
(102, 'Metropolitan-Frankreich', 'FX', 3, 'France, Metropolitan', 10, '', 0, 0, 0, 0, 'FXX', 0, 0),
(103, 'Französisch-Guyana', 'GF', 2, 'French Guiana', 10, '', 0, 0, 0, 0, 'GUF', 0, 0),
(104, 'Französisch-Polynesien', 'PF', 2, 'French Polynesia', 10, '', 0, 0, 0, 0, 'PYF', 0, 0),
(105, 'Französische Gebiete im südlichen Indischen Ozean', 'TF', 2, 'French Southern Territories', 10, '', 0, 0, 0, 0, 'ATF', 0, 0),
(106, 'Gabun', 'GA', 2, 'Gabon', 10, '', 0, 0, 0, 0, 'GAB', 0, 0),
(107, 'Gambia', 'GM', 2, 'Gambia', 10, '', 0, 0, 0, 0, 'GMB', 0, 0),
(108, 'Georgien', 'GE', 3, 'Georgia', 10, '', 0, 0, 0, 0, 'GEO', 0, 0),
(109, 'Ghana', 'GH', 2, 'Ghana', 10, '', 0, 0, 0, 0, 'GHA', 0, 0),
(110, 'Gibraltar', 'GI', 2, 'Gibraltar', 10, '', 0, 0, 0, 0, 'GIB', 0, 0),
(111, 'Grönland', 'GL', 2, 'Greenland', 10, '', 0, 0, 0, 0, 'GRL', 0, 0),
(112, 'Grenada', 'GD', 2, 'Grenada', 10, '', 0, 0, 0, 0, 'GRD', 0, 0),
(113, 'Guadeloupe', 'GP', 2, 'Guadeloupe', 10, '', 0, 0, 0, 0, 'GLP', 0, 0),
(114, 'Guam', 'GU', 2, 'Guam', 10, '', 0, 0, 0, 0, 'GUM', 0, 0),
(115, 'Guatemala', 'GT', 2, 'Guatemala', 10, '', 0, 0, 0, 0, 'GTM', 0, 0),
(116, 'Guinea', 'GN', 2, 'Guinea', 10, '', 0, 0, 0, 0, 'GIN', 0, 0),
(117, 'Guinea-Bissau', 'GW', 2, 'Guinea-Bissau', 10, '', 0, 0, 0, 0, 'GNB', 0, 0),
(118, 'Guyana', 'GY', 2, 'Guyana', 10, '', 0, 0, 0, 0, 'GUY', 0, 0),
(119, 'Haiti', 'HT', 2, 'Haiti', 10, '', 0, 0, 0, 0, 'HTI', 0, 0),
(120, 'Heard und McDonald-Inseln', 'HM', 2, 'Heard and Mc Donald Islands', 10, '', 0, 0, 0, 0, 'HMD', 0, 0),
(121, 'Honduras', 'HN', 2, 'Honduras', 10, '', 0, 0, 0, 0, 'HND', 0, 0),
(122, 'Hong Kong', 'HK', 2, 'Hong Kong', 10, '', 0, 0, 0, 0, 'HKG', 0, 0),
(123, 'Indien', 'IN', 2, 'India', 10, '', 0, 0, 0, 0, 'IND', 0, 0),
(124, 'Indonesien', 'ID', 2, 'Indonesia', 10, '', 0, 0, 0, 0, 'IDN', 0, 0),
(125, 'Iran', 'IR', 2, 'Iran', 10, '', 0, 0, 0, 0, 'IRN', 0, 0),
(126, 'Irak', 'IQ', 2, 'Iraq', 10, '', 0, 0, 0, 0, 'IRQ', 0, 0),
(127, 'Jamaica', 'JM', 2, 'Jamaica', 10, '', 0, 0, 0, 0, 'JAM', 0, 0),
(128, 'Jordanien', 'JO', 2, 'Jordan', 10, '', 0, 0, 0, 0, 'JOR', 0, 0),
(129, 'Kasachstan', 'KZ', 3, 'Kazakhstan', 10, '', 0, 0, 0, 0, 'KAZ', 0, 0),
(130, 'Kenia', 'KE', 2, 'Kenya', 10, '', 0, 0, 0, 0, 'KEN', 0, 0),
(131, 'Kiribati', 'KI', 2, 'Kiribati', 10, '', 0, 0, 0, 0, 'KIR', 0, 0),
(132, 'Nordkorea', 'KP', 2, 'Korea, Democratic People''s Republic of', 10, '', 0, 0, 0, 0, 'PRK', 0, 0),
(133, 'Südkorea', 'KR', 2, 'Korea, Republic of', 10, '', 0, 0, 0, 0, 'KOR', 0, 0),
(134, 'Kuwait', 'KW', 2, 'Kuwait', 10, '', 0, 0, 0, 0, 'KWT', 0, 0),
(135, 'Kirgisistan', 'KG', 2, 'Kyrgyzstan', 10, '', 0, 0, 0, 0, 'KGZ', 0, 0),
(136, 'Volksdemokratische Republik Laos', 'LA', 2, 'Lao People''s Democratic Republic', 10, '', 0, 0, 0, 0, 'LAO', 0, 0),
(137, 'Letland', 'LV', 2, 'Latvia', 10, '', 0, 0, 0, 0, 'LVA', 0, 0),
(138, 'Libanon', 'LB', 2, 'Lebanon', 10, '', 0, 0, 0, 0, 'LBN', 0, 0),
(139, 'Lesotho', 'LS', 2, 'Lesotho', 10, '', 0, 0, 0, 0, 'LSO', 0, 0),
(140, 'Liberien', 'LR', 2, 'Liberia', 10, '', 0, 0, 0, 0, 'LBR', 0, 0),
(141, 'Libysch-Arabische Dschamahirija', 'LY', 2, 'Libyan Arab Jamahiriya', 10, '', 0, 0, 0, 0, 'LBY', 0, 0),
(142, 'Litauen', 'LT', 2, 'Lithuania', 10, '', 0, 0, 0, 0, 'LTU', 0, 0),
(143, 'Macao', 'MO', 2, 'Macau', 10, '', 0, 0, 0, 0, 'MAC', 0, 0),
(144, 'Ehemalige jugoslawische Republik Mazedonien', 'MK', 2, 'Macedonia, The Former Yugoslav Republic of', 10, '', 0, 0, 0, 0, 'MKD', 0, 0),
(145, 'Madagascar', 'MG', 2, 'Madagascar', 10, '', 0, 0, 0, 0, 'MDG', 0, 0),
(146, 'Malawi', 'MW', 2, 'Malawi', 10, '', 0, 0, 0, 0, 'MWI', 0, 0),
(147, 'Malaysien', 'MY', 2, 'Malaysia', 10, '', 0, 0, 0, 0, 'MYS', 0, 0),
(148, 'Malediven', 'MV', 2, 'Maldives', 10, '', 0, 0, 0, 0, 'MDV', 0, 0),
(149, 'Mali', 'ML', 2, 'Mali', 10, '', 0, 0, 0, 0, 'MLI', 0, 0),
(150, 'Malta', 'MT', 3, 'Malta', 10, '', 0, 0, 0, 0, 'MLT', 0, 0),
(151, 'Marschall-Inseln', 'MH', 2, 'Marshall Islands', 10, '', 0, 0, 0, 0, 'MHL', 0, 0),
(152, 'Martinique', 'MQ', 2, 'Martinique', 10, '', 0, 0, 0, 0, 'MTQ', 0, 0),
(153, 'Mauritanien', 'MR', 2, 'Mauritania', 10, '', 0, 0, 0, 0, 'MRT', 0, 0),
(154, 'Mauritius', 'MU', 2, 'Mauritius', 10, '', 0, 0, 0, 0, 'MUS', 0, 0),
(155, 'Mayotte', 'YT', 2, 'Mayotte', 10, '', 0, 0, 0, 0, 'MYT', 0, 0),
(156, 'Mexiko', 'MX', 2, 'Mexico', 10, '', 0, 0, 0, 0, 'MEX', 0, 0),
(157, 'Föderierte Staaten von Mikronesien', 'FM', 2, 'Micronesia, Federated States of', 10, '', 0, 0, 0, 0, 'FSM', 0, 0),
(158, 'Moldavien', 'MD', 2, 'Moldova, Republic of', 10, '', 0, 0, 0, 0, 'MDA', 0, 0),
(159, 'Monaco', 'MC', 3, 'Monaco', 10, '', 0, 0, 0, 0, 'MCO', 0, 0),
(160, 'Mongolei', 'MN', 2, 'Mongolia', 10, '', 0, 0, 0, 0, 'MNG', 0, 0),
(161, 'Montserrat', 'MS', 2, 'Montserrat', 10, '', 0, 0, 0, 0, 'MSR', 0, 0),
(162, 'Marokko', 'MA', 2, 'Morocco', 10, '', 0, 0, 0, 0, 'MAR', 0, 0),
(163, 'Mozambique', 'MZ', 2, 'Mozambique', 10, '', 0, 0, 0, 0, 'MOZ', 0, 0),
(164, 'Myanmar', 'MM', 2, 'Myanmar', 10, '', 0, 0, 0, 0, 'MMR', 0, 0),
(165, 'Nauru', 'NR', 2, 'Nauru', 10, '', 0, 0, 0, 0, 'NRU', 0, 0),
(166, 'Nepal', 'NP', 2, 'Nepal', 10, '', 0, 0, 0, 0, 'NPL', 0, 0),
(167, 'Niederländischen Antillen', 'AN', 2, 'Netherlands Antilles', 10, '', 0, 0, 0, 0, 'ANT', 0, 0),
(168, 'Neukaledonien', 'NC', 2, 'New Caledonia', 10, '', 0, 0, 0, 0, 'NCL', 0, 0),
(169, 'Neuseeland', 'NZ', 2, 'New Zealand', 10, '', 0, 0, 0, 0, 'NZL', 0, 0),
(170, 'Nicaragua', 'NI', 2, 'Nicaragua', 10, '', 0, 0, 0, 0, 'NIC', 0, 0),
(171, 'Niger', 'NE', 2, 'Niger', 10, '', 0, 0, 0, 0, 'NER', 0, 0),
(172, 'Nigeria', 'NG', 2, 'Nigeria', 10, '', 0, 0, 0, 0, 'NGA', 0, 0),
(173, 'Niue', 'NU', 2, 'Niue', 10, '', 0, 0, 0, 0, 'NIU', 0, 0),
(174, 'Norfolkinseln', 'NF', 2, 'Norfolk Island', 10, '', 0, 0, 0, 0, 'NFK', 0, 0),
(175, 'Nördliche Marianen-Inseln', 'MP', 2, 'Northern Mariana Islands', 10, '', 0, 0, 0, 0, 'MNP', 0, 0),
(176, 'Oman', 'OM', 2, 'Oman', 10, '', 0, 0, 0, 0, 'OMN', 0, 0),
(177, 'Pakistan', 'PK', 2, 'Pakistan', 10, '', 0, 0, 0, 0, 'PAK', 0, 0),
(178, 'Palau', 'PW', 2, 'Palau', 10, '', 0, 0, 0, 0, 'PLW', 0, 0),
(179, 'Panama', 'PA', 2, 'Panama', 10, '', 0, 0, 0, 0, 'PAN', 0, 0),
(180, 'Papua Neu Guinea', 'PG', 2, 'Papua New Guinea', 10, '', 0, 0, 0, 0, 'PNG', 0, 0),
(181, 'Paraguay', 'PY', 2, 'Paraguay', 10, '', 0, 0, 0, 0, 'PRY', 0, 0),
(182, 'Peru', 'PE', 2, 'Peru', 10, '', 0, 0, 0, 0, 'PER', 0, 0),
(183, 'Philippinen', 'PH', 2, 'Philippines', 10, '', 0, 0, 0, 0, 'PHL', 0, 0),
(184, 'Pitcairn', 'PN', 2, 'Pitcairn', 10, '', 0, 0, 0, 0, 'PCN', 0, 0),
(185, 'Puerto Rico', 'PR', 2, 'Puerto Rico', 10, '', 0, 0, 0, 0, 'PRI', 0, 0),
(186, 'Qatar', 'QA', 2, 'Qatar', 10, '', 0, 0, 0, 0, 'QAT', 0, 0),
(187, 'Reunion', 'RE', 2, 'Reunion', 10, '', 0, 0, 0, 0, 'REU', 0, 0),
(188, 'Russland', 'RU', 3, 'Russian Federation', 10, '', 0, 0, 0, 0, 'RUS', 0, 0),
(189, 'Ruanda', 'RW', 2, 'Rwanda', 10, '', 0, 0, 0, 0, 'RWA', 0, 0),
(190, 'St. Kitts und Nevis', 'KN', 2, 'Saint Kitts and Nevis', 10, '', 0, 0, 0, 0, 'KNA', 0, 0),
(191, 'St. Lucia', 'LC', 2, 'Saint Lucia', 10, '', 0, 0, 0, 0, 'LCA', 0, 0),
(192, 'St. Vincent und die Grenadinen', 'VC', 2, 'Saint Vincent and the Grenadines', 10, '', 0, 0, 0, 0, 'VCT', 0, 0),
(193, 'Samoa', 'WS', 2, 'Samoa', 10, '', 0, 0, 0, 0, 'WSM', 0, 0),
(194, 'San Marino', 'SM', 3, 'San Marino', 10, '', 0, 0, 0, 0, 'SMR', 0, 0),
(195, 'Sao Tome und Principe', 'ST', 2, 'Sao Tome and Principe', 10, '', 0, 0, 0, 0, 'STP', 0, 0),
(196, 'Saudi Arabien', 'SA', 2, 'Saudi Arabia', 10, '', 0, 0, 0, 0, 'SAU', 0, 0),
(197, 'Senegal', 'SN', 2, 'Senegal', 10, '', 0, 0, 0, 0, 'SEN', 0, 0),
(198, 'Seychellen', 'SC', 2, 'Seychelles', 10, '', 0, 0, 0, 0, 'SYC', 0, 0),
(199, 'Sierra Leone', 'SL', 2, 'Sierra Leone', 10, '', 0, 0, 0, 0, 'SLE', 0, 0),
(200, 'Singapur', 'SG', 2, 'Singapore', 10, '', 0, 0, 0, 0, 'SGP', 0, 0),
(201, 'Slowenien', 'SI', 3, 'Slovenia', 10, '', 0, 0, 0, 0, 'SVN', 0, 0),
(202, 'Salomoninseln', 'SB', 2, 'Solomon Islands', 10, '', 0, 0, 0, 0, 'SLB', 0, 0),
(203, 'Somalia', 'SO', 2, 'Somalia', 10, '', 0, 0, 0, 0, 'SOM', 0, 0),
(204, 'Südafrika', 'ZA', 2, 'South Africa', 10, '', 0, 0, 0, 0, 'ZAF', 0, 0),
(205, 'Südgeorgien und Südliche Sandwichinseln', 'GS', 2, 'South Georgia and the South Sandwich Islands', 10, '', 0, 0, 0, 0, 'SGS', 0, 0),
(206, 'Sri Lanka', 'LK', 2, 'Sri Lanka', 10, '', 0, 0, 0, 0, 'LKA', 0, 0),
(207, 'St. Helena', 'SH', 2, 'St. Helena', 10, '', 0, 0, 0, 0, 'SHN', 0, 0),
(208, 'St. Pierre und Miquelon', 'PM', 2, 'St. Pierre and Miquelon', 10, '', 0, 0, 0, 0, 'SPM', 0, 0),
(209, 'Sudan', 'SD', 2, 'Sudan', 10, '', 0, 0, 0, 0, 'SDN', 0, 0),
(210, 'Surinam', 'SR', 2, 'Suriname', 10, '', 0, 0, 0, 0, 'SUR', 0, 0),
(211, 'Svalbard und Jan Mayen Inseln', 'SJ', 2, 'Svalbard and Jan Mayen Islands', 10, '', 0, 0, 0, 0, 'SJM', 0, 0),
(212, 'Swaziland', 'SZ', 2, 'Swaziland', 10, '', 0, 0, 0, 0, 'SWZ', 0, 0),
(213, 'Syrien', 'SY', 2, 'Syrian Arab Republic', 10, '', 0, 0, 0, 0, 'SYR', 0, 0),
(214, 'Taiwan', 'TW', 2, 'Taiwan', 10, '', 0, 0, 0, 0, 'TWN', 0, 0),
(215, 'Tadschikistan', 'TJ', 2, 'Tajikistan', 10, '', 0, 0, 0, 0, 'TJK', 0, 0),
(216, 'Tansania', 'TZ', 2, 'Tanzania, United Republic of', 10, '', 0, 0, 0, 0, 'TZA', 0, 0),
(217, 'Thailand', 'TH', 2, 'Thailand', 10, '', 0, 0, 0, 0, 'THA', 0, 0),
(218, 'Togo', 'TG', 2, 'Togo', 10, '', 0, 0, 0, 0, 'TGO', 0, 0),
(219, 'Tokelau', 'TK', 2, 'Tokelau', 10, '', 0, 0, 0, 0, 'TKL', 0, 0),
(220, 'Tonga', 'TO', 2, 'Tonga', 10, '', 0, 0, 0, 0, 'TON', 0, 0),
(221, 'Trinidad und Tobago', 'TT', 2, 'Trinidad and Tobago', 10, '', 0, 0, 0, 0, 'TTO', 0, 0),
(222, 'Tunesien', 'TN', 2, 'Tunisia', 10, '', 0, 0, 0, 0, 'TUN', 0, 0),
(223, 'Turkmenistan', 'TM', 2, 'Turkmenistan', 10, '', 0, 0, 0, 0, 'TKM', 0, 0),
(224, 'Turks- und Caicosinseln', 'TC', 2, 'Turks and Caicos Islands', 10, '', 0, 0, 0, 0, 'TCA', 0, 0),
(225, 'Tuvalu', 'TV', 2, 'Tuvalu', 10, '', 0, 0, 0, 0, 'TUV', 0, 0),
(226, 'Uganda', 'UG', 2, 'Uganda', 10, '', 0, 0, 0, 0, 'UGA', 0, 0),
(227, 'Ukraine', 'UA', 3, 'Ukraine', 10, '', 0, 0, 0, 0, 'UKR', 0, 0),
(228, 'kleinere amerikanische Überseeinseln', 'UM', 2, 'United States Minor Outlying Islands', 10, '', 0, 0, 0, 0, 'UMI', 0, 0),
(229, 'Uruguay', 'UY', 2, 'Uruguay', 10, '', 0, 0, 0, 0, 'URY', 0, 0),
(230, 'Uzbekistan', 'UZ', 2, 'Uzbekistan', 10, '', 0, 0, 0, 0, 'UZB', 0, 0),
(231, 'Vanuatu', 'VU', 2, 'Vanuatu', 10, '', 0, 0, 0, 0, 'VUT', 0, 0),
(232, 'Vatikan Staat', 'VA', 3, 'Vatican City State (Holy See)', 10, '', 0, 0, 0, 0, 'VAT', 0, 0),
(233, 'Venezuela', 'VE', 2, 'Venezuela', 10, '', 0, 0, 0, 0, 'VEN', 0, 0),
(234, 'Vietnam', 'VN', 2, 'Viet Nam', 10, '', 0, 0, 0, 0, 'VNM', 0, 0),
(235, 'Britische Jungferninseln', 'VG', 2, 'Virgin Islands (British)', 10, '', 0, 0, 0, 0, 'VGB', 0, 0),
(236, 'Amerikanische Jungferninseln', 'VI', 2, 'Virgin Islands (U.S.)', 10, '', 0, 0, 0, 0, 'VIR', 0, 0),
(237, 'Wallis und Futuna Inseln', 'WF', 2, 'Wallis and Futuna Islands', 10, '', 0, 0, 0, 0, 'WLF', 0, 0),
(238, 'Westsahara', 'EH', 2, 'Western Sahara', 10, '', 0, 0, 0, 0, 'ESH', 0, 0),
(239, 'Jemen', 'YE', 2, 'Yemen', 10, '', 0, 0, 0, 0, 'YEM', 0, 0),
(240, 'Jugoslawien', 'YU', 3, 'Yugoslavia', 10, '', 0, 0, 0, 0, 'YUG', 0, 0),
(241, 'Zaire', 'ZR', 2, 'Zaire', 10, '', 0, 0, 0, 0, 'ZAR', 0, 0),
(242, 'Sambia', 'ZM', 2, 'Zambia', 10, '', 0, 0, 0, 0, 'ZMB', 0, 0),
(243, 'Zimbabwe', 'ZW', 2, 'Zimbabwe', 10, '', 0, 0, 0, 0, 'ZWE', 0, 0),
(244, 'Åland Inseln', 'AX', 2, 'Åland Inseln', 10, '', 0, 0, 0, 0, 'ALA', 0, 0);
        ";
        
        Shopware()->Db()->query($sql);
    }

	/**
	 * This function delete the database entries
	 * 
	 */
	private function removeDatabase()
	{
    	$sql= "DELETE FROM `s_core_countries` WHERE  `id` >37";
    	
    	Shopware()->Db()->query($sql);
	}
}
