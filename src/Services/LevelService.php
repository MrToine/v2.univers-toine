<?php
namespace App\Services;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Doctrine\ORM\EntityManagerInterface;

class LevelService
{

    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    // Dans le service LevelService
    public function updateLevel($user)
    {   

        $xp = $user->getExperience();
        $level = $this->calculateLevelFromXp($xp);

        if($user->getLevel() != $level)
        {
            // Enregistrer les modifications
            $user->setLevel($level);
            $user->setMoney($user->getMoney() + 100);

            $this->manager->persist($user);
            $this->manager->flush();
        }
    }

    private function calculateLevelFromXp(int $xp)
    {
        $level = 0;

        // Définir les niveaux en fonction de l'XP.
        // Cette implémentation est très simple, mais vous pouvez la personnaliser en fonction de vos besoins.
        if ($xp >= 0 && $xp < 100) {
            $level = 1;
        } elseif ($xp >= 100 && $xp < 200) {
            $level = 2;
        } elseif ($xp >= 200 && $xp < 300) {
            $level = 3;
        } // etc.

        return $level;
    }

}