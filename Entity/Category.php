<?php

namespace Ok99\PrivateZoneCore\ClassificationBundle\Entity;

use Ok99\PrivateZoneCore\MediaBundle\Entity\Media;
use Ok99\PrivateZoneCore\UserBundle\Entity\User;
use Sonata\ClassificationBundle\Entity\BaseCategory as BaseCategory;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="classification__category")
 * @ORM\Entity(repositoryClass="Ok99\PrivateZoneCore\ClassificationBundle\Entity\Repository\CategoryRepository")
 */
class Category extends BaseCategory
{
    /**
     * @var integer $id
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Context")
     * @ORM\JoinColumn(name="context_id", referencedColumnName="id", nullable=true)
     */
    protected $context;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    /**
     * @ORM\ManyToOne(targetEntity="Ok99\PrivateZoneCore\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=true)
     */
    protected $media;

    /**
     * @ORM\ManyToMany(targetEntity="Ok99\PrivateZoneCore\UserBundle\Entity\User")
     * @ORM\JoinTable(name="classification__category__users",
     *      joinColumns={@ORM\JoinColumn(name="classification__category__id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="RESTRICT")})
     */
    private $allowedUsers;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_notifiable", type="boolean")
     */
    private $isNotifiable = true;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->allowedUsers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->enabled = true;
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set context
     *
     * @param Context $context
     * @return Category
     */
    public function setContext(\Sonata\ClassificationBundle\Model\ContextInterface $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context
     *
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set parent
     *
     * @param Category $parent
     * @return Category
     */
    public function setParent(\Sonata\ClassificationBundle\Model\CategoryInterface $parent = null, $nested = false)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set media
     *
     * @param \Ok99\PrivateZoneCore\MediaBundle\Entity\Media $media
     * @return Category
     */
    public function setMedia(\Sonata\MediaBundle\Model\MediaInterface $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return \Ok99\PrivateZoneCore\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }


    /**
     * Add allowedUser
     *
     * @param \Ok99\PrivateZoneCore\UserBundle\Entity\User $allowedUser
     * @return Category
     */
    public function addAllowedUser(\Ok99\PrivateZoneCore\UserBundle\Entity\User $allowedUser)
    {
        $this->allowedUsers[] = $allowedUser;

        return $this;
    }

    /**
     * Remove allowedUser
     *
     * @param \Ok99\PrivateZoneCore\UserBundle\Entity\User $allowedUser
     */
    public function removeAllowedUser(\Ok99\PrivateZoneCore\UserBundle\Entity\User $allowedUser)
    {
        $this->allowedUsers->removeElement($allowedUser);
    }

    /**
     * Get allowedUsers
     *
     * @return User[]
     */
    public function getAllowedUsers()
    {
        $allowedUsers = $this->allowedUsers->getValues();

        $collator = new \Collator('cs_CZ');
        $collator->sort($allowedUsers);

        return $allowedUsers;
    }

    /**
     * Set isNotifiable
     *
     * @param boolean $isNotifiable
     * @return Category
     */
    public function setIsNotifiable(bool $isNotifiable)
    {
        $this->isNotifiable = $isNotifiable;

        return $this;
    }

    /**
     * Get isNotifiable
     *
     * @return boolean
     */
    public function getIsNotifiable()
    {
        return $this->isNotifiable;
    }
}
