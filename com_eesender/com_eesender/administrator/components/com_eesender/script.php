<?php

/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2014 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Class Com_EesenderInstallerScript
 *
 * @since  1.0
 */
class Com_EesenderInstallerScript {
    private $type, $parent, $status, $html;
   
   
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
        $html=array();
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
    
            $this->status->plugins[] = array('name' => 'Elastic Email Mailer', 'result' => $result);
        }
        
    }

    private function installLibraries() {
       
        $src = $this->parent->getParent()->getPath('source') . '/libraries/';
        
        $installer = new JInstaller;
        foreach ($this->installationQueue['libraries'] as $library => $published) {
            $path = $src . $library;
           
            $result = $installer->install($path);
            $this->status->libraries[] = array('name' => 'Elastic Email Libraries', 'result' => $result);
            $this->status->components[] = array('name' => "Elastic Email Sender", 'result' => $result);
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
      
        $this->status = new stdClass;
        $this->uninstallPlugins();
        $this->uninstallLibraries();
        $this->changeStatus();
        echo $this->displayInfoUninstallation();
    }
    public function uninstallLibraries(){
     
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->clear();
    $query->select('extension_id')->from($db->qn('#__extensions'))
            ->where($db->qn('type') . '=' . $db->q('library'))
            ->where($db->qn('element') . '=' . $db->q('eesender'));
            
    $db->setQuery($query);

    $id = $db->loadResult();
    if ($id) {
        $installer = new JInstaller;
        $result = $installer->uninstall('library', $id, 1);
        $this->status->libraries[] =array('name' => 'Elastic Email Libraries', 'result' => $result );
        
    }
        }
    private function uninstallPlugins() {
      
        $this->status->libraries = array();
       $this->status->plugins = array();
        $this->status->components = array();
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
       
            if ($id) {
                $installer = new JInstaller;
                $result = $installer->uninstall('plugin', $id, 1);
                
                $this->status->components[] =array('name' => 'Elastic Email Sender', 'result' => $result );
                $this->status->plugins[] = array('name' => 'Elastic Email Mailer', 'result' => $result);
                
            }
        }
    }

    /**
     * Displays info about the status of the current install
     *
     * @return string
     */
    private function displayInfoInstallation() {
        
        $html[] = '<h2>' . JText::_('Installation was successful') . '</h2>';
        $html[] = '<p><strong>' . JText::_('Find out how to Setup the Account and Learn how to use your marketing interface.') . '</strong><br/><a href="https://help.elasticemail.com" target="_blank">https://help.elasticemail.com</a></p>';
        $html[] = '<table><tr><td>' . JText::_('Like us on Facebook: ') . '</td><td><iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FElasticEmail&amp;width=200&amp;layout=button_count&amp;action=like&amp;show_faces=true&amp;share=false&amp;height=21&amp;appId=572789276128937" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;" allowTransparency="true"></iframe></td></tr></table>';
        $html[] = '<table class="table">'; 
       
        $html[] = $this->renderInfoStatus($this->status->plugins, 'Plugins', JText::_('Installed'));
        $html[] = $this->renderInfoStatus($this->status->libraries, 'Library', JText::_('Installed'));
        $html[] = $this->renderInfoStatus($this->status->components, 'Component', JText::_('Installed'));
        $html[] = '</table>';
        return implode('', $html);
    }

/**
 * Change status in analitics list
 * @return void 
 */
    private function changeStatus () {
    $email =JComponentHelper::getParams('com_eesender')->get('username');
    $url = 'https://api.elasticemail.com/v2/contact/add';
    $post = array('email' => $email,
                  'publicAccountID' => 'd0bcb758-a55c-44bc-927c-34f48d5db864',
                  'publicListID' => '8e85d689-69ff-4486-9374-f50d611cb4b6',
                  'firstName' => 'D',
                  'lastName' => ' ',
                );
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_SSL_VERIFYPEER => false
    ));
    $result=curl_exec($ch);
    }

    /**
     * Display uninstall info
     *
     * @return string
     */
    public function displayInfoUninstallation() {
        ?>
        
        <h2> <?php JText::_('COM_EEMAIL_LIKEUS') ?> </h2>
                <p><strong> <?php JText::_('COM_EEMAIL_LASTPROMOTION'); ?></strong>:</p>
        <table><tr><td><?php JText::_('COM_EEMAIL_LIKEUS'); ?></td><td><iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FElasticEmail&amp;width=200&amp;layout=button_count&amp;action=like&amp;show_faces=true&amp;share=false&amp;height=21&amp;appId=572789276128937" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;" allowTransparency="true"></iframe></td></tr></table> 
        <table class="table">
        <?php echo $this->renderInfoStatus($this->status->libraries, 'Library',JText::_('COM_EEMAIL_UNINSTALLED')); ?>
        <?php echo $this->renderInfoStatus($this->status->plugins, 'Plugins', JText::_('COM_EEMAIL_UNINSTALLED')); ?>
        <?php echo $this->renderInfoStatus($this->status->components, 'Component', JText::_('COM_EEMAIL_UNINSTALLED')); ?>
        </table>
        <?php
    }

    private function renderInfoStatus($status, $type, $mode) {
        $rows = 0;
        $html = array();
        if (count($status)) {
            $html[] = '<tr>';
            $html[] = '<th>' . $type . '</th>';
            $html[] = '<th>Status</th>';
            $html[] = '</tr>';
            foreach ($status as $item) {
                $html[] = '<tr class="row' . ( ++$rows % 2) . '">';
                $html[] = '<td class="key">' . $item['name'] . '</td>';
                $html[] = '<td>';
                $html[] = '<span style="color: ' . (($item['result']) ? 'green' : 'red') . '; font-weight: bold;">';
                $html[] = ($item['result']) ? $mode : 'Not ' . $mode;
                $html[] = '</span>';
                if (isset($item['message'])) {
                    $html[] = ' (' . $library['message'] . ')';
                }
                $html[] = '</td>';
                $html[] = '</tr>';
            }
        }
       
        return implode('', $html);
    }

}
