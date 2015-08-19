<?php

/**
 * @author AzT3K
 */
class AbcSiteTreeExtension extends DataExtension {

    public function HashedPath($file, $extension = null) {
        $absPath = Director::getAbsFile(trim($file  . ($extension ? '.' . $extension : ''), '/'));
        return $file . '?h=' . sha1_file($absPath);
    }

    public function TimestampedPath($file, $extension = null) {
        $absPath = Director::getAbsFile(trim($file . ($extension ? '.' . $extension : ''), '/'));
        return $file . '?m=' . filemtime($absPath);
    }

}
