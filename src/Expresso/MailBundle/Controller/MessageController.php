<?php
namespace Expresso\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function ListMessagesAction( $folder = 'INBOX', $limit = 0 , $offset  = 0 )
    {

        $imap = $this->get('ExpressoImap');
        $imap->openMailbox( $folder );
        $mails = $imap->sort();

        if($limit !== 0)
            $mails = array_slice($mails, $offset , $limit );

//        $return = array();
//        foreach($folders as $i => $folder)
//        {
//            $iFolder = array();
//            $iFolder['id'] = mb_convert_encoding(substr($folder->name,(strpos($folder->name , '}') + 1)) , 'UTF-8' , 'UTF7-IMAP' ) ;
//            $explodeName = explode( $folder->delimiter , $iFolder['id']);
//            $iFolder['cn'] = array_pop($explodeName);
//            $iFolder['parentFolder'] = implode('/' , $explodeName);
//
//            $status = $imap->status(substr($folder->name,(strpos($folder->name , '}') + 1)));
//            $iFolder['status']['Messages'] = $status->messages;
//            $iFolder['status']['Recent'] = $status->recent;
//            $iFolder['status']['Unseen'] = $status->unseen;
//            $iFolder['status']['UIDnext'] = $status->uidnext;
//            $iFolder['status']['UIDvalidity'] = $status->uidvalidity;
//
//            $return[] = $iFolder;
//        }
//
//        $newReturn = array();
//
//        foreach ($imap->getDefaultFolders() as $i => $v)
//        {
//            foreach ($return as $ii => $vv)
//            {
//                if($vv['cn'] == $v)
//                {
//                    $vv['cn'] = $this->get('translator')->trans($vv['cn']);
//                    unset($return[$ii]);
//                    $newReturn[] = $vv;
//                }
//            }
//        }

        $response = new Response( json_encode( $mails ) );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
