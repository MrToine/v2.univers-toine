<?php

namespace App\EntityListener;

use App\Entity\Member;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberListener 
{

	private UserPasswordHasherInterface $hasher;

	public function __construct(UserPasswordHasherInterface $hasher)
	{
		$this->hasher = $hasher;
	}

	public function prePersist(Member $user)
	{
		$this->encodePassword($user);
	}

	public function preUpdate(Member $user)
	{
		$this->encodePassword($user);
	}

	/**
	 * Encode un mot de passe basé sur plainPassword
	 * @param  User   $user [description]
	 * @return [type]       [description]
	 */
	public function encodePassword(Member $user)
	{
		if($user->getPlainPassword() === null)
		{
			return;
		}

		$user->setPassword(
			$this->hasher->hashPassword(
				$user,
				$user->getPlainPassword()
			)
		);

		$user->setPlainPassword("");
	}
}