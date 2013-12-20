<?php
    /**
    ARI Soft copyright
    * Copyright (C) 2008 ARI Soft.
    * All Rights Reserved.  No use, copying or distribution of this
    * work may be made except in accordance with a valid license
    * agreement from ARI Soft. This notice must be included on
    * all copies, modifications and derivatives of this work.
    *
    * ARI Soft products are provided "as is" without warranty of
    * any kind, either expressed or implied. In no event shall our
    * juridical person be liable for any damages including, but
    * not limited to, direct, indirect, special, incidental or
    * consequential damages or other losses arising out of the use
    * of or inability to use our products.
    *
    *
    */
    defined('ARI_FRAMEWORK_LOADED') or die ('Direct Access to this location is not allowed.');
    AriKernel::import('Controllers.FileController');
    AriKernel::import('Xml.SimpleXml');
    AriKernel::import('Entity.EntityFactory');
    AriKernel::import('Entity.AriDBTable');
    class AriXmlInstaller extends AriObject {
        function doInstallFile($file, $option) {
            if (empty($file) || !file_exists($file)) return;
            $xmlStr = file_get_contents($file);
            $xml = new AriSimpleXML();
            $xml->loadString($xmlStr);
            $doc = $xml->document;
            $path = pathinfo($file);
            $path = $path['dirname'].'/';
            $childs = $doc->children();
            foreach($childs as $child) {
                $tag = $child->name();
                switch ($tag) {
                    case 'files':
                        $this->_parseFilesTag($child, $path, $option);
                    break;
                }
            }
        }
        function _parseFilesTag($node, $path, $option) {
            $folder = $node->attributes('folder');
            if ($folder) $path.= $folder.'/';
            $tagName = 'file';
            $fileNodeList = isset($node->$tagName) ? $node->$tagName : null;
            if (empty($fileNodeList)) return;
            $group = $node->attributes('group');
            $files = array();
            foreach($fileNodeList as $fileNode) {
                $tagName = 'name';
                $name = $fileNode-> {
                    $tagName
                }
                [0]->data();
                if (!file_exists($path.$name)) continue;
                $tagName = 'shortDescription';
                $tagName1 = 'flags';
                $tagName2 = 'action';
                $files[$name] = array('shortDescription'=>isset($fileNode->$tagName) ? $fileNode-> {
                    $tagName
                }
                [0]->data() : '', 'flags'=>isset($fileNode->$tagName1) ? $fileNode-> {
                    $tagName1
                }
                [0]->data() : 0, 'action'=>isset($fileNode->$tagName2) ? $fileNode-> {
                    $tagName2
                }
                [0]->data() : '', 'update'=>false);
            }
            $fileController = new AriFileController(AriConstantsManager::getVar('FileTable', $option));
            $existsFiles = $fileController->getFileList($group, array(), true);
            if (!empty($existsFiles)) {
                foreach($existsFiles as $existFile) {
                    $name = $existFile->FileName;
                    if (isset($files[$name])) {
                        $file = $files[$name];
                        if ($file['flags'] == $existFile->Flags) {
                            $files[$name]['update'] = true;
                            $files[$name]['oldContent'] = $existFile->Content;
                            $files[$name]['fileId'] = $existFile->FileId;
                        }
                    }
                }
            }
            global $my;
            $ownerId = $my->get('id');
            if (!empty($files)) {
                $nullValue = null;
                foreach($files as $name=>$file) {
                    if ($file['update']) {
                        if ($file['action'] == 'Merge') {
                            $baseRes = ArisI18NHelper::parseXmlFromFile($path.$name, $nullValue);
                            $res = ArisI18NHelper::parseXmlFromString($file['oldContent'], $nullValue);
                            $res = ArisI18NHelper::mergeResources($baseRes, $res);
                            $xml = ArisI18NHelper::createXmlFromData($res);
                            $xmlStr = ARI_I18N_TEMPLATE_XML.$xml->document->toString();
                            $fields = array();
                            $fields['Group'] = $group;
                            $fields['FileName'] = $name;
                            $fields['Flags'] = $file['flags'];
                            $fields['Content'] = $xmlStr;
                            $fileController->saveFile($file['fileId'], $fields, $ownerId);
                        } else if ($file['action'] == 'Update') {
                            $fields = array();
                            $fields['ShortDescription'] = $file['shortDescription'];
                            $fields['Group'] = $group;
                            $fields['FileName'] = $name;
                            $fields['Flags'] = $file['flags'];
                            $fileController->saveFileFromFile($file['fileId'], $fields, $path.$name, $ownerId);
                        }
                    } else {
                        $fields = array();
                        $fields['ShortDescription'] = $file['shortDescription'];
                        $fields['Group'] = $group;
                        $fields['FileName'] = $name;
                        $fields['Flags'] = $file['flags'];
                        $fileController->saveFileFromFile(0, $fields, $path.$name, $ownerId);
                    }
                }
            }
        }
    };
?>