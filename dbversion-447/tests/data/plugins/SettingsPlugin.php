<?php

class SettingsPlugin extends PluginBase
{
    protected static $description = 'Dummy plugin for testing SettingsPlugin event';
    protected static $name = 'SettingsPlugin';
    protected $storage = 'DbStorage';
    protected $encryptedSettings = [];

    public function init()
    {
    }

    public function setSetting($name, $value)
    {
        return $this->set($name, $value);
    }

    public function getSetting($name)
    {
        return $this->get($name);
    }

    public function setEncriptedSettings($encryptedSettings)
    {
        $this->encryptedSettings = $encryptedSettings;
    }
}