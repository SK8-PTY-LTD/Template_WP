<?php


class N2SmartsliderBackendSlidersView extends N2ViewBase
{

    public function renderImportByUploadForm() {

        N2SmartsliderSlidersModel::renderImportByUploadForm();
    }

    public function renderRestoreByUploadForm() {

        N2SmartsliderSlidersModel::renderRestoreByUploadForm();
    }

    public function renderImportFromServerForm() {

        N2SmartsliderSlidersModel::renderImportFromServerForm();
    }

    public function renderRestoreFromServerForm(){
        N2SmartsliderSlidersModel::renderRestoreFromServerForm();
    }
} 