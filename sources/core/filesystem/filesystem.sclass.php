<?php

/*
 * Copyright (C) 2016 polux
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace MyGED\Core\FileSystem;

use MyGED\Exceptions as AppExceptions;

/**
 * Filesystem Management - ToolsBox
 *
 * @author polux
 */
class FileSystem
{
    /**
     * deleteDir
     *
     * Delete dir and all of these children
     *
     * @param string $dirPath Path to delete
     * @throws InvalidArgumentException
     */
    public static function deleteDir($dirPath)
    {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    /**
     * Copy an element to another
     *
     * @param string    $pStrSource     Source file to copy.
     * @param string    $pStrTarget     Target file.
     * @throws AppExceptions\GenericException
     * @throws \Exception
     */
    public static function filecopy($pStrSource, $pStrTarget)
    {
        try {
            $lBoolResult = copy($pStrSource, $pStrTarget);
            //chmod($pStrTarget, 0777);

            if (!$lBoolResult) {
                throw new \Exception(sprintf("Error during file copy (from '%s' to '%s').", $pStrSource, $pStrTarget));
            }
        } catch (\Exception $e) {
            $lArrOptions = array('msg'=> $e->getMessage());
            throw new AppExceptions\GenericException('FILE_COPY_ERR', $lArrOptions);
        }
    }

    /**
     * Returns extension from filename
     *
     * @link http://php.net/manual/fr/function.pathinfo.php
     * @param string $pStrFilepath Filepath
     * @return string File extension
     */
    public static function getExtensionFromPath($pStrFilepath)
    {
        return pathinfo($pStrFilepath, PATHINFO_EXTENSION);
    }//end getExtensionFromPath()

    /**
     * Return a temporay filename
     *
     * @param string $pStrFilepath Filepath
     * @return string File extension
     */
    public static function getTempFilename($pStrTargetDir='/tmp', $pStrFilePrefix='tmp')
    {
        return tempnam($pStrTargetDir, $pStrFilePrefix);
    }//end getTempFilename()

    /**
     * Returns an array containing all files filtered by extension
     *
     * @param string  $pStrTargetDir            Directory where files will be scanned.
     * @param boolean $pBoolRecursive           Recursive mode (scan all subfolders).
     * @param string  $pStrFileExtensionFilter  File extensions to search.
     *
     * @return array(files) Scanned Files filtered by extension.
     */
    public static function getAllFilenamesOfDirectory($pStrTargetDir, $pBoolRecursive=false, $pStrFileExtensionFilter='*')
    {
        $lArrtmp = scandir($pStrTargetDir);
        $lArrFiles = array_filter(scandir($pStrTargetDir), function ($file) {
            return (strcmp('.', $file)!=0 && strcmp('..', $file)!=0);
        });

        $lArrFilesResult = array();
        foreach ($lArrFiles as $lStrFilename) {
            if (is_file($pStrTargetDir.'/'.$lStrFilename)) {
                $lArrFilesResult[] = $pStrTargetDir.'/'.$lStrFilename;
            }
        }

        // Recursive mode and subfolders exists!
        if ($pBoolRecursive && static::getAllSubFoldersOfDirectory($pStrTargetDir) > 0) {
            $lArrSubfolders = static::getAllSubFoldersOfDirectory($pStrTargetDir);

            foreach ($lArrSubfolders as $lStrDirectoryPath) {
                $lArrFilesResult = array_merge($lArrFilesResult, static::getAllFilenamesOfDirectory($lStrDirectoryPath, true, $pStrFileExtensionFilter));
            }
        }

        return $lArrFilesResult;
    }//end getAllFilenamesOfDirectory()

    /**
     * Returns an array containing all subfolders of Path
     *
     * @param string  $pStrTargetDir            Directory where files will be scanned.
     *
     * @return array(directory) Scanned Directory into TargetPath (one level).
     */
    public static function getAllSubFoldersOfDirectory($pStrTargetDir, $pBoolRecursive=false, $pStrFileExtensionFilter='*')
    {
        $lArrDirectories = array_filter(scandir($pStrTargetDir), function ($file) {
            return (strcmp('.', $file)!=0 && strcmp('..', $file)!=0);
        });
        $lArrDirectoriesResult = array();
        foreach ($lArrDirectories as $lStrDir) {
            if (is_dir($pStrTargetDir.'/'.$lStrDir)) {
                $lArrDirectoriesResult[] = $pStrTargetDir.'/'.$lStrDir;
            }
        }
        return $lArrDirectoriesResult;
    }//end getAllSubFoldersOfDirectory()
}
