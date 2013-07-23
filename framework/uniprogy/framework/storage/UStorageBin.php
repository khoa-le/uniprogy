<?php

/**
 * UStorageBin class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UStorageBin is a class which is used to make all possible manipulations with a storage bin
 * and files in it.
 * 
 * Create new bin and save file into it:
 * <pre>
 * $file = CUploadedFile::getInstance($model,'upload');
 * $bin = app()->storage->bin();
 * $bin->put($file,'original');
 * $bin->makePermanent();
 * </pre>
 * 
 * Retrieve file URL and show it as image:
 * <pre>
 * $bin = app()->storage->bin($id);
 * echo CHtml::image($bin->getFileUrl('original'));
 * </pre>
 * 
 * Delete file from bin and an associated DB entry:
 * <pre>
 * app()->storage->bin($id)->delete('original',true);
 * </pre>
 * 
 * Remove bin entirely:
 * <pre>
 * app()->storage->bin($id)->purge();
 * </pre>
 *
 * @version $Id: UStorageBin.php 73 2010-11-09 09:26:00 GMT vasiliy.y $
 * @package system.storage
 * @since 1.0.0
 */
class UStorageBin extends CComponent {

    public $roles = array('administrator');
    private $_model = null;
    private $_storage = null;
    private static $_bins = array();

    /**
     * Constructor.
     * @param UStorage storage object to get settings from.
     */
    public function __construct($storage) {
        $this->_storage = $storage;
        $this->init();
    }

    /**
     * Initializes this bin.
     * This method is invoked at the end of the constructor.
     * You may override this method to provide customized initialization.
     */
    public function init() {
        
    }

    /**
     * Creates a model for this bin.
     * @return UActiveRecord active record model.
     * @see getModelClass
     */
    public function createModel() {
        $class = $this->getModelClass('bin');
        $model = new $class;
        $model->owner = app()->session->sessionId;
        $model->status = 0;
        $model->save();
        return $model;
    }

    /**
     * Creates bin: model, storage directories.
     */
    public function createBin() {
        $model = $this->createModel();
        $this->createDirectory($this->getDirectory($model->id));
        $this->createDirectory($this->getPath($model->id));
        $this->_model = $model;
        self::$_bins[$model->id] = $this;
    }

    /**
     * Creates a model and saves file info into it.
     * Note: data isn't added to DB (model isn't saved) here.
     * Model is only created.
     * @param string file label.
     * @return UActiveRecord storage file model.
     */
    public function createFile($name) {
        $class = $this->getModelClass('file');
        $model = new $class;
        $model->binId = $this->getId();
        $model->name = $name;
        $model->hash = UHelper::hash(null);
        return $model;
    }

    /**
     * Returns storage bin based on ID or creates new one if ID is not provided.
     * @param UStorage storage object.
     * @param mixed storage bin id: number, model or {@link UStorageBin} instance.
     * @return UStorageBin instance of this class.
     */
    public static function bin($storage, $id = null) {
        // id is empty - creating new bin
        if (!$id)
            return new UStorageBin($storage);
        // id is a bin
        elseif ($id instanceOf self)
            return $id;
        // id is a model
        elseif ($id instanceOf CActiveRecord) {
            $model = $id;
            $id = $model->id;
            $bin = new UStorageBin($storage);
            $bin->setModel($model);
            self::$_bins[$id] = $bin;
        }
        // id is a number
        elseif (!isset(self::$_bins[$id])) {
            $bin = new UStorageBin($storage);
            $model = CActiveRecord::model($bin->getModelClass('bin'))->findByPk($id);
            if (!$model)
                return null;
            else {
                $bin->setModel($model);
                self::$_bins[$id] = $bin;
            }
        }
        return self::$_bins[$id];
    }

    /**
     * Verifies if current user has edit/delete rights on this bin.
     * Administrator is always approved,
     * if this is temporary bin owner and current session id should match,
     * if this is permanent bin owner and current user id should match.
     * @return boolean access granted or denied.
     */
    public function checkAccess() {
        $m = $this->getModel();
        // checking roles access
        $access = true;
        foreach ($this->roles as $role)
            $access = $access || app()->user->checkAccess($role);

        // administrator is always approved
        // if this is temporary bin owner and current session id should match
        // if this is permanent bin owner and current user id should match
        return $access
                || ($m->status == 0 && $m->owner == app()->session->sessionId)
                || ($m->status == 1 && $m->owner == app()->user->id);
    }

    /**
     * Makes bin permanent.
     * @param integer user id to be set as bin owner.
     * @return boolean result.
     */
    public function makePermanent($owner = null) {
        if (!$this->checkAccess())
            return false;
        if ($this->getModel()->status != 1) {
            $this->getModel()->status = 1;
            $this->getModel()->owner = $owner ? $owner : app()->user->id;
            $this->getModel()->save();
        }
        return true;
    }

    /**
     * Puts a file into a bin.
     * @param mixed file source.
     * @param string file label.
     * @param  string class name of wrapper this file needs to be put in before saving.
     * @return boolean result.
     */
    public function put($source, $name, $class = 'UStorageFile') {
        // new bin - create model
        if ($this->getModel() === null)
            $this->createBin();

        // only user with edit/delete rights can put files into it
        if (!$this->checkAccess())
            return false;

        // if there's no file with such label - create a model for it
        if (!$file = $this->get($name))
            $file = $this->createFile($name);

        // load file into a wrapper
        $source = new $class($source);
        
        // save file and it's model
        if ($dist = $source->save(Yii::getPathOfAlias($this->getPath()) . DS . $file->hash)) {
            if (!$file->isNewRecord && $file->extension != $dist->extension)
                $this->delete($name, false);
            $file->extension = strtolower($dist->extension);
            $file->size = $dist->getSize(false);
            $file->save();

            $path = Yii::getPathOfAlias($this->getPath());
            $bfStorage = explode('storage', $path);

            //syns to s3
            //exec('s3cmd -P sync storage' . $bfStorage[1] . '/ s3://st.source.vn/storage' . $bfStorage[1].'/');
            return true;
        }
        return false;
    }

    /**
     * @return array all files from the bin.
     */
    public function getFiles() {
        // we can't rely on $this->getModel()->files because it doesn't provide fresh data
        // if we do changes during the same session
        return $this->getModel()->getRelated('files', true);
    }

    /**
     * Returns file model based on it's label.
     * @param string file label.
     * @return UActiveRecord file model.
     */
    public function get($name) {
        $files = $this->getFiles();
        foreach ($files as $f)
            if ($f->name == $name)
                return $f;
        return null;
    }

    /**
     * Returns file path based on it's label.
     * @param string file label.
     * @return string path to file.
     */
    public function getFilePath($name) {
        $file = $this->get($name);
        return $file ? Yii::getPathOfAlias($this->getPath()) . DS . $file->hash . '.' . $file->extension : null;
    }

    /**
     * Returns file URL based on it's label.
     * @param string file label.
     * @param string schema to use (e.g. http, https). If empty, the schema used for the current request will be used.
     * @return string file URL.
     */
    public function getFileUrl($name, $schema = '') {
        $file = $this->get($name);
        return $file ? UHelper::pathToUrl($this->getPath()) . '/' . $file->hash . '.' . $file->extension : null;
    }

    /**
     * Deletes file based on it's label and an associated DB entry (optional).
     * @param string file label.
     * @param boolean delete associated DB entry or not.
     * @return boolean result.
     */
    public function delete($name, $entry = true) {
        if (!$this->checkAccess())
            return false;
        if ($filePath = $this->getFilePath($name)) {
            if (file_exists($filePath))
                app()->file->set($filePath)->delete();
            return $entry && ($file = $this->get($name)) !== null ? $file->delete() : true;
        }
        return false;
    }

    /**
     * Removes entire bin from the system.
     * @return boolean result.
     */
    public function purge() {
        // check if user has enough rights to do it
        if (!$this->checkAccess())
            return false;
        // purge entire storage directory, DB entries for files and bin
        if (app()->file->set($this->getPath())->delete(true)) {
            CActiveRecord::model($this->getModelClass('file'))->deleteAll(
                    'binId = ?', array($this->getId())
            );
            $this->getModel()->delete();
            return true;
        }
        return false;
    }

    /**
     * Creates storage directory.
     * @param string path.
     */
    public function createDirectory($path) {
        $dir = app()->file->set($path);
        if (!$dir->exists)
            $dir->createDir(0755);
    }

    /**
     * Model setter.
     * @param UActiveRecord bin model.
     */
    public function setModel($model) {
        $this->_model = $model;
    }

    /**
     * Model getter.
     * @return UActiveRecord bin model.
     */
    public function getModel() {
        return $this->_model;
    }

    /**
     * Bin ID getter.
     * @return integer bin ID.
     */
    public function getId() {
        return $this->getModel()->id;
    }

    /**
     * Storage setter.
     * @param UStorage storage object.
     */
    public function setStorage($value) {
        $this->_storage = $value;
    }

    /**
     * Storage getter.
     * @return UStorage storage object.
     */
    public function getStorage() {
        return $this->_storage;
    }

    /**
     * Returns bin parent storage directory path.
     * @param integer bin ID.
     * @return string path to bin parent storage directory.
     */
    public function getDirectory($id = null) {
        $id = $id === null ? $this->getId() : $id;
        return $this->getStorage()->getPath() . '.' . self::idToDir($id);
    }

    /**
     * Returns bin storage directory path.
     * @param integer bin ID.
     * @return string path to bin storage directory.
     */
    public function getPath($id = null) {
        $id = $id === null ? $this->getId() : $id;
        return $this->getDirectory($id) . '.' . $id;
    }

    /**
     * Retrieves model class names from the storage associated with this bin.
     * @param string which class name needs to be returned.
     * @return string class name.
     */
    public function getModelClass($which) {
        switch ($which) {
            case 'bin':
                return $this->getStorage()->storageBinModelClass;
            case 'file':
                return $this->getStorage()->storageFileModelClass;
        }
    }

    /**
     * Determines a storage subdirectory based on bin ID.
     * That's a hack to deal with Linux 32,000 limit: no directory should contain more then 32,000 subdirectories.
     * @param integer bin ID.
     * @return string subdirectory.
     */
    public static function idToDir($id) {
        // dealing with linux 32,000 limit
        return (floor($id / 32000) + 1) * 32000;
    }

}