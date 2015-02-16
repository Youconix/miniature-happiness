<?php
namespace core\services;

/**
 * File upload service
 *
 * This file is part of Scripthulp framework
 *
 * @copyright 2014,2015,2016 Rachelle Scheijen
 * @author Rachelle Scheijen
 * @version 1.0
 * @since 1.0
 *        @date 12/01/2006
 *        @changed 30/03/2014
 *       
 *        Scripthulp framework is free software: you can redistribute it and/or modify
 *        it under the terms of the GNU Lesser General Public License as published by
 *        the Free Software Foundation, either version 3 of the License, or
 *        (at your option) any later version.
 *       
 *        Scripthulp framework is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *        GNU General Public License for more details.
 *       
 *        You should have received a copy of the GNU Lesser General Public License
 *        along with Scripthulp framework. If not, see <http://www.gnu.org/licenses/>.
 */
class Upload extends Service
{

    protected $model_Image;

    /**
     * PHP 5 constructor
     *
     * @param \core\models\Image $model_Image
     *            The image model
     */
    public function __construct(\core\models\Image $model_Image)
    {
        $this->model_Image = $model_Image;
    }

    /**
     * Checks if the file is correct uploaded
     *
     * @param String $s_name
     *            form field name
     * @return boolean if the file is uploaded,otherwise false
     */
    public function isUploaded($s_name)
    {
        return (array_key_exists($s_name, $_FILES) && $_FILES[$s_name]['error'] == 0);
    }

    /**
     * Checks if the file is valid
     *
     * @param String $s_name
     *            field name
     * @param array $a_extensions
     *            extensions with the mimetype as value
     * @param int $i_maxSize
     *            filesize in bytes, optional
     * @return boolean if the file is valid, otherwise false
     */
    public function isValid($s_name, $a_extensions, $i_maxSize = -1)
    {
        $s_mimetype = $this->model_Image->getMimeType($_FILES[$s_name]['tmp_name']);
        
        $a_data = explode('/', $s_mimetype);
        $a_fileExtensions = explode('.', $_FILES[$s_name]['name']);
        $s_extension = strtolower(end($a_fileExtensions));
        
        if (! array_key_exists($s_extension, $a_extensions) || ($a_extensions[$s_extension] != $s_mimetype && (is_array($a_extensions[$s_extension]) && ! in_array($s_mimetype, $a_extensions[$s_extension])))) {
            unlink($_FILES[$s_name]['tmp_name']);
            return false;
        }
        
        if ($i_maxSize != - 1 && $_FILES[$s_name]['size'] > $i_maxSize) {
            unlink($_FILES[$s_name]['tmp_name']);
            return false;
        }
        
        return true;
    }

    /**
     * Moves the uploaded file to the target directory
     * Does NOT overwrite files with the same name.
     *
     * @param String $s_name
     *            field name
     * @param String $s_targetDir
     *            directory
     * @param String $s_targetName
     *            name to use. Do not provide an extension, optional. Default the filename
     * @return String choosen filename without directory
     */
    public function moveFile($s_name, $s_targetDir, $s_targetName = '')
    {
        if (empty($s_targetName)) {
            $s_targetName = $_FILES[$s_name]['name'];
        } else {
            $a_fileExtensions = explode('.', $_FILES[$s_name]['name']);
            $s_extension = '.' . strtolower(end($a_fileExtensions));
            
            $s_targetName .= $s_extension;
        }
        
        if (file_exists($s_targetDir . '/' . $s_targetName)) {
            $a_fileExtensions = explode('.', $_FILES[$s_name]['name']);
            $s_extension = '.' . strtolower(end($a_fileExtensions));
            
            $i = 1;
            $s_testname = $s_targetName;
            while (file_exists($s_targetDir . '/' . str_replace($s_extension, '__' . $i . $s_extension, $s_testname))) {
                $i ++;
            }
            
            $s_targetName = str_replace($s_extension, '__' . $i . $s_extension, $s_testname);
        }
        
        move_uploaded_file($_FILES[$s_name]['tmp_name'], $s_targetDir . '/' . $s_targetName);
        
        if (! file_exists($s_targetDir . '/' . $s_targetName)) {
            return '';
        }
        
        return $s_targetName;
    }

    /**
     * Resizes the given picture
     * Works only with jpg, gif and png
     *
     * @param String $s_file
     *            file url
     * @param int $i_maxWidth
     *            width
     * @param int $i_maxHeight
     *            max height
     * @throws Exception the file is not a jpg,gif or png image
     */
    public function resizeImage($s_file, $i_maxWidth, $i_maxHeight)
    {
        $this->model_Image->resizeImage($s_file, $i_maxWidth, $i_maxHeight);
    }

    /**
     * Generates a trumbnail with max width 50 pixels.
     * Works only with jpg, gif and png
     *
     * @param String $s_file
     *            file url
     */
    public function makeTrumb($s_file)
    {
        $this->model_Image->makeTrumb($s_file);
    }
}
?>