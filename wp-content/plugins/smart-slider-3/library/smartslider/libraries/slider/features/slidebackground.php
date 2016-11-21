<?php

class N2SmartSliderFeatureSlideBackground {

    private $slider;

    public function __construct($slider) {

        $this->slider = $slider;
    }

    /**
     * @param $slide N2SmartSliderSlide
     *
     * @return string
     */
    public function make($slide) {

        $dynamicHeight = intval($this->slider->params->get('dynamic-height', 0));

        $backgroundImageOpacity = min(100, max(0, $slide->parameters->get('backgroundImageOpacity', 100))) / 100;

        if ($slide->hasGenerator()) {

            $rawBackgroundImage = $slide->parameters->get('backgroundImage', '');
            $backgroundImage    = $slide->fill($rawBackgroundImage);

            $imageData = N2ImageManager::getImageData($rawBackgroundImage);

            $imageData['desktop-retina']['image'] = $slide->fill($imageData['desktop-retina']['image']);
            $imageData['tablet']['image']         = $slide->fill($imageData['tablet']['image']);
            $imageData['tablet-retina']['image']  = $slide->fill($imageData['tablet-retina']['image']);
            $imageData['mobile']['image']         = $slide->fill($imageData['mobile']['image']);
            $imageData['mobile-retina']['image']  = $slide->fill($imageData['mobile-retina']['image']);
        } else {
            $backgroundImage = $slide->fill($slide->parameters->get('backgroundImage', ''));

            $imageData = N2ImageManager::getImageData($backgroundImage);
        }
        $sizes = $this->slider->assets->sizes;

        $backgroundColor = '';
        $color           = $slide->parameters->get('backgroundColor', '');
        if (strlen($color) == 8 && substr($color, 6, 2) != '00') {
            $backgroundColor = 'background-color: #' . substr($color, 0, 6) . ';';

            if (!class_exists('N2Color')) {
                N2Loader::import("libraries.image.color");
            }

            $rgba    = N2Color::hex2rgba($color);
            $rgba[3] = round($rgba[3] / 127, 2);
            $backgroundColor .= "background-color: RGBA({$rgba[0]}, {$rgba[1]}, {$rgba[2]}, {$rgba[3]});";
        }
        $x = max(0, min(100, $slide->parameters->get('backgroundFocusX', 50)));
        $y = max(0, min(100, $slide->parameters->get('backgroundFocusY', 50)));

        if (empty($backgroundImage)) {
            $src = N2Image::base64Transparent();
        } else {
            $src = N2ImageHelper::dynamic($this->slider->features->optimize->optimizeBackground($backgroundImage, $x, $y));
        }


        $alt      = $slide->fill($slide->parameters->get('backgroundAlt', ''));
        $title    = $slide->fill($slide->parameters->get('backgroundTitle', ''));
        $fillMode = $slide->parameters->get('backgroundMode', 'default');
        if ($fillMode == 'default') {
            $fillMode = $this->slider->params->get('backgroundMode', 'fill');
        }

        if ($dynamicHeight) {
            return $this->simple($backgroundColor, $backgroundImageOpacity, $src, $imageData, $alt, $title, $sizes, $x, $y);
        }
        switch ($fillMode) {
            case 'fit':
                return $this->fit($backgroundColor, $backgroundImageOpacity, $src, $imageData, $alt, $title, $sizes, $x, $y);
            case 'stretch':
                return $this->stretch($backgroundColor, $backgroundImageOpacity, $src, $imageData, $alt, $title, $x, $y);
            case 'center':
                return $this->center($backgroundColor, $backgroundImageOpacity, $src, $imageData, $x, $y);
            case 'tile':
                return $this->tile($backgroundColor, $backgroundImageOpacity, $src, $imageData, $x, $y);
        }
    
        return $this->fill($backgroundColor, $backgroundImageOpacity, $src, $imageData, $alt, $title, $sizes, $x, $y);
    }

    private function getSize($image, $imageData) {
        $size = N2Parse::parse($imageData['desktop']['size']);
        if ($size[0] > 0 && $size[1] > 0) {
            return $size;
        } else {
            list($width, $height) = @getimagesize($image);
            if ($width != null && $height != null) {
                $imageData['desktop']['size'] = $width . '|*|' . $height;
                N2ImageManager::setImageData($image, $imageData);
                return array(
                    $width,
                    $height
                );
            }
        }
        return null;
    }

    private function getDeviceAttributes($image, $imageData) {
        $attributes                 = array();
        $attributes['data-hash']    = md5($image);
        $attributes['data-desktop'] = N2ImageHelper::fixed($image);

        if ($imageData['desktop-retina']['image'] == '' && $imageData['tablet']['image'] == '' && $imageData['tablet-retina']['image'] == '' && $imageData['mobile']['image'] == '' && $imageData['mobile-retina']['image'] == '') {

        } else {
            if ($imageData['desktop-retina']['image'] != '') {
                $attributes['data-desktop-retina'] = N2ImageHelper::fixed($imageData['desktop-retina']['image']);
            }
            if ($imageData['tablet']['image'] != '') {
                $attributes['data-tablet'] = N2ImageHelper::fixed($imageData['tablet']['image']);
            }
            if ($imageData['tablet-retina']['image'] != '') {
                $attributes['data-tablet-retina'] = N2ImageHelper::fixed($imageData['tablet-retina']['image']);
            }
            if ($imageData['mobile']['image'] != '') {
                $attributes['data-mobile'] = N2ImageHelper::fixed($imageData['mobile']['image']);
            }
            if ($imageData['mobile-retina']['image'] != '') {
                $attributes['data-mobile-retina'] = N2ImageHelper::fixed($imageData['mobile-retina']['image']);
            }

            //We have to force the fade on load enabled to make sure the user get great result.
            $this->slider->features->fadeOnLoad->forceFadeOnLoad();
        }
        return $attributes;
    }

    private function getDefaultImage($src, $deviceAttributes) {
        if (count($deviceAttributes) > 2 || $this->slider->features->lazyLoad->isEnabled > 0) {
            return N2Image::base64Transparent();
        } else {
            return N2ImageHelper::fixed($src);
        }
    }

    private function fill($backgroundColor, $backgroundImageOpacity, $src, $imageData, $alt, $title, $sizes, $x, $y) {

        $outerRatio = $sizes['canvasWidth'] / $sizes['canvasHeight'];

        list($width, $height) = $this->getSize($src, $imageData);
        if (!$width || !$height) {
            $style = '';
        } else {
            $ratio = $width / $height;

            if ($outerRatio > $ratio) {
                $style  = 'width: 100%;height: auto;';
                $height = ($sizes['canvasHeight'] - $sizes['canvasWidth'] / $width * $height) / 2;
                $style .= 'margin-top: ' . $height . 'px;';
            } else {
                $style = 'width: auto;height: 100%;';
                $width = ($sizes['canvasWidth'] - $sizes['canvasHeight'] / $height * $width) / 2;
                $style .= 'margin-left: ' . $width . 'px;';
            }
        }

        $deviceAttributes = $this->getDeviceAttributes($src, $imageData);

        return N2Html::tag('div', $deviceAttributes + array(
                "style"        => $backgroundColor,
                "class"        => "n2-ss-slide-background",
                "data-opacity" => $backgroundImageOpacity
            ), N2Html::image($this->getDefaultImage($src, $deviceAttributes), $alt, array(
            "title"  => $title,
            "style"  => $style . 'opacity:' . $backgroundImageOpacity . ';',
            "class"  => "n2-ss-slide-background-image n2-ss-slide-fill n2-ow",
            "data-x" => $x,
            "data-y" => $y
        )));
    }

    private function simple($backgroundColor, $backgroundImageOpacity, $src, $imageData, $alt, $title, $sizes, $x, $y) {

        $style = 'width: 100%;height: auto;';


        $deviceAttributes = $this->getDeviceAttributes($src, $imageData);
        return N2Html::tag('div', $deviceAttributes + array(
                "style"        => $backgroundColor,
                "class"        => "n2-ss-slide-background",
                "data-opacity" => $backgroundImageOpacity
            ), N2Html::image($this->getDefaultImage($src, $deviceAttributes), $alt, array(
            "title"  => $title,
            "style"  => $style . 'opacity:' . $backgroundImageOpacity . ';',
            "class"  => "n2-ss-slide-background-image n2-ss-slide-simple n2-ow",
            "data-x" => $x,
            "data-y" => $y
        )));
    }

    private function fit($backgroundColor, $backgroundImageOpacity, $src, $imageData, $alt, $title, $sizes, $x, $y) {

        $outerRatio = $sizes['canvasWidth'] / $sizes['canvasHeight'];

        list($width, $height) = $this->getSize($src, $imageData);
        if (!$width || !$height) {
            $style = '';
        } else {
            $ratio = $width / $height;
            if ($outerRatio < $ratio) {
                $style  = 'width: 100%;height: auto;';
                $height = ($sizes['canvasHeight'] - $sizes['canvasWidth'] / $width * $height) / 2;
                $style .= 'margin-top: ' . $height . 'px;';
            } else {
                $style = 'width: auto;height: 100%;';
                $width = ($sizes['canvasWidth'] - $sizes['canvasHeight'] / $height * $width) / 2;
                $style .= 'margin-left: ' . $width . 'px;';
            }
        }

        $deviceAttributes = $this->getDeviceAttributes($src, $imageData);
        return N2Html::tag('div', $deviceAttributes + array(
                "style"        => $backgroundColor,
                "class"        => "n2-ss-slide-background",
                "data-opacity" => $backgroundImageOpacity
            ), N2Html::image($this->getDefaultImage($src, $deviceAttributes), $alt, array(
            "title"  => $title,
            "style"  => $style . 'opacity:' . $backgroundImageOpacity . ';',
            "class"  => "n2-ss-slide-background-image n2-ss-slide-fit n2-ow",
            "data-x" => $x,
            "data-y" => $y
        )));
    }

    private function stretch($backgroundColor, $backgroundImageOpacity, $src, $imageData, $alt, $title, $x, $y) {
        $deviceAttributes = $this->getDeviceAttributes($src, $imageData);
        return N2Html::tag('div', $deviceAttributes + array(
                "style"        => $backgroundColor,
                "class"        => "n2-ss-slide-background",
                "data-opacity" => $backgroundImageOpacity
            ), N2Html::image($this->getDefaultImage($src, $deviceAttributes), $alt, array(
            "title"  => $title,
            "style"  => 'opacity:' . $backgroundImageOpacity . ';',
            "class"  => "n2-ss-slide-background-image n2-ss-slide-stretch n2-ow",
            "data-x" => $x,
            "data-y" => $y
        )));
    }

    private function center($backgroundColor, $backgroundImageOpacity, $src, $imageData, $x, $y) {
        $deviceAttributes = $this->getDeviceAttributes($src, $imageData);
        return N2Html::tag('div', $deviceAttributes + array(
                "style"        => $backgroundColor,
                "class"        => "n2-ss-slide-background",
                "data-opacity" => $backgroundImageOpacity
            ), N2Html::tag('div', array(
            "class" => "n2-ss-slide-background-image n2-ss-slide-center",
            "style" => "background-image: url(" . $this->getDefaultImage($src, $deviceAttributes) . ");" . 'opacity:' . $backgroundImageOpacity . ';background-position: ' . $x . '% ' . $y . '%;'
        )));
    }

    private function tile($backgroundColor, $backgroundImageOpacity, $src, $imageData, $x, $y) {
        $deviceAttributes = $this->getDeviceAttributes($src, $imageData);
        return N2Html::tag('div', $deviceAttributes + array(
                "style"        => $backgroundColor,
                "class"        => "n2-ss-slide-background",
                "data-opacity" => $backgroundImageOpacity
            ), N2Html::tag('div', array(
            "class" => "n2-ss-slide-background-image n2-ss-slide-tile",
            "style" => "background-image: url(" . $this->getDefaultImage($src, $deviceAttributes) . ");" . 'opacity:' . $backgroundImageOpacity . ';background-position: ' . $x . '% ' . $y . '%;'
        )));
    }

    private function fixed($backgroundColor, $backgroundImageOpacity, $src, $imageData, $x, $y) {
        $deviceAttributes = $this->getDeviceAttributes($src, $imageData);
        return N2Html::tag('div', $deviceAttributes + array(
                "style"        => $backgroundColor,
                "class"        => "n2-ss-slide-background",
                "data-opacity" => $backgroundImageOpacity
            ), N2Html::tag('div', array(
            "class" => "n2-ss-slide-background-image n2-ss-slide-fixed",
            "style" => "background-image: url(" . $this->getDefaultImage($src, $deviceAttributes) . ");" . 'opacity:' . $backgroundImageOpacity . ';background-position: ' . $x . '% ' . $y . '%;'
        )));
    }
}