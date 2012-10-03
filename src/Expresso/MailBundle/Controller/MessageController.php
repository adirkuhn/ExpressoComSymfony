<?php
namespace Expresso\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class MessageController extends Controller
{
    public function ListMessagesAction( $folder = 'INBOX', $sort = 'SORTDATE' , $limit = 0 , $offset  = 0 )
    {
        $imap = $this->get('ExpressoImap');
        $imap->openMailbox( $folder );

        $reverse = 0;

        if ( $sort[0] === 'R' )
        {
            $sort = defined(substr($sort , 1)) ? constant(substr($sort , 1)) :  SORTDATE;
            $reverse = 1;
        }
        else
            $sort = defined(constant($sort)) ? constant($sort) :  SORTDATE ;

        $mails = $imap->sort( $sort , $reverse , 0 );

        if($limit !== 0)
            $mails = array_slice($mails, $offset , $limit );

        $return = $this->getListMessages($imap, $mails);

        $response = new Response( json_encode( $return ) );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function jqGridListMessagesAction($folder = 'INBOX')
    {
        $request = Request::createFromGlobals();

        $imap = $this->get('ExpressoImap');
        $imap->openMailbox( $folder );

        $limit= $request->query->get('rows') ? (int)$request->query->get('rows') : 50;
        $offset= $request->query->get('page') ? ((int)$request->query->get('page')*$limit - $limit): 0;
        $sort= $request->query->get('sidx') ? constant($request->query->get('sidx')) : SORTDATE;
        $reverse= $request->query->get('sord') ?
                    $request->query->get('sord') === "asc" ? 0 : 1 
                : 1;

        $return = array();

        $mails = $imap->sort( $sort , $reverse , 0 );

        $return['records'] = count($mails);
        $return['total'] = count($mails) > 0 ? ceil( count($mails)/$limit ) : 0;
        $return['page'] = (int)$request->query->get('page') ? (int)$request->query->get('page') : 1;
        
        if($limit !== 0)
            $mails = array_slice($mails, $offset , $limit );

        $return['rows'] = $this->getListMessages($imap, $mails);

        $response = new Response( json_encode( $return ) );
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    private function getListMessages( &$imap , $mails )
    {
        $return = array();
        foreach( $mails as $i => $UID )
        {
            $mailObject = $imap->headerInfo($UID);
            $mimeBody = $imap->body( $UID );
            $mimeHeader = $imap->fetchHeader( $UID );

            $return[$i]['id'] = $mailObject->Msgno;
            $return[$i]['flags']['Recent'] = $mailObject->Recent == 'R' ? true : false;
            $return[$i]['flags']['Unseen'] = $mailObject->Unseen == 'U' ? true : false;
            $return[$i]['flags']['Flagged'] = $mailObject->Flagged == 'F' ? true : false;
            $return[$i]['flags']['Answered'] = $mailObject->Answered == 'A' ? true : false;
            $return[$i]['flags']['Draft'] = $mailObject->Draft == 'D' ? true : false;
            $return[$i]['flags']['Attachment'] = ( preg_match('/((Content-Disposition:(.)*([\r\n\s]*filename))|(Content-Type:(.)*([\r\n\s]*name)))/i', $mimeBody) ) ? true : false;
            $return[$i]['flags']['Importance'] = ( preg_match('/importance *: *(.*)\r/i', $mimeHeader , $importance) === 0 ) ? true : false;
            $return[$i]['subject'] = $imap->decodeMimeString($mailObject->subject);
            $return[$i]['to'] = (isset($mailObject->to) && is_array($mailObject->to) && count($mailObject->to) > 0) ? $imap->formatMailObjects($mailObject->to) : array();
            $return[$i]['from'] = (isset($mailObject->from) && is_array($mailObject->from) && count($mailObject->from) > 0) ? $imap->formatMailObjects($mailObject->from) : array();
            $return[$i]['cc'] = (isset($mailObject->cc) && is_array($mailObject->cc) && count($mailObject->cc) > 0) ? $imap->formatMailObjects($mailObject->cc) : array();
            $return[$i]['replyTo'] = (isset($mailObject->reply_to) && is_array($mailObject->reply_to) && count($mailObject->reply_to) > 0) ? $imap->formatMailObjects($mailObject->reply_to) : array();
            $return[$i]['sender'] = (isset($mailObject->sender) && is_array($mailObject->sender) && count($mailObject->sender) > 0) ? $imap->formatMailObjects($mailObject->sender) : array();
            $return[$i]['udate'] = $mailObject->udate;
            $return[$i]['date'] = $mailObject->MailDate;
            $return[$i]['size'] = $mailObject->Size;
        }
        return $return;
    }
}
