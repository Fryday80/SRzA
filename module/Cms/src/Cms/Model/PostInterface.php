<?php
namespace Cms\Model;

interface PostInterface
{
    /**
     * Will return the ID of the page
     *
     * @return int
     */
    public function getId();

    /**
     * Will return the TITLE of the page
     *
     * @return string
     */
    public function getTitle();

    /**
     * Will return the URL of the page
     *
     * @return string
     */
    public function getUrl();

    /**
     * Will return the TEXT of the page
     *
     * @return string
     */
    public function getContent();

    public function getExceptedRoles($asArray = false);
}