<?php
/**
 * ImportConfigurationForm class.
 * ImportConfigurationForm is the parent class for all configuration imports
 */
abstract class ImportConfigurationForm extends CFormModel
{
    protected $file;
    protected $filePath;
    protected $fileExtension;

    protected $allowedImportFileTypes = array(
        'csv',
    );

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    public function uploadAndValidateConfigFile(){
        $this->filePath = null;
        $this->fileExtension = null;

        $uploadedFile = CUploadedFile::getInstance($this, 'file');
        if (!$uploadedFile instanceof CUploadedFile) {
            $this->addError('file','File cannot be blank.');
            return false;
        }

        $info = pathinfo($uploadedFile->getName());
        if (!in_array(strtolower($info["extension"]), $this->allowedImportFileTypes)) {
            $this->addError('file','The file "'.$uploadedFile->getName().'" cannot be uploaded. Only files with these extensions are allowed: '.implode(', ',$this->allowedImportFileTypes).'.');
            return false;
        }

        $filePath = File::getFullPath(substr($uploadedFile->getName(), 0, -4) . ((string) time()) . '.csv', Yii::app()->params['configsFolder']);
        if (!$uploadedFile->saveAs($filePath)) {
            $this->addError('file', 'An error occurred saving file.');
            return false;
        }

        $this->filePath = $filePath;
        $this->fileExtension = $info["extension"];

        return true;
    }

}