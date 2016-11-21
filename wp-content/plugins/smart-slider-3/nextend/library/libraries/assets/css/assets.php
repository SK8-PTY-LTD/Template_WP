<?php

class N2AssetsCss extends N2AssetsAbstract
{

    public function __construct() {
        $this->cache = new N2AssetsCacheCSS();
    }

    public function getOutput() {

        N2GoogleFonts::build();
        N2LESS::build();

        $output = "";

        foreach ($this->urls AS $url) {
            $output .= N2Html::style($url, true, array(
                    'media' => 'screen, print'
                )) . "\n";
        }

        foreach ($this->getFiles() AS $file) {
            $output .= N2Html::style(N2Uri::pathToUri($file) . '?' . filemtime($file), true, array(
                    'media' => 'screen, print'
                )) . "\n";
        }

        $inline = implode("\n", $this->inline);
        if (!empty($inline)) {
            $output .= N2Html::style($inline);
        }

        return $output;
    }

    public function get() {
        N2GoogleFonts::build();
        N2LESS::build();

        return array(
            'url'    => $this->urls,
            'files'  => $this->getFiles(),
            'inline' => implode("\n", $this->inline)
        );
    }

    protected function getFilesRaw() {

        N2GoogleFonts::build();
        N2LESS::build();

        return parent::getFilesRaw();
    }

    public function getAjaxOutput() {

        //$output = $this->getFilesRaw() . "\n";

        $output = implode("\n", $this->inline);

        return $output;
    }
} 