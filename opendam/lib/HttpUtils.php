<?php 
class HttpUtils {
	/**
	 * Formate une date pour être utilisé dans une requête HTTP.
	 * ex: Wed, 07 Aug 2013 17:46:33 +0200
	 * 
	 * @param DateTime $datetime
	 * @return string
	 */
	public static function formatDate(DateTime $datetime)
	{
		return $datetime->format(DateTime::RFC1123);
	}

	/**
	 * Parse une date provenant d'une requête HTTP.
	 * 
	 * @param string $str
	 * @return DateTime
	 */
	public static function parseDate($str)
	{
		return new DateTime($str);
	}
	
	/**
	 * Parse une date provenant d'une requête HTTP.
	 *
	 * @param string $str
	 * @return DateTime
	 */
	public static function parseDateToTimestamp($str)
	{
		return strtotime($str);
	}
}
?>