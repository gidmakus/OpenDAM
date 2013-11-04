<?php


/**
 * Skeleton subclass for performing query and update operations on the 'country' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * Mon Jun 27 11:11:12 2011
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class CountryPeer extends BaseCountryPeer
{
	const ID_FRANCE = 70;
	const CODE_FRANCE = 33;

	const ID_USA = 66;
	
	/*________________________________________________________________________________________________________________*/
	public static function findAll()
	{
		$c = new Criteria();
	
		$c->addAscendingOrderByColumn(CountryI18nPeer::TITLE);
	
		return self::doSelectWithI18n($c, sfContext::getInstance()->getUser()->getCulture());
	}
	
	/*________________________________________________________________________________________________________________*/
	public static function getInArray()
	{
		$c = new Criteria();
		
		$c->addAscendingOrderByColumn(CountryI18nPeer::TITLE);

		$countrys = self::doSelectWithI18n($c, sfContext::getInstance()->getUser()->getCulture());
		$country_array = Array();

		foreach ($countrys as $country) {
			$country_array[$country->getId()] = $country->getTitle();
		}

		return $country_array;
	}

	/*________________________________________________________________________________________________________________*/
	public static function retrieveByTitle($title)
	{
		$c = new Criteria();
		
		$c->addJoin(self::ID, CountryI18nPeer::ID, Criteria::INNER_JOIN);
		$c->add(CountryI18nPeer::CULTURE, sfContext::getInstance()->getUser()->getCulture());
		$c->add(CountryI18nPeer::TITLE, $title);

		return self::doSelectOne($c);
	}

	/*________________________________________________________________________________________________________________*/
	public static function retrieveByContinentId($continent_id)
	{
		$c = new Criteria();
		
		$c->add(self::CONTINENT_ID, $continent_id);
		$c->addJoin(CountryI18nPeer::ID, self::ID);
		$c->add(CountryI18nPeer::CULTURE, sfContext::getInstance()->getUser()->getCulture());
		$c->addAscendingOrderByColumn(CountryI18nPeer::TITLE);

		return self::doSelectWithI18n($c);
	}
}