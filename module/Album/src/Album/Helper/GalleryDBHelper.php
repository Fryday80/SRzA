<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 08.11.2016
 * Time: 21:47
 */

Class GalleryDBHelper {
    protected $new_dirs = array();
    protected $matched = array();


    public function first_link ()
        {
            $t = $this->getServiceLocator()->get('MediaService');
            $directorys = $t->getAlbumFolderNames();

            $db_dirs = $this->read_db();
            $this->compare($directorys, $db_dirs);

            // add $new_dirs in db
        }

    private function read_db() {
        $db = 'db "link tab" auslesen';
        return $results;
    }

    private function compare ($directorys, $db_results) {
        // $db und $directorys vergleichen und zuordnen;
        foreach ($directorys as $local_dir){
            if ($db_results['name'] == $local_dir) {
                $this->matched = array (
                    $db_results['id'] = array (
                        'id' => $db_results['id'],
                        'name'  => $db_results['name']
                    )
                )
            }
        }

        $new_dirs = 'dirs not in db';

    }
}