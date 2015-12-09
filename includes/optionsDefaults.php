<?php


class optionsDefaults
{
    const HINT_STANDARD = 0;
    const HINT_RECOMMENDED = 1;
    const HINT_PROBABLY = 2;
    const HINT_INTERNALLY = 3;
    const HINT_EXTERNALLY = 4;

    const STATE_UNCHANGED = 0;
    const STATE_MODIFIED = 1;
    const STATE_NEW = 2;

    private $optionMeta = array(
        'blogname' => array('type' => self::HINT_RECOMMENDED),
        'blogdescription' => array('type' => self::HINT_RECOMMENDED),
        'admin_email' => array('type' => self::HINT_PROBABLY),
        'wpcfm_settings' => array('type' => self::HINT_RECOMMENDED),

        'recently_activated' => array('type' => self::HINT_INTERNALLY),
        'active_plugins' => array('type' => self::HINT_INTERNALLY),
        'cron' => array('type' => self::HINT_INTERNALLY),
        'can_compress_scripts' => array('type' => self::HINT_INTERNALLY),
        'rewrite_rules' => array('type' => self::HINT_INTERNALLY),
        'db_version' => array('type' => self::HINT_INTERNALLY),
        'initial_db_version' => array('type' => self::HINT_INTERNALLY),
        'finished_splitting_shared_terms' => array('type' => self::HINT_INTERNALLY),

        'home' => array('type' => self::HINT_EXTERNALLY),
        'siteurl' => array('type' => self::HINT_EXTERNALLY),
        'sidebars_widgets' => array('type' => self::HINT_EXTERNALLY),
        'widget_*' => array('type' => self::HINT_EXTERNALLY),

    );

    private $standardOption = array('type' => self::HINT_STANDARD);

    public function getOptionMeta()
    {
        return $this->optionMeta;
    }

    public function getDefaults($version)
    {
        global $wp_version;

        $file = __DIR__.'/versions/'.$wp_version;
        if (file_exists($file)) {
            $ret = unserialize(file_get_contents($file));

            return $ret;
        }

        return false;
    }

    public function compareToDefault()
    {
        $ret = array();

        $currentOptions = wp_load_alloptions();
        $defaultOptions = $this->getDefaults($wp_version);

        foreach ($currentOptions as $name => $option) {
            if (substr($name, 0, 1) == '_') {
                continue;
            }

            if (!isset($defaultOptions[$name])) {
                $obj = new stdClass();
                $obj->name = $name;
                $obj->state = self::STATE_NEW;
                $obj->default = null;
                $obj->current = $option;
                $ret[$name] = $obj;
            } else {
                // this option existied at the start.
                if ($option == $defaultOptions[$name]) {
                    // unchanged
                    $obj = new stdClass();
                    $obj->name = $name;
                    $obj->state = self::STATE_UNCHANGED;
                    $obj->default = $option;
                    $obj->current = $option;
                    $ret[$name] = $obj;
                } else {
                    $obj = new stdClass();
                    $obj->name = $name;
                    $obj->state = self::STATE_MODIFIED;
                    $obj->default = $defaultOptions[$name];
                    $obj->current = $option;
                    $ret[$name] = $obj;
                }
            }
        }

        foreach ($ret as $name => &$obj) {
            $obj->meta = $this->standardOption;
            foreach ($this->optionMeta as $metaName => $meta) {
                $match = preg_match('/'.$metaName.'/', $name);
                if ($match) {
                    $obj->meta = $meta;
                }
            }
        }

        return $ret;
    }
}
