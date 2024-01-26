<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RoomCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Room::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Identification')
                ->setIcon('home')->addCssClass('optional')
                ->setHelp('All information about the room'),
            TextField::new('title'),
            TextEditorField::new('description'),
            FormField::addRow('xl'),
            MoneyField::new('price')->setCurrency('EUR'),
            // FormField::addTab('Second Tab'),
            TextField::new('address')->hideOnIndex(),
            TextField::new('city'),
            TextField::new('country')->hideOnIndex(),
            ImageField::new('cover')
                ->setBasePath('uploads/rooms/')
                ->setUploadDir('public/uploads/rooms/'),
        ];
    }

}
