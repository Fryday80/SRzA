<?php
namespace Media\Service;

class MediaService {

    function __construct() {
    }

    function import() {
        //check import folder for files, and if any, import them.
        //if gallery folder has a sub folder check if this folder exists allready
        //then move content images to storage
        //if not move folder and add permission
        //clear import folder
    }

    /**
     * returns a multidimensional array with folders
     */
    function getAllFolder() {
        return array(
            'name',
            'path',
            'folders' => array(
                'name',
                'path',
                'folders' => array())
        );
    }
    /**
     * @param {string} $path relative path to the data folder
     */
    function getFolder($path) {
        //path exists
        //load folder.perm
        //check permission   permission template = recource is media  privileg is /path/to/folder
        //if no permission ?? redirect to login ?? or return false
        //return array with file paths'
    }
}