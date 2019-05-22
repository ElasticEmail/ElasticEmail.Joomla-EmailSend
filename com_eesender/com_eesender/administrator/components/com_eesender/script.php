<?php

/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Class Com_EesenderInstallerScript
 *
 * @since  0.9
 */
class Com_EesenderInstallerScript {
    private $type, $parent, $status;
    
    private $installationQueue = array(
        // Modules => { (folder) => { (module) => { (position), (published) } }* }*
        'modules' => array(),
        'plugins' => array('plg_system_eesender' => 0),
        // Key is the name without the lib_ prefix, value if the library should be autopublished
        'libraries' => array(
            'eesender' => 1
        )
    );

    /**
     * method to run after an install/update/discover method
     *
     * @param   string                      $type    - the type
     * @param   JInstallerAdapterComponent  $parent  - the parent object
     *
     * @return void
     */
    public function postflight($type, $parent) {
        $this->type = $type;
        $this->parent = $parent;
        // Let us install the modules
        $this->status = new stdClass;
       
        $this->installLibraries();
        
        $this->installPlugins();
        echo $this->displayInfoInstallation();
    }

    private function installPlugins() {
        $src = $this->parent->getParent()->getPath('source');
        
        $installer = new JInstaller;
        foreach ($this->installationQueue['plugins'] as $plugin => $published) {
            $parts = explode('_', $plugin);
            $pluginType = $parts[1];
            $pluginName = $parts[2];
            $path = $src . "/plugins/$pluginType/$pluginName";
            $result = $installer->install($path);
    
            $this->status->plugins[] = array('name' => $plugin, 'result' => $result);
        }
        
    }

    private function installLibraries() {
     
        $src = $this->parent->getParent()->getPath('source') . '/libraries/';
        
        $installer = new JInstaller;
        foreach ($this->installationQueue['libraries'] as $library => $published) {
            $path = $src . $library;
           
            $result = $installer->install($path);
            $this->status->libraries[] = array('name' => $library, 'result' => $result);
        }
    }

    /**
     * method to uninstall the component
     *
     * @param   JInstallerAdapterComponent  $parent  - the parent object
     *
     * @return void
     */
    public function uninstall($parent) {
       $this->parent = $parent;
        $this->status = new stdClass;
        $this->uninstallPlugins();
 
    }

    private function uninstallPlugins() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        foreach ($this->installationQueue['plugins'] as $plugin => $published) {
            $parts = explode('_', $plugin);
            $pluginType = $parts[1];
            $pluginName = $parts[2];
            
            $query->clear();
            $query->select('extension_id')->from($db->qn('#__extensions'))
                    ->where($db->qn('type') . '=' . $db->q('plugin'))
                    ->where($db->qn('element') . '=' . $db->q($pluginName))
                    ->where($db->qn('folder') . '=' . $db->q($pluginType));
            $db->setQuery($query);

            $id = $db->loadResult();
            
        $query2 = $db->getQuery(true);
            $query2->clear();
            $query2->select('extension_id')->from($db->qn('#__extensions'))
                    ->where($db->qn('type') .'='. $db->q('component'))
                    ->where($db->qn('element'). '='. $db->q('com_eesender'));
            $db->setQuery($query2);
            
            $component_id = $db->loadResult();
            
            if ($id) {
                $installer = new JInstaller;
                $result = $installer->uninstall('plugin', $id, 1);
                $this->status->plugins[] = array('name' => $plugin, 'result' => $result2);
                $result2= $installer->uninstall('component', $component_id, 1);
                $this->status->component[] =array('name' => 'com_eesender', 'result' => $result2 );
                
              
            }
        }
    }

    /**
     * Displays info about the status of the current install
     *
     * @return string
     */
    private function displayInfoInstallation() {
        $html[] = '<h2>' . JText::_('COM_EESENDER_INSTALLATION_SUCCESS') . '</h2>';
        $html[] = '<p><strong>' . JText::_('COM_EESENDER_LASTPROMOTION') . '</strong><br/><a href="https://elasticemail.com/support" target="_blank">https://elasticemail.com/support</a></p>';
        $html[] = '<table><tr><td>' . JText::_('COM_EESENDER_LIKEUS') . '</td><td><iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FElasticEmail&amp;width=200&amp;layout=button_count&amp;action=like&amp;show_faces=true&amp;share=false&amp;height=21&amp;appId=572789276128937" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;" allowTransparency="true"></iframe></td></tr></table>';
        $html[] = $this->renderInfoStatus($this->status->libraries, 'Library', JText::_('Installed'));
        $html[] = $this->renderInfoStatus($this->status->plugins, 'Plugins', JText::_('Installed'));
        $html[] = $this->renderInfoStatus($this->status->component, 'Library', JText::_('Installed'));
        return implode('', $html);
    }


    

}
