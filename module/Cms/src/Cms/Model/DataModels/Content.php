<?php
namespace Cms\Model\DataModels;

use Application\Model\AbstractModels\AbstractModel;

class Content extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
	public $title;

    /**
     * @var string
     */
	public $url;

    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    public $exceptedRoles;

    public $updated;

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

	/**
	 * @param bool $asArray
	 *
	 * @return string | array
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