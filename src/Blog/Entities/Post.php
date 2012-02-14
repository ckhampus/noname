<?php

namespace Blog\Entities;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Entity
 */
class Post 
{
    /** 
     * @Id 
     * @GeneratedValue 
     * @Column(type="integer") 
     */
    private $id;

    /**
     * @Column(type="string", length=128)
     */
    private $title;

    /**
     * @Column(type="text")
     */
    private $content;

    /**
     * @var datetime $created_at
     *
     * @Timestampable(on="create")
     * @Column(type="datetime")
     */
    private $created_at;

    /**
     * @var datetime $updated_at
     *
     * @Timestampable(on="update")
     * @Column(type="datetime")
     */
    private $updated_at;

    /**
     * Get the post id.
     * 
     * @return int The post id.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the post title.
     * 
     * @return string The post title.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the post title.
     * 
     * @param $value The new post title.
     */
    public function setTitle($value)
    {
        $this->title = $value;
    }

    /**
     * Get the post content.
     * 
     * @return string The post content.
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the post content.
     * 
     * @param $value The new post content.
     */
    public function setContent($value)
    {
        $this->content = $value;
    }

    /**
     * Get the date and time this post was created.
     * 
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Get the date and time this post was last updated.
     * 
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
}