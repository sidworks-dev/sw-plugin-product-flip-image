<?php declare(strict_types=1);

namespace Sidworks\ProductFlipImage\Core\Content;

use Shopware\Core\Content\Product\Aggregate\ProductMedia\ProductMediaDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\NoConstraint;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new FkField(
                'flip_id',
                'flipId',
                ProductMediaDefinition::class
            ))->addFlags(new ApiAware(), new Inherited(), new NoConstraint())
        );

        $collection->add(
            (new ManyToOneAssociationField(
                'flip',
                'flip_id',
                ProductMediaDefinition::class,
                'id',
                true
            ))->addFlags(new ApiAware(), new Inherited())
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}
