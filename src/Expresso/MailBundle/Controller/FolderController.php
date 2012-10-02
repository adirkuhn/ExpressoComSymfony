<?php
namespace Expresso\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class FolderController extends Controller
{
    public function ListFoldersAction()
    {

        $imap = $this->get('ExpressoImap');
        $imap->openMailbox();
        $folders =  $imap->getMailBoxes();

        $return = array();
        foreach($folders as $i => $folder)
        {
            $iFolder = array();
            $iFolder['id'] = mb_convert_encoding( str_replace( $folder->delimiter , '.', substr($folder->name,(strpos($folder->name , '}') + 1))) , 'UTF-8' , 'UTF7-IMAP' ) ;
            $explodeName = explode( '.' , $iFolder['id']);
            $iFolder['cn'] = array_pop($explodeName);
            $iFolder['parentFolder'] = implode('.' , $explodeName);

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
}
