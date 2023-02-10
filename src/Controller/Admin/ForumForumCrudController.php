<?php

namespace App\Controller\Admin;

use App\Entity\ForumForum;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ForumForumCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ForumForum::class;
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
