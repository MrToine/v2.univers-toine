<?php

namespace App\Controller\Admin;

use App\Entity\NewsCats;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class NewsCatsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return NewsCats::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
