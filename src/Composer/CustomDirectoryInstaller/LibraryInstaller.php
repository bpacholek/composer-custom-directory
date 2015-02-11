<?php

namespace Composer\CustomDirectoryInstaller;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller as BaseLibraryInstaller;
use Composer\Repository\InstalledRepositoryInterface;

class LibraryInstaller extends BaseLibraryInstaller
{
    public function getInstallPath(PackageInterface $package)
    {
        $names = $package->getNames();

        if ($this->composer->getPackage())
        {
            $extra = $this->composer->getPackage()->getExtra();
            if(!empty($extra['installer-paths']))
            {
                foreach($extra['installer-paths'] as $path => $packageNames)
                {
                    foreach($packageNames as $packageName)
                    {
                        if (in_array(strtolower($packageName), $names)) {
                            if($matched = preg_match_all("/\{(.*?)\}/is",$path,$matches,PREG_PATTERN_ORDER)) {
                                $packageParts = explode('/',$packageName);
                                foreach($matches[1] as $pattern)
                                {
                                    $patternParts = explode('|', $pattern);
                                    $flags = array();
                                    if(count($patternParts) > 1) {
                                        $flags = str_split($patternParts[1]);
                                    }
                                    switch($patternParts[0])
                                    {
                                        case '$package':
                                            $value = $packageName;
                                            break;
                                        case '$name':
                                            if(count($packageParts) > 1) {
                                                $value = $packageParts[1];
                                            } else {
                                                $value = 'undefined';
                                            }
                                            break;
                                        case '$vendor':
                                        if(count($packageParts) > 1) {
                                            $value = $packageParts[0];
                                        } else {
                                            $value = 'undefined';
                                        }
                                            break;
                                    }
                                    foreach($flags as $flag)
                                    {
                                        switch($flag)
                                        {
                                            case 'F':
                                                $value = ucfirst($value);
                                                break;
                                            case 'P':
                                                $value = preg_replace_callback('/[_\-]([a-zA-Z])/',
                                                function ($matches) {
                                                return strtoupper($matches[1]);
                                                },
                                                $value);
                                            break;
                                        }
                                    }

                                    $path = str_replace('{' . $pattern . '}', $value, $path);
                                }
                            }
                            return $path;
                        }
                    }
                }
            }
        }


    /*
     * In case, the user didn't provide a custom path
     * use the default one, by calling the parent::getInstallPath function
     */
        return parent::getInstallPath($package);
    }

    protected function installCode(PackageInterface $package)
    {
        parent::installCode($package);

        $name = $package->getNames();

        $downloadPath = $this->getInstallPath($package);
        $extra = $this->composer->getPackage()->getExtra();
        if(isset($extra['redistribute']) && isset($extra['redistribute'][$name])) {
            $rules = isset($extra['redistribute'][$name];
            foreach($rules as $rule) {
                $basePath = $downloadPath . "/" . $rule['path'];
                if(file_exists($basePath)) {
                    $destinationPath = $downloadPath . "/" . $rule['destination'];
                    rename($basePath, $desinationPath);
                }
            }
        }
    }

}
