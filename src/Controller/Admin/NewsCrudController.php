<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Entity\NewsCat;
use App\Entity\Member;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class NewsCrudController extends AbstractCrudController
{   
    public static function getEntityFqcn(): string
    {
        return News::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('category', 'Catégorie');
        yield TextField::new('title', 'Titre');
        yield TextField::new('thumbnail', 'Image à la une');
        yield TextEditorField::new('content', 'Contenu');
        yield AssociationField::new('author', 'Auteur');
        yield DateTimeField::new('createdAt', 'Date de création')->onlyOnIndex();
        yield DateTimeField::new('updatedAt', 'Date de modification')->onlyOnIndex();
        yield IdField::new('published', 'Publié');
    }

}
