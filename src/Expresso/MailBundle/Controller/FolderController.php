<?php
namespace Expresso\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class FolderController extends Controller
{
    public function ListFoldersAction($folder = 'INBOX')
    {

        $imap = $this->get('ExpressoImap');
        $imap->openMailbox($folder);
        $folders =  $imap->getMailBoxes();

        $return = array();
        foreach($folders as $i => $folder)
        {
            $iFolder = array();
            $iFolder['id'] = mb_convert_encoding( str_replace( $folder->delimiter , '.', substr($folder->name,(strpos($folder->name , '}') + 1))) , 'UTF-8' , 'UTF7-IMAP' ) ;
            $explodeName = explode( '.' , $iFolder['id']);
            $iFolder['cn'] = array_pop($explodeName);
            $iFolder['parentFolder'] = implode('.' , $explodeName) == 'INBOX' ? '' : implode('.' , $explodeName);

            $status = $imap->status(substr($folder->name,(strpos($folder->name , '}') + 1)));
            $iFolder['status']['Messages'] = $status->messages;
            $iFolder['status']['Recent'] = $status->recent;
            $iFolder['status']['Unseen'] = $status->unseen;
            $iFolder['status']['UIDnext'] = $status->uidnext;
            $iFolder['status']['UIDvalidity'] = $status->uidvalidity;

            $return[] = $iFolder;
        }

        $newReturn = array();

        foreach ($imap->getDefaultFolders() as $i => $v)
        {
            foreach ($return as $ii => $vv)
            {
                if($vv['cn'] == $v)
                {
                   $vv['cn'] = $this->get('translator')->trans($vv['cn']);
                   unset($return[$ii]);
                   $newReturn[] = $vv;
                }
            }
        }

        $response = new Response( json_encode( array_merge ($newReturn , $return) ) );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function createFolderAction(){
        $request = Request::createFromGlobals();
        $imap = $this->get('ExpressoImap');
        $imap->openMailbox();
        $response = new Response();
        if($imap->createFolder($request->request->get('folder'))){
            $response->headers->set('Content-Location', '/rest/Mail/Folder/'.$request->request->get('folder'));
            $response->setStatusCode(201);
        }else{
            $response->setContent($this->get('translator')->trans(imap_last_error()));
            $response->setStatusCode(400);
        }
        return $response;
    }

    public function editFolderAction($folder){
        $imap = $this->get('ExpressoImap');
        $imap->openMailbox();

        $put_str = $this->getRequest()->getContent();
        parse_str($put_str, $_PUT);

        $response = new Response();
        if($imap->editFolder($folder,$_PUT['folder'])){
            $response->setStatusCode(204);
            $response->headers->set('Content-Location', '/rest/Mail/Folder/'.$_PUT['folder']);
        }else{
            $response->setContent($this->get('translator')->trans(imap_last_error()));
            $response->setStatusCode(500);
        }
        return $response;
    }

    public function deleteFolderAction($folder){
        $imap = $this->get('ExpressoImap');
        $imap->openMailbox();
        $response = new Response();
        if($imap->deleteFolder($folder)){
            $response->setStatusCode(204);
        }else{
            $response->setContent($this->get('translator')->trans(imap_last_error()));
            $response->setStatusCode(500);
        }
        return $response;
    }

    public function getFolderAction($folder){
        $request = Request::createFromGlobals();
        $imap = $this->get('ExpressoImap');
        $imap->openMailbox();
        $response = new Response();
        if($imap->deleteFolder($folder)){
            $response->setStatusCode(204);
        }else{
            $response->setContent($this->get('translator')->trans(imap_last_error()));
            $response->setStatusCode(500);
        }
        return $response;
    }
}
