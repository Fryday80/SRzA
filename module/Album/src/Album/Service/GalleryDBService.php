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
 * Manages the db <-> folder mapping
 * for additional (db) information
 */
Class GalleryDBService
{
    protected $matched = array();
    protected $directories = array();
    protected $db_dirs = array();
    protected $refactored_db_results = array();
    protected $mapped = array ();

    protected $error_occurred_in = 'error occurred ind GalleryDBService';


    /**
     * Mapps folder names on db entrys
     *
     * checks if db is up to date
     *
     * @return array of matched db-folder links
     */
    public function get_gallery_db_mapping ()
    {
        $changes = $this->getTheParts();        // check if all is up-to-date
        if (!empty($changes))                   // fallback if not up-to-date
        {
            $this->gallery_db_mapping_update();
        }

        $this->mapping();
        return $this->mapped;

    }

    /**
     * updates db with local folders
     */
    public function gallery_db_mapping_update ()
    {
        $changes = $this->getTheParts();
        $new    = $changes['new'];
        $gone   = $changes['gone'];

        if (!empty($new))
        {
            $this->dbupdate ($new);
            $changes = $this->compare($this->directories, $this->db_dirs);
            if ($changes['new'] !=='')
            {
                echo ("$this->error_occurred_in gallery_db_mapping_update write");
            }
        }

        if (!empty($gone))
        {
            $this->deleteGoneItems($gone);
            $changes = $this->compare($this->directories, $this->db_dirs);
            if ($changes['gone'] !=='')
            {
                echo ("$this->error_occurred_in gallery_db_mapping_update delete");
            }
        }
    }

    /**
     * mapping function
     *
     * sets $this->mapped
     */
    private function mapping ()
    {
        foreach ($this->matched as $foldername)
        {
            $this->refactor_db_results();
            $id = $this->refactored_db_results['folder']["$foldername"];
            $this->mapped = array (
                $id => array (
                    'id'=> $id,
                    'folder' => $this->refactored_db_results['id']["$id"]['folder'],
                    'vis' => $this->refactored_db_results['id']["$id"]['vis']
                )
            );
        }
    }


    /**
     * get the parts needed for comparison and update and finally $this->matched
     *
     * sets $this->directories and $this->db_dirs
     *
     * @return mixed array with keys "new" and "gone"
     */
    private function getTheParts ()
    {
        $t = $this->getServiceLocator()->get('MediaService');
        $this->directories = $t->getAlbumFolderNames();

        $this->read_db();
        $changes = $this->compare($this->directories, $this->db_dirs);
        return $changes;
    }

    /**
     * compares local folders with db entries
     *
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

    /* ---------db functions ---------*/ // Auslagern in eigenen service??

    /**
     * reads out db
     * into $this->db_dirs
     */
    private function read_db()
    {
        $this->db_dirs = '$results of db "link tab" read';
    }

    /**
     * db update action
     *
     * @param $dir_array_new array of new folders
     */
    private function dbupdate ($dir_array_new)
    {
        foreach ($dir_array_new as $foldername) {
            // INSERT 'foldername' = $foldername, 'vis' =  1
        }
    }

    /**
     * db items delete
     *
     * @param $gone_folders array of deleted folders
     */
    private function deleteGoneItems ($gone_folders)
    {
        foreach ($gone_folders as $foldernames) {
            //DELETE WHERE 'foldername' = $foldername
        }
    }

    /**
     * refactors the db result array
     *
     * @param $results pure db result array
     *
     * sets $this->refactored_db_results
     */
    private function refactor_db_results ($results)
    {
        //$results to array; ///  vars anpassen!!!!!!
        $this->refactored_db_results = array (
                                        'id'    => array (
                                                            $this->db_dirs['id'] => array (
                                                                            'id'        => $this->db_dirs['id'],
                                                                            'folder'    => $this->db_dirs['folder'],
                                                                            'vis'       => $this->db_dirs['vis']
                                                        )),
                                        'folder' => array(
                                                        $this->db_dirs['folder'] => $this->db_dirs['id']
                                        ));
    }
}