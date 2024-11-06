<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\VichImageField;
use App\Entity\Certificate;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CertificateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Certificate::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Создать сертификат');
            });
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Сертификаты')
            ->setEntityLabelInSingular('Сертификат')
            ->setPageTitle('new', 'Добавление сертификата')
            ->setPageTitle('edit', 'Изменение сертификата');
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addColumn();
        yield TextField::new('name', 'Название')
                       ->setColumns(6);

        yield TextEditorField::new('description', 'Описание')
                       ->setRequired(true)
                       ->setColumns(6);

        yield TextField::new('shortDescription', 'Краткое описание')
                       ->setRequired(true)
                       ->setColumns(6);

        yield IntegerField::new('price', 'Цена')
                       ->setColumns(6);

        yield VichImageField::new('imageFile', 'Изображение')
                       ->setHelp('
                           <div class="mt-3">
                               <span class="badge badge-info">*.jpg</span>
                               <span class="badge badge-info">*.jpeg</span>
                               <span class="badge badge-info">*.png</span>
                               <span class="badge badge-info">*.webp</span>
                           </div>
                       ')
                       ->setRequired(true)
                       ->onlyOnForms();

        yield VichImageField::new('image', 'Изображение')
                       ->onlyOnIndex();

        yield DateTimeField::new('createdAt', 'Создано')
                       ->onlyOnIndex()
                       ->setRequired(false);
    }
}