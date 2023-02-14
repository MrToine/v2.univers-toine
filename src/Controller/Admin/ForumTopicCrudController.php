<?php

namespace App\Controller\Admin;

use App\Entity\ForumTopic;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ForumTopicCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ForumTopic::class;
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
