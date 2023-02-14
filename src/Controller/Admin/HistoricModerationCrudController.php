<?php

namespace App\Controller\Admin;

use App\Entity\Member;
use App\Entity\HistoricModeration;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class HistoricModerationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HistoricModeration::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('ip_adress'),
            TextField::new('user.displayName'),
            DateTimeField::new('createAt'),
            TextField::new('content'),
        ];
    }
}
