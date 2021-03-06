<?php


/**
 * Skeleton subclass for performing query and update operations on the 'file_waiting' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * Thu Sep  1 12:28:12 2011
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class FileWaitingPeer extends BaseFileWaitingPeer 
{
	const __STATE_WAITING_VALIDATE = 1;
	const __STATE_VALIDATE = 2;
	const __STATE_WAITING_DELETE = 3;
	const __STATE_DELETE = 4;

	/*________________________________________________________________________________________________________________*/
	public static function haveWaitingFile($user_id, $file_id, $type)
	{
		$c = new Criteria();
		
		$c->add(self::USER_ID, $user_id);
		$c->add(self::FILE_ID, $file_id);
		$c->add(self::STATE, $type);

		return self::doSelectOne($c);
	}

	/*________________________________________________________________________________________________________________*/
	public static function retrieveByFileIdAndType($file_id, $type)
	{
		$c = new Criteria();
		
		$c->add(self::FILE_ID, $file_id);
		$c->add(self::STATE, $type);

		return self::doSelectOne($c);
	}

	/*________________________________________________________________________________________________________________*/
	public static function getWaintingFilesPager($group_id, $keyword, $state, $sort = "name_asc", $page = 1, $perPage = 10)
	{
		if (!is_array($state)) {
			$state = array($state);
		}

		$c = new Criteria();
		$c->addJoin(GroupePeer::ID, FilePeer::GROUPE_ID);
		$c->addJoin(FilePeer::ID, self::FILE_ID);
		$c->add(GroupePeer::ID, $group_id);
		$c->add(self::STATE, $state, Criteria::IN);

		if($keyword && $keyword != __("search") && $keyword != __("Search"))
		{
			$keyword = htmlentities(replaceAccentedCharacters($keyword), ENT_QUOTES);

			$c1 = $c->getNewCriterion(FilePeer::NAME, "%".$keyword."%", Criteria::LIKE);
			$c2 = $c->getNewCriterion(FilePeer::ORIGINAL, "%".$keyword."%", Criteria::LIKE);
			$c3 = $c->getNewCriterion(FilePeer::DESCRIPTION, "%".$keyword."%", Criteria::LIKE);
			$c4 = $c->getNewCriterion(UserPeer::EMAIL, "%".$keyword."%", Criteria::LIKE);
			$c5 = $c->getNewCriterion(UserPeer::FIRSTNAME, "%".$keyword."%", Criteria::LIKE);
			$c6 = $c->getNewCriterion(UserPeer::LASTNAME, "%".$keyword."%", Criteria::LIKE);

			$c1->addOr($c2);
			$c1->addOr($c3);
			$c1->addOr($c4);
			$c1->addOr($c5);
			$c1->addOr($c6);

			$c->addJoin(FilePeer::USER_ID, UserPeer::ID);
			$c->add($c1);
		}

		switch ($sort) {
			case "name_asc":
				$c->addAscendingOrderByColumn(FilePeer::NAME);
				$c->addAscendingOrderByColumn(FilePeer::ORIGINAL);
			break;

			case "name_desc":
				$c->addDescendingOrderByColumn(FilePeer::NAME);
				$c->addDescendingOrderByColumn(FilePeer::ORIGINAL);
			break;

			case "user_asc":
				$c->addAscendingOrderByColumn(UserPeer::EMAIL);
			break;

			case "user_desc":
				$c->addDescendingOrderByColumn(UserPeer::EMAIL);
			break;

			case "date_asc":
				$c->addAscendingOrderByColumn(FilePeer::CREATED_AT);
				break;
			
			case "date_desc":
				$c->addDescendingOrderByColumn(FilePeer::CREATED_AT);
			break;
		}

		$pager = new sfPropelPager("FileWaiting", $perPage);
		$pager->setCriteria($c);
		$pager->setPage($page);
		$pager->setPeerMethod("doSelect");
		$pager->init();

		return $pager;
	}
}
