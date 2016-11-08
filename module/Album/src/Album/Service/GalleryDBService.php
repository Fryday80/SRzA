<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 08.11.2016
 * Time: 21:47
 */

/**
 * Class GalleryDBService
 *
 * Manages the db - folder mapping
 * for additional (db) information
 */
Class GalleryDBService
{
    protected $new_dirs = array();
    protected $matched = array();


    /**
     * Mapps folder names on db entrys
     *
     * @return array
     */
    public function gallery_db_mapping ()
    {
        return $this->refactor_db_results($this->read_db());
    }

    /**
     * updates db with local folders
     */
    public function gallery_db_mapping_update ()
    {
        $t = $this->getServiceLocator()->get('MediaService');
        $directorys = $t->getAlbumFolderNames();

        $db_dirs = $this->read_db();
        $changes = $this->compare($directorys, $db_dirs);
        $new    = $changes['new'];
        $gone   = $changes['gone'];

        if (!empty($new))
        {
            $this->dbupdate ($new);
            $new_dir = $this->compare($directorys, $db_dirs);
            if ($new_dir !=='')
            {
                echo ('here is something wrong with the db in the GalleryDBService!!');
            }
        }

        if (!empty($gone))
        {
            $this->deleteGoneItems($gone);
        }
    }

    /**
     * reads out db
     *
     * @return array
     */
    private function read_db()
    {
        $db = 'db "link tab" auslesen';
        return $db;
    }

    /**
     * @param $directories
     * @param $db_results
     * @return mixed array with keys "new" and "gone"
     */
    private function compare ($directories, $db_results)
    {
        $gone_folders = array();
        $new_dirs = array();
        foreach ($directories as $local_dir)
        {
            if (in_array($local_dir, $db_results))
            {
                array_push($this->matched, $local_dir);
            }
            else
            {
                array_push($new_dirs, $local_dir);
            }
        }

        foreach ($db_results['folder'] as $folder_name)
        {
            if ( !in_array($folder_name, $directories))
            {
                array_push($gone_folders, $directories);
            }
        }
        $result['gone'] = $gone_folders;
        $result['new']  = $new_dirs;
        return $result;
    }

    /**
     * db update action
     *
     * @param $dir_array_new array of new folders
     */
    private function dbupdate ($dir_array_new)
    {
        // write $dir_array to db
    }

    /**
     * db items delete
     *
     * @param $gone_folders array of deleted folders
     */
    private function deleteGoneItems ($gone_folders)
    {
        // delete $gone_folders from db
    }

    /**
     * refactors the db result array
     *
     * @param $results pure db result array
     *
     * @return mixed refactored array
     */
    private function refactor_db_results ($results)
    {
        //$results to array;
        $refactored_db_results = $results;
        return $refactored_db_results;
    }
}