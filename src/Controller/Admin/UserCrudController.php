<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Service\Attribute\Required;


class UserCrudController extends AbstractCrudController
{
    #[Required]
    public UserPasswordHasherInterface  $passwordEncoder;


    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User && $entityInstance->getPlainPassword()) {
            $password =$this->passwordEncoder->hashPassword($entityInstance, $entityInstance->getPlainPassword());
            $entityInstance->setPassword($password);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User && $entityInstance->getPlainPassword()) {
            $password =$this->passwordEncoder->hashPassword($entityInstance, $entityInstance->getPlainPassword());
            $entityInstance->setPassword($password);
        }

        parent::updateEntity($entityManager, $entityInstance);
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
                 ->setPageTitle('new', 'Добавление пользователя')
                 ->setPageTitle('edit', 'Изменение пользователя');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
                     ->onlyOnIndex();

        yield TextField::new('username', 'Логин')
                     ->setColumns(3);

        yield TextField::new('plainPassword', 'Пароль')
                     ->onlyWhenCreating()
                     ->setRequired(true)
                     ->setColumns(3);

        yield TextField::new('plainPassword', 'Новый пароль')
                     ->onlyWhenUpdating()
                     ->setRequired(true)
                     ->setColumns(3);

        yield TextField::new('email', 'Электронная почта')
                     ->setColumns(3);

        yield ChoiceField::new('roles', 'Права')
                     ->setRequired(true)
                     ->allowMultipleChoices()
                     ->renderExpanded()
                     ->setChoices(User::ROLES);
    }
}
