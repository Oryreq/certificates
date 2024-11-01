<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
                     ->onlyOnIndex();

        yield TextField::new('username', 'Логин');

        yield TextField::new('plainPassword', 'Пароль')
                     ->onlyWhenCreating();

        yield ChoiceField::new('roles', 'Права')
                     ->setRequired(true)
                     ->allowMultipleChoices()
                     ->renderExpanded()
                     ->setChoices(User::ROLES);
    }
}
