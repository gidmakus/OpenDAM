<?php 
class RightUtils
{
	/*________________________________________________________________________________________________________________*/
	/**
	 * 
	 */
	public static function getObjectForAlbumAndUser(Groupe $album, User $user)
	{
		$userAlbum = UserGroupPeer::retrieveByGroupIdAndUserId($album->getId(), $user->getId());

		if ($userAlbum) {
			return $userAlbum;
		}

		$groupAlbum = UnitGroupPeer::retrieveMinRoleByGroupIdAndUserId($album->getId(), $user->getId());

		if ($groupAlbum) {
			return $groupAlbum;
		}

		return $album;
	}

	/*________________________________________________________________________________________________________________*/
	/**
	 * Renvoie true si l'utilisateur peut mettre à jour un album (update).
	 * 
	 * Un utilisateur peut modifier un album s'il est propriétaire, ou manager de cette album.
	 * 
	 * @return boolean
	 */
	public static function canUpdateAlbum(Groupe $album)
	{
		$user = sfContext::getInstance()->getUser()->getInstance();

		/* if ($album->getUserId() == $user->getId()) {
			return true;
		} */

		if ($user->getRole() == RolePeer::__ADMIN && $album->getCustomerId() == $user->getCustomerId()) {
			return true;
		}

		$xHasRight = self::getObjectForAlbumAndUser($album, $user);

		if ($xHasRight) {
			return $xHasRight->getCredential()->getId() == RolePeer::__ADMIN;
		}

		return false;
	}


	/*________________________________________________________________________________________________________________*/
	public static function getAccessForFolderAndUser(Folder $folder, User $user)
	{
		$object = null;
		$inherit = null;

		if ($folder->getFree()) {
			$access = true;
		}
		else {
			$access = false;
		}

		$userFolder = UserFolderPeer::retrieveByUserAndFolder($user->getId(), $folder->getId());

		if ($userFolder) {
			$access = !$access;
			$object = $userFolder;
		}

		$userGroups = UserUnitPeer::retrieveByUser($user->getId());

		$userFolderGroups = UnitFolderPeer::retrieveByUserIdAndFolderId($user->getId(), $folder->getId());

		if (($folder->getFree() && count($userFolderGroups) == count($userGroups) && $userFolderGroups) ||
		(!$folder->getFree() && $userFolderGroups)) {
			if (!$object) {
				$access = true;
				$object = current($userFolderGroups)->getUnit();
			}
			else {
				$inherit = current($userFolderGroups)->getUnit();
			}
		}
		else {
			if (!$object) {
				if (count($userGroups) > 0) {
					$countAccess = 0;
					$countNoAccess = 0;

					foreach ($userGroups as $userGroup) {
						$userUnitFolder = UnitFolderPeer::retrieveByUnitIdAndFolderId($userGroup->getId(), $folder->getId());
	
						if ($userUnitFolder) {
							if ($userUnitFolder->getRole()) {
								$countAccess++;
							}
							else {
								$countNoAccess++;
							}
						}
					}

					if (!$countAccess && $countNoAccess > 0) {
						$access = false;
					}
				}
			}
		}

		$album = $folder->getGroupe();

		if (!$folder->getSubfolderId()) {
			if ($album->getFree()) {
				if (!$object) {
					$object = $folder;
				}
			}
			else {
				$userAlbum = UserGroupPeer::retrieveByGroupIdAndUserId($album->getId(), $user->getId());
	
				if ($userAlbum) {
					if (!$object) {
						$object = $folder;
					}
				}
				else {
					$userAlbumGroups = UnitGroupPeer::retrieveByAlbumIdAndUserId($album->getId(), $user->getId());

					if ($userAlbumGroups) {
						if (!$object) {
							$object = current($userAlbumGroups)->getUnit();
						}
						else {
							$inherit = current($userAlbumGroups)->getUnit();
						}
					}
					else {
						if (!$object) {
							$access = false;
							$object = null;
						}
					}
				}
			}
		}
		else {
			$isUserException = $object instanceof UserFolder;

			if (!$isUserException) {
				if(!$folder->getFree() && $access == false) {
					$object = $folder;
				}
				else {
					$recursiveAccess = self::getRecursiveAccessForFolderAndUser($folder, $user);

					$access = $recursiveAccess["access"];
					$object = $recursiveAccess["object"];
				}
			}
		}

		if (!$access) {
			if (($user->getRoleId() == RolePeer::__ADMIN && $folder->getCustomerId() == $user->getCustomerId()) ||
				$user->getRoleId() == RolePeer::__SUPER_ADMIN) {
				$access = true;
			}
			else {
				$album = $folder->getGroupe();
				$xHasRight = self::getObjectForAlbumAndUser($album, $user);

				if ($xHasRight && $xHasRight->getCredential()->getId() == RolePeer::__ADMIN) {
					$access = true;
				}
			}
		}

		return array(
			"access"	=> $access,
			"object"	=> $object,
			"inherit"	=> $inherit
		);
	}

	protected static function getRecursiveAccessForFolderAndUser(Folder $folder, User $user, &$firstObject = null)
	{
		if ($folder->getSubfolderId()) {
			$count = 0;
			$object = null;
			$currentFolder = FolderPeer::retrieveByPK($folder->getSubfolderId());

			$userFolder = UserFolderPeer::retrieveByUserAndFolder($user->getId(), $currentFolder->getId());

			if (($currentFolder->getFree() && $userFolder) || (!$currentFolder->getFree() && !$userFolder)) {
				$count++;
		
				$object = $currentFolder;
			}
	
			$unitFolder = UnitFolderPeer::retrieveByUserIdAndFolderId($user->getId(), $currentFolder->getId());
	
			if (!$unitFolder) {
				$count++;
			}
			else {
				$object = current($unitFolder)->getUnit();
			}
	
			if (!$firstObject) {
				$firstObject = $object;
			}

			if ($count == 2) {
				return array(
					"access"	=> false,
					"object"	=> $firstObject
				);
			}

			self::getRecursiveAccessForFolderAndUser($currentFolder, $user, $firstObject);
		}

		return array(
				"access"	=> true,
				"object"	=> $firstObject
		);
	}
}
?>