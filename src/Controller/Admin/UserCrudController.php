<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions
                     ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                         return $action->setLabel('Создать пользователя');
                     });
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
                 ->setEntityLabelInPlural('Пользователи')
                 ->setEntityLabelInSingular('Пользователь')
                 ->setPageTitle('new', 'Добавдение пользователя')
                 ->setPageTitle('edit', 'Изменение пользователя');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
                     ->onlyOnIndex();

        yield TextField::new('username', 'Логин')
                     ->setColumns(4);

        yield TextField::new('password', 'Пароль')
                     ->onlyWhenCreating()
                     ->setColumns(4);

        yield ChoiceField::new('roles', 'Права')
                     ->setRequired(true)
                     ->allowMultipleChoices()
                     ->renderExpanded()
                     ->setChoices(User::ROLES);
    }
}
