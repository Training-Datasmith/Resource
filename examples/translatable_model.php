<?php

declare(strict_types=1);

/**
 * Sylius Resource Component — translatable entity model example.
 *
 * Shows how to create a translatable Sylius resource using the provided
 * interfaces and traits, without requiring a Symfony kernel.
 *
 * Note: ORM mapping annotations/attributes and Doctrine EntityManager are
 * needed at runtime for persistence; this example shows the model layer only.
 */

require __DIR__ . '/../vendor/autoload.php';

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Resource\Model\AbstractTranslation;

// --- Translation entity (one per locale) ---

class ProductTranslation extends AbstractTranslation implements TranslationInterface
{
    private string $name = '';
    private string $description = '';

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): void { $this->description = $description; }
}

// --- Main product entity ---

class Product implements ResourceInterface, TranslatableInterface, TimestampableInterface
{
    use TranslatableTrait;
    use TimestampableTrait;

    private ?int $id = null;
    private string $sku = '';

    public function getId(): ?int { return $this->id; }
    public function getSku(): string { return $this->sku; }
    public function setSku(string $sku): void { $this->sku = $sku; }

    protected function createTranslation(): ProductTranslation
    {
        return new ProductTranslation();
    }

    public function getName(): string
    {
        return $this->getTranslation()->getName();
    }
}

// --- Usage ---

$product = new Product();
$product->setSku('WIDGET-001');
$product->setCurrentLocale('en');
$product->setFallbackLocale('en');

/** @var ProductTranslation $translation */
$translation = $product->getTranslation();
$translation->setName('Blue Widget');
$translation->setDescription('A sturdy blue widget for all occasions.');

echo 'SKU:         ' . $product->getSku() . PHP_EOL;
echo 'Name (en):   ' . $product->getName() . PHP_EOL;

// Add a French translation
$product->setCurrentLocale('fr');
/** @var ProductTranslation $frTranslation */
$frTranslation = $product->getTranslation();
$frTranslation->setName('Gadget Bleu');
$frTranslation->setDescription('Un gadget bleu robuste pour toutes les occasions.');

echo 'Name (fr):   ' . $product->getName() . PHP_EOL;

// Switch back to English
$product->setCurrentLocale('en');
echo 'Name (en):   ' . $product->getName() . PHP_EOL;
