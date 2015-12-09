<?php

class wpbsui_SettingsManager
{
    public function initViewdata(&$viewData)
    {
        $od = new optionsDefaults();
        $changeSet = $od->compareToDefault($currentOptions, $defaultOptions);

        $viewData->changeSet = $changeSet;

        // is wp-cfm installed
        $viewData->cfmInstalled = $this->isCFMInstalled();
        $viewData->wpbBundle = $this->wpBootstrapBundle();
    }

    public function updateWPCFMSettings(&$viewData)
    {
        if ($this->isCFMInstalled()) {
            $cfmSettings = json_decode(get_option('wpcfm_settings', '{"bundles":[]}'));
            $pos = $this->findObjInArrayPosition('wpbootstrap', 'name', $cfmSettings->bundles);
            if ($pos === false) {
                $obj = new stdClass();
                $obj->name = 'wpbootstrap';
                $obj->label = 'wpbootstrap';
                $obj->config = array();
                $cfmSettings->bundles[] = $obj;
                $pos = 0;
            }

            $wpbBundle = $cfmSettings->bundles[$pos];
            $wpbBundle->config = array();
            foreach ($_POST as $name => $value) {
                if (substr($name, 0, 8) == 'managed_') {
                    $optionName = substr($name, 8);
                    if (!in_array($optionName, $wpbBundle->config)) {
                        $wpbBundle->config[] = $optionName;
                    }
                }
            }
            $cfmSettings->bundles[$pos] = $wpbBundle;
            update_option('wpcfm_settings', json_encode($cfmSettings));
        }
    }

    private function findObjInArrayPosition($string, $member, $array)
    {
        $i = 0;
        foreach ($array as $item) {
            if ($item->$member == $string) {
                return $i;
            }
        }

        return false;
    }

    private function isCFMInstalled()
    {
        $active = get_option('active_plugins', array());
        foreach ($active as $plugin) {
            if ($plugin == 'wp-cfm/wp-cfm.php') {
                return true;
            }
        }

        return false;
    }

    private function wpBootstrapBundle()
    {
        // a reasonable default
        $ret = new stdClass();
        $ret->name = 'wpbootstrap';
        $ret->label = 'wpbootstrap';
        $ret->config = array();
        if ($this->isCFMInstalled()) {
            $cfmSettings = json_decode(get_option('wpcfm_settings', '{}'));
            if (isset($cfmSettings->bundles)) {
                foreach ($cfmSettings->bundles as $bundle) {
                    if ($bundle->name = 'wpbootstrap') {
                        $ret = $bundle;
                        break;
                    }
                }
            }
        }

        return $ret;
    }
}
