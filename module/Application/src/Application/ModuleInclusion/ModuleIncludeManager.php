<?php
/*
 * This file is part of the codeliner/ginger-wfms package.
 * (c) Alexander Miertsch <kontakt@codeliner.ws>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\ModuleInclusion;

use Application\Exception;
use Zend\Config\Writer\PhpArray;
/**
 *  ModuleIncludeManager
 * 
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class ModuleIncludeManager
{
    protected $configDir;
    
    /**
     * Construct
     * 
     * @param string $configDir Dir where configuration files can be found
     */
    public function __construct($configDir = null)
    {
        if (is_null($configDir)) {
            $configDir = 'config';
        }
        
        $this->configDir = $configDir;
    }
    
    /**
     * Get list of all activated modules needed to run the backend (core + backend)
     * 
     * @return array List of backend modules
     */
    public function getBackendModulesList()
    {
        $coreModules = $this->loadCoreModulesList();
        
        $backendModules = $this->loadBackendModulesList();
        
        return array_merge($coreModules, $backendModules);
    }
    
    /**
     * Get list of all activated modules (core, backend, frontend)
     * 
     * @return array List of all modules
     */
    public function getAllModulesList()
    {
        $backendModules = $this->getBackendModulesList();
        
        $frontendModules = $this->loadFrontendModulesList();
        
        return array_merge($backendModules, $frontendModules);
    }
    
    /**
     * Activate new backend module
     * 
     * @param string $namespace   Module namespace
     * @param string $packageName Name of the package incl. vendor name
     * 
     * @return void
     */
    public function addBackendModule($namespace, $packageName)
    {
        $backendModules = $this->loadBackendModulesList();
        
        $backendModules[$namespace] = $packageName;
        
        $this->writeFile($backendModules, 'backend.modules.php');
    }
    
    /**
     * Activate new frontend module
     * 
     * @param string $namespace   Module namespace
     * @param string $packageName Name of the package incl. vendor name
     * 
     * @return void
     */
    public function addFrontendModule($namespace, $packageName)
    {
        $frontendModules = $this->loadFrontendModulesList();
        
        $frontendModules[$namespace] = $packageName;
        
        $this->writeFile($frontendModules, 'frontend.modules.php');
    }
    
    /**
     * Deactivate backend module
     * 
     * @param string $namespace Module namespace
     * 
     * @return void
     */
    public function removeBackendModule($namespace)
    {
        $backendModules = $this->loadBackendModulesList();
        
        unset($backendModules[$namespace]);
        
        $this->writeFile($backendModules, 'backend.modules.php');
    }
    
    /**
     * Deactivate frontend module
     * 
     * @param string $namespace Module namespace
     * 
     * @return void
     */
    public function removeFrontendModule($namespace)
    {
        $frontendModules = $this->loadFrontendModulesList();
        
        unset($frontendModules[$namespace]);
        
        $this->writeFile($frontendModules, 'frontend.modules.php');
    }
    
    /**
     * Load core modules list
     * 
     * @return array List of core modules 
     */
    protected function loadCoreModulesList()
    {
        return $this->readFile('core.modules.php');
    }

    /**
     * Load list of backend modules
     * 
     * @return array List of backend modules
     */
    protected function loadBackendModulesList()
    {
        return $this->readFile('backend.modules.php');
    }
    
    /**
     * Load list of frontend modules
     * 
     * @return array List of frontend modules
     */
    protected function loadFrontendModulesList()
    {
        return $this->readFile('frontend.modules.php');
    }

    /**
     * Read module list from given file
     * 
     * If file does not exist method checks if a .dist version of the file
     * can be found instead. In this case the .dist file is copied and renamed
     * to the requested filename.
     * 
     * @param string $filename
     * @return array List of modules
     * 
     * @throws Exception\InvalidArgumentException If file can not be found
     * @throws Exception\RuntimeException If copy of .dist file fails
     */
    protected function readFile($filename)
    {
        $file = $this->configDir . '/' . $filename;
        
        if (!file_exists($file)) {
            $distFile = $file . '.dist';
            
            if (!file_exists($distFile)) {
                throw new Exception\InvalidArgumentException(
                    sprintf(                    
                        'Provided filename -%s- does not match with an existing module list file',
                        $filename
                    )
                );
            }
            
            $success = @copy($distFile, $file);
            
            if (!$success) {
                throw new Exception\RuntimeException(
                    sprintf(
                        'Can not rename -%s-. Permission denied.',
                        $distFile                        
                    )
                );
            }
        }
        
        $moduleList = include $file;
        
        return $moduleList;
    }
    
    /**
     * Write given module list to file
     * 
     * @param array $modules
     * @param string $filename
     * 
     * @return void
     */
    protected function writeFile(array $modules, $filename)
    {
        $file = $this->configDir . '/' . $filename;
        
        $configWriter = new PhpArray();
        
        $configWriter->toFile($file, $modules);
    }
}
