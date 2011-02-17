<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version.
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * SwiftMailer plugin definition.
 */
class SystemPlugin_SwiftMailer_Plugin extends Zikula_Plugin implements Zikula_Plugin_Configurable, Zikula_Plugin_AlwaysOn
{
    /**
     * Get plugin meta data.
     *
     * @return array Meta data.
     */
    protected function getMeta()
    {
        return array('displayname' => $this->__('SwiftMailer'),
                'description' => $this->__('Provides SwiftMailer'),
                'version' => '4.0.6'
        );
    }

    /**
     * Initialise.
     *
     * Runs ar plugin init time.
     *
     * @return void
     */
    public function initialize()
    {
        // register namespace
        ZLoader::addAutoloader('Swift', dirname(__FILE__) . '/lib/vendor/SwiftMailer/classes');

        // initialize Swift
        require_once realpath($this->baseDir . '/lib/vendor/SwiftMailer/swift_init.php');

        // load configuration (todo: move this to persistence).
        include dirname(__FILE__) . '/configuration/config.php';

        $this->serviceManager['swiftmailer.preferences.sendmethod'] = $config['sendmethod'];

        $preferences = Swift_Preferences::getInstance();
        $this->serviceManager['swiftmailer.preferences.charset'] = $config['charset'];
        $this->serviceManager['swiftmailer.preferences.cachetype'] = $config['cachetype'];
        $this->serviceManager['swiftmailer.preferences.temdir'] = $config['temdir'];
        $preferences->setCharset($config['charset']);
        $preferences->setCacheType($config['cachetype']);
        $preferences->setTempDir($config['temdir']);

        // determine the correct transport
        $type = $config['transport']['type'];
        $args = $config['transport'][$type];
        switch ($type) {
            case 'mail':
                $this->serviceManager['swiftmailer.transport.mail.extraparams'] = $args['extraparams'];
                $definition = new Zikula_ServiceManager_Definition('Swift_MailTransport', array(new Zikula_ServiceManager_Argument('swiftmailer.transport.mail.extraparams')));
                break;

            case 'smtp':
                $this->serviceManager['swiftmailer.transport.smtp.host'] = $args['host'];
                $this->serviceManager['swiftmailer.transport.smtp.port'] = $args['port'];
                $definition = new Zikula_ServiceManager_Definition('Swift_SmtpTransport', array(
                                new Zikula_ServiceManager_Argument('swiftmailer.transport.smtp.host'),
                                new Zikula_ServiceManager_Argument('swiftmailer.transport.smtp.port')));

                if ($args['username'] && $args['password']) {
                    $this->serviceManager['swiftmailer.transport.smtp.username'] = $args['username'];
                    $this->serviceManager['swiftmailer.transport.smtp.password'] = $args['password'];
                    $definition->addMethod('setUserName', new Zikula_ServiceManager_Argument('swiftmailer.transport.smtp.username'));
                    $definition->addMethod('setPassword', new Zikula_ServiceManager_Argument('swiftmailer.transport.smtp.password'));
                }
                if (isset($args['encryption'])) {
                    $this->serviceManager['swiftmailer.transport.smtp.encryption'] = $args['encryption'];
                    $definition->addMethod('setEncryption', new Zikula_ServiceManager_Argument('swiftmailer.transport.smtp.encryption'));
                }
                break;

            case 'sendmail':
                $this->serviceManager['swiftmailer.transport.mail.command'] = $args['command'];
                $definition = new Zikula_ServiceManager_Definition('Swift_SendmailTransport', array(new Zikula_ServiceManager_Argument('swiftmailer.transport.mail.command')));
                break;
            
            default:
                // error
                throw new InvalidArgumentException('Invalid transport type, must be mail, smtp or sendmail');
                break;
        }

        // register transport
        $this->serviceManager->registerService(new Zikula_ServiceManager_Service('swiftmailer.transport', $definition));

        // define and register mailer using transport service
        $definition = new Zikula_ServiceManager_Definition('Swift_Mailer', array(new Zikula_ServiceManager_Service('swiftmailer.transport')));
        $this->serviceManager->registerService(new Zikula_ServiceManager_Service('mailer', $definition));

        // register simple mailer service
        $definition = new Zikula_ServiceManager_Definition('SystemPlugins_SwiftMailer_Mailer', array(new Zikula_ServiceManager_Service('zikula.servicemanager')));
        $this->serviceManager->registerService(new Zikula_ServiceManager_Service('mailer.simple', $definition));
    }

    /**
     * Return controller instance.
     *
     * @return Zikula_Plugin_Controller
     */
    public function getConfigurationController()
    {
        return new SystemPlugin_SwiftMailer_Controller($this->serviceManager, array('plugin' => $this));
    }
}