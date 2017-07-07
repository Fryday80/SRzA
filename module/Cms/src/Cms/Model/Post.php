<?php
namespace Cms\Model;

class Post implements PostInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $exceptedRoles;

    protected $updated;

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getExceptedRoles($asArray = false)
    {
        return ($asArray)? explode(",", $this->exceptedRoles) : $this->exceptedRoles;
    }
    /**
     * @param string | array $roles
     */
    public function setExceptedRoles($roles)
    {
        if (is_array($roles)) $roles = implode(',', $roles);
        $this->exceptedRoles = $roles;
    }
    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
    /**
     * {@inheritDoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $text
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }
}