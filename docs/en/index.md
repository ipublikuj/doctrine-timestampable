# Quickstart

Timestampable behavior will automate the update of date fields on your Entities in [Nette Framework](http://nette.org/) and [Doctrine 2](http://www.doctrine-project.org/)
This extension is using annotations and can update fields on creation, update, delete, property subset update, or even on specific property value change.

## Installation

The best way to install **ipub/doctrine-timestampable** is using [Composer](http://getcomposer.org/):

```sh
composer require ipub/doctrine-timestampable
```

After that you have to register extension in config.neon.

```neon
extensions:
    doctrineTimestampable: IPub\DoctrineTimestampable\DI\DoctrineTimestampableExtension
```

## Usage

### Annotation syntax

**@IPub\Mapping\Annotation\Timestampable** used in entity property, tells that this column is timestampable.

#### Available options

* **on**: main and required option which define event when a change should be triggered. Allowed values are: **create**, **update**, **change** and **delete**.
* **field**: is used only for event **change** and specifies tracked property. When the tracked property is changed, event is triggered.
* **value**: is used only for event change and with tracked field. When the value of tracked field is same as defined, event is triggered.

### Example entity

```php
<?php
namespace Your\Cool\Namespace;

use Doctrine\ORM\Mapping as ORM;
use IPub\Mapping\Annotation as IPub;

/**
 * @ORM\Entity
 */
class Article
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $title;

    /**
     * @ORM\Column(name="body", type="string")
     */
    private $body;

    /**
     * @var \DateTime|NULL $createdAt
     *
     * @IPub\Timestampable(on="create")
     * @ORM\Column(type="string")
     */
    private $createdAt;

    /**
     * @var \DateTime|NULL $updatedAt
     *
     * @IPub\Timestampable(on="update")
     * @ORM\Column(type="string")
     */
    private $updatedAt;

    /**
     * @var \DateTime|NULL $contentChangedAt
     *
     * @ORM\Column(name="content_changed_at", type="string", nullable=true)
     * @IPub\Timestampable(on="change", field={"title", "body"})
     */
    private $contentChangedAt;

    // ...

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getContentChangedAt()
    {
        return $this->contentChangedAt;
    }

}
```

### Automatic associations

Columns doesn't have to be specified with doctrine ORM definition, this extension can map them automatically, all depends on how you configure it.

#### Automatic associations

```php
<?php
namespace Your\Cool\Namespace;

use Doctrine\ORM\Mapping as ORM;
use IPub\Mapping\Annotation as IPub;

/**
 * @ORM\Entity
 */
class Article
{

    /**
     * @var \DateTime|NULL $createdAt
     *
     * @IPub\Timestampable(on="create")
     */
    private $createdAt;

}
```

Entity property ```$createdAt``` will be mapped as string field with column name ```created_at```

### Using dependency of property changes

One entity can relay on other entity, and field updating could be triggered after external change:

```php
<?php
namespace Your\Cool\Namespace;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Type
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="type")
     */
    private $articles;

    // ...

}
```

And we want to monitor when the article is **published** - the type is changed.

```php
<?php
namespace Your\Cool\Namespace;

use Doctrine\ORM\Mapping as ORM;
use IPub\Mapping\Annotation as IPub;

/**
 * @ORM\Entity
 */
class Article
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="articles")
     */
    private $type;

    /**
     * @var \DateTime|NULL $publishedAt
     *
     * @ORM\Column(type="string", nullable=true)
     * @IPub\Timestampable(on="change", field="type.title", value="Published")
     */
    private $publishedAt;

    // ...

}
```

When the article type is changed to **Published** event for updating ```$publishedAt``` will be triggered.

You can even monitor more than one value:

```php
<?php
namespace Your\Cool\Namespace;

use Doctrine\ORM\Mapping as ORM;
use IPub\Mapping\Annotation as IPub;

/**
 * @ORM\Entity
 */
class Article
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="articles")
     */
    private $type;

    /**
     * @var \DateTime|NULL $publishedBy
     *
     * @ORM\Column(type="string", nullable=true)
     * @IPub\Timestampable(on="change", field="type.title", value={"Published", "Deleted"})
     */
    private $publishedBy;

    // ...

}
```

Now property ```$publishedBy``` will be changed when article type is set to **Published** or **Deleted**

### Using traits

You can use extension traits for quick createdAt updatedAt property definitions. This traits are splitted into three, one for entity creation, one for updating and one for deleting entity.

```php
<?php
namespace Your\Cool\Namespace;

use Doctrine\ORM\Mapping as ORM;
use IPub\DoctrineTimestampable\Entities;

/**
 * @ORM\Entity
 */
class UsingTrait implements Entities\IEntityCreated, Entities\IEntityUpdated, Entities\IEntityRemoved
{

    /**
     * Hook timestampable behavior for entity author
     * updates createdAt field
     */
    use Entities\TEntityCreated;

    /**
     * Hook timestampable behavior for entity editor
     * updates updatedAt field
     */
    use Entities\TEntityUpdated;

    /**
     * Hook timestampable behavior for entity deleter
     * updates deletedAt field
     */
    use Entities\TEntityRemoved;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(length=128)
     */
    private $title;

}
```
