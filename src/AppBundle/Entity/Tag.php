<?php
namespace AppBundle\Entity;

use AppBundle\Entity\Traits\TitleTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Tag
 *
 * @category Entity
 * @package  AppBundle\Entity
 * @author   Anton Serra <aserratorta@gmail.com>
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagRepository")
 */
class Tag extends AbstractBase
{
    use TitleTrait;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Post", mappedBy="tag")
     */
    private $posts;

    /**
     *
     *
     * Methods
     *
     *
     */

    /**
     * Tag constructor.
     * @param ArrayCollection $posts
     */
    public function __construct(ArrayCollection $posts)
    {
        $this->posts = $posts;
    }

    /**
     * @return ArrayCollection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param ArrayCollection $posts
     * @return Tag
     */
    public function setPosts(ArrayCollection $posts)
    {
        $this->posts = $posts;
        return $this;
    }

    public function __toString() {

        return $this->getTitle() ? $this->getTitle() : '---';
    }

    /**
     * Add post
     *
     * @param Post $post
     *
     * @return Category
     */
    public function addPost(Post $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param Post $post
     *
     * @return Category
     */
    public function removePost(Post $post)
    {
        $this->posts->removeElement($post);

        return $this;
    }
}