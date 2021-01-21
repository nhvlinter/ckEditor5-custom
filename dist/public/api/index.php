<?php
/**
 * Created by PhpStorm.
 * User: Son
 * Date: 14-01-21
 * Time: 22:56
 */
header('Access-Control-Allow-Origin: *'); 
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
http_response_code(200);

use salesteck\api\RequestResponse;
use salesteck\security\Security;
use salesteck\utils\File;
use salesteck\utils\String_Helper;

require_once "../../../vendor/autoload.php";


// Retrive the $_REQUEST
// Prevent from Xss attack
$request = Security::checkXss($_REQUEST);


// Response object to return status
$response = RequestResponse::_inst()
    ->setMessage("An error happened!")
    ->debug($request)
;


// retrieve the content from ajax
$content = array_key_exists('content', $request) ? $request["content"] : null;

// retrieve the tagId from ajax
$tagId = array_key_exists('id', $request) ? $request["id"] : null;

// the file path where to modify content
$contentFilePath = "/dist/public/content.html";


$response
    ->debug("request", $request)
    ->debug("content", $content)
    ->debug("tagId", $tagId)
    ->debug("contentFilePath", $contentFilePath)
;
// Check if the $data & $id
if(String_Helper::_isStringNotEmpty($content) && String_Helper::_isStringNotEmpty($tagId)){

    $response->_line(__LINE__);

    // Check if file exist
    if(File::_fileExist($contentFilePath)){
        //get the content of the file
        $contentFile = File::_fileGetContent($contentFilePath);
        $response
            ->_line(__LINE__)
            ->debug("contentFile", $contentFile)
        ;

        // check if its a string
        if(is_string($contentFile)){
            // Replace special html character
            $content = str_replace("&nbsp;", '', $content);

            // Check if the tagExist
            $tagExist = ContentEditor::_tagExist($contentFile, $tagId);
            $response
                ->_line(__LINE__)
                ->debug("content", $content)
                ->debug("tagExist", $tagExist)
            ;
            if($tagExist){
                //Replace the
                $contentFile = ContentEditor::_replaceBetweenTag($contentFile, [$tagId => $content]);

                // Is content modified correctly
                $modified = File::_setContent($contentFilePath, $contentFile);
                $response
                    ->_line(__LINE__)
                    ->debug("contentFile", $contentFile)
                    ->debug("modified", $modified)
                ;

                if($modified){
                    $response
                        ->setStatus($modified)
                        ->setMessage("Content was correctly modified.")
                    ;
                }
            }
            else{
                $response->setMessage("Tha tag doesn't exist.");
            }
        }

    }
}
$response->display();

