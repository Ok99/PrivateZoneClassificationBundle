<?php

namespace Ok99\PrivateZoneCore\ClassificationBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Ok99\PrivateZoneCore\ClassificationBundle\Entity\Category;
use Ok99\PrivateZoneCore\UserBundle\Entity\User;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createDocumentsQuery()
    {
        return $this->createQueryBuilder('c')
            ->where('c.context = :context')
            ->andWhere('c.enabled = :true')
            ->andWhere('c.parent IS NOT NULL')
            ->orderBy('c.position')
            ->setParameter('context', 'documents')
            ->setParameter('true', true);
    }

    /**
     * @return Category
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDocumentsCategory()
    {
        try {
            return $this->createDocumentsQuery()
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @param User|null $user
     * @return Category[]
     */
    public function getDocumentsCategories($user = null)
    {
        try {
            $categories = $this->createDocumentsQuery()
                ->getQuery()
                ->getResult();

            if ($user) {
                $categories = array_filter($categories, function (Category $category) use ($user) {
                    return
                        !$category->getAllowedUsers()
                        || in_array($user, $category->getAllowedUsers());
                });
            }

            return $categories;
        } catch (NoResultException $e) {
            return [];
        }
    }

    /**
     * @param User|null $user
     * @return Category[]
     */
    public function getNotifiableDocumentsCategories($user = null)
    {
        $categories = $this->getDocumentsCategories($user);
        return array_filter($categories, function (Category $category) {
            return $category->getIsNotifiable();
        });
    }
}
