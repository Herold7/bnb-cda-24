<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')

            // in addition to a string, the argument of the singular and plural label methods
            // can be a closure that defines two nullable arguments: entityInstance (which will
            // be null in 'index' and 'new' pages) and the current page name
            ->setEntityLabelInSingular(
                fn (?User $user, ?string $pageName) => $user ? $user->__toString() : 'Utilisateur'
            );
    }

    // Method that configures the actions available for this entity (Show, Edit, Delete)
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    // Method that configures the fields displayed on the CRUD pages and the index page
    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addFieldset('Identification')
                ->setIcon('user')->addCssClass('optional')
                ->setHelp('All information about the user'),
            ImageField::new('image', 'Profile picture')
                ->setBasePath('uploads/users/')
                ->setUploadDir('public/uploads/users/'),
            TextField::new('email', 'Email address'),
            TextField::new('firstname', 'First name'),
            TextField::new('lastname', 'Last name'),
            IntegerField::new('birthyear', 'Birth year'),
            TextField::new('job', 'Job')->hideOnIndex(),
            TextField::new('address', 'Address')->hideOnIndex(),
            TextField::new('city', 'City')->hideOnIndex(),
            TextField::new('country', 'Country')->hideOnIndex(),
        ];
    }
}
