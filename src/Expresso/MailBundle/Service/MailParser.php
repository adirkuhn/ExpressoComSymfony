<?php

namespace Expresso\MailBundle\Service;

class MailParser
{
    var $rawMail;
    var $structure;
    var $params;

    public function __construct(  )
    {
        require_once __DIR__ . '/../Resources/Library/Mime/Mail_mimeDecode.php';

        $this->params['include_bodies'] = true;
        $this->params['decode_bodies']  = true;
        $this->params['decode_headers'] = true;
        $this->params['rfc_822bodies']  = true;
    }

    public function setRawMail( $rawMail )
    {
        $this->rawMail = $rawMail;
        $decoder = new \Mail_mimeDecode($rawMail);
        $this->structure = $decoder->decode($this->params);
        if($this->isSignedMenssage())
            $this->convertSignedMenssage();
    }

    public function parseBody( )
    {
        $content = '';

        /*
        * Inicia Gerenciador de Anexos
        */
       // $attachmentManager = new Attachment();
        //$attachmentManager->setStructure($structure);
        //----------------------------------------------//

//        /*
//         * Monta informações dos anexos para o cabecalhos
//         */
//         $attachments = $this->getAttachmentsInfo();
//        $return['attachments'] = $attachments;
//        //----------------------------------------------//

        /*
         * Monta informações das imagens
         */
       // $images = $attachmentManager->getEmbeddedImagesInfo();
        //----------------------------------------------//

        switch (strtolower($this->structure->ctype_primary))
        {
            case 'text':
                if(strtolower($this->structure->ctype_secondary) == 'x-pkcs7-mime')
                {
                    $return['body']='isCripted';
                    return $return;
                }
                $attachment = array();

                $msg_subtype = strtolower($this->structure->ctype_secondary);
                if(isset($this->structure->disposition))
                    $disposition = strtolower($this->structure->disposition);
                else
                    $disposition = '';

                if(($msg_subtype == "html" || $msg_subtype == 'plain') && ($disposition != 'attachment'))
                {
                    if(strtolower($msg_subtype) == 'plain')
                    {
                        if(isset($this->structure->ctype_parameters['charset']))
                            $content = $this->decodeMailPart($this->structure->body, $this->structure->ctype_parameters['charset'],false);
                        else
                            $content = $this->decodeMailPart($this->structure->body, null,false);
                        $content = str_replace( array( '<', '>' ), array( ' #$<$# ', ' #$>$# ' ), $content );
                        $content = htmlentities( $content );
                        $this->replaceLinks($content);
                        $content = str_replace( array( ' #$&lt;$# ', ' #$&gt;$# ' ), array( '&lt;', '&gt;' ), $content );
                        $content = '<pre>' . $content . '</pre>';
                        $return[ 'body' ] = $content;
                        return $return;
                    }
                    $content = $this->decodeMailPart($this->structure->body, $this->structure->ctype_parameters['charset']);
                }
                if(strtolower($this->structure->ctype_secondary) == 'calendar')
//                    $content .= $this->builderMsgCalendar($this->structure->body);

                break;

            case 'multipart':
                $this->builderMsgBody($this->structure , $content);

                break;

//            case 'message':
////
////                    if(!is_array($this->structure->parts))
////                    {
////                        $content .= "<hr align='left' width='95%' style='border:1px solid #DCDCDC'>";
////                        $content .= '<pre>'.htmlentities($this->decodeMailPart($this->structure->body, $this->structure->ctype_parameters['charset'],false)).'</pre>';
////                        $content .= "<hr align='left' width='95%' style='border:1px solid #DCDCDC'>";
////                    }
////                    else
////                        $this->builderMsgBody($this->structure , $content,true);
////
//                break;
//
//            case 'application':
//                if(strtolower($this->structure->ctype_secondary) == 'x-pkcs7-mime')
//                {
//                    //  $return['body']='isCripted';
//                    // return $return;
//
//                    $rawMessageData2 = $this->extractSignedContents($this->rawMail);
//                    if($rawMessageData2 === false){
//                        $return['body']='isCripted';
//                        return $return;
//                    }
//                    $decoder2 = new \Mail_mimeDecode($rawMessageData2);
//                    $structure2 = $decoder2->decode($this->params);
//                    $this-> builderMsgBody($structure2 , $content);
//
//                  //  $attachmentManager->setStructure($structure2);
////                    /*
////                    * Monta informações dos anexos para o cabecarios
////                    */
////                    $attachments = $attachmentManager->getAttachmentsInfo();
////                    $return['attachments'] = $attachments;
////
////                    //----------------------------------------------//
//
//                    /*
//                   * Monta informações das imagens
//                    */
//             //       $images = $attachmentManager->getEmbeddedImagesInfo();
//                    //----------------------------------------------//
//
////                    if(!$this->has_cid){
////                        $return['thumbs']    = $this->get_thumbs($images,$msg_number,$msg_folder);
////                        $return['signature'] = $this->get_signature($msg,$msg_number,$msg_folder);
////                    }
//                }
            ///////////////////////////////////////////////////////////////////////////////////////////
            default:
                if(count($this->getAttachmentsInfo()) > 0)
                    $content .= '';
                break;
        }

       // $content = $this->processEmbeddedImages($msgUID,$content, $folder);
        $content = $this->replaceSpecialCharacters($content);
        $this->replaceLinks($content);
        $return['body'] = &$content;

        return $return;


    }

    function decodeMailPart($part, $encode, $html = true)
    {
        switch (strtolower($encode))
        {
            case 'iso-8859-1':
                return mb_convert_encoding(  $part , 'UTF-8' , 'ISO-8859-1');
                break;
            case 'utf-8':
                return $part;
                break;
            default:
                return mb_convert_encoding($part, 'UTF-8');
                break;
        }
    }

    function builderMsgBody($structure , &$content )
    {
        if(strtolower($structure->ctype_primary) == 'multipart' && strtolower($structure->ctype_secondary) == 'alternative')
        {
            $numParts = count($structure->parts) - 1;

            for($i = $numParts; $i >= 0; $i--)
            {
                $part = $structure->parts[$i];

                switch (strtolower($part->ctype_primary))
                {
                    case 'text':
                        $disposition = isset($part->disposition) ? strtolower($part->disposition) : '';
                        if($disposition != 'attachment')
                        {
                            if(strtolower($part->ctype_secondary) == 'html')
                            {
//                                if($printHeader)
//                                    $content .= $this->builderMsgHeader($part);

                                $content .= $this->decodeMailPart($part->body,$part->ctype_parameters['charset']);
                            }

                            if(strtolower($part->ctype_secondary) == 'plain' )
                            {
//                                if($printHeader)
//                                    $content .= $this->builderMsgHeader($part);

                                $content .= '<pre>'. htmlentities($this->decodeMailPart($part->body,$part->ctype_parameters['charset'],false)).'</pre>';
                            }
                            if(strtolower($part->ctype_secondary) == 'calendar')
                                $content.= $this->builderMsgCalendar($this->decodeMailPart($part->body, $part->ctype_parameters['charset']));

                        }

                        $i = -1;
                        break;

                    case 'multipart':

//                        if($printHeader)
//                            $content .= $this->builderMsgHeader($part);

                        $this->builderMsgBody($part,$content);

                        $i = -1;
                        break;

//                    case 'message':
//
//                        if(!is_array($part->parts))
//                        {
//                            $content .= "<hr align='left' width='95%' style='border:1px solid #DCDCDC'>";
//                            $content .= '<pre>'. htmlentities($this->decodeMailPart($part->body, $structure->ctype_parameters['charset'],false)).'</pre>';
//                            $content .= "<hr align='left' width='95%' style='border:1px solid #DCDCDC'>";
//                        }
//                        else
//                            $this->builderMsgBody($part,$content,true);
//
//                        $i = -1;
//                        break;
                }
            }
        }
        else
        {
            foreach ($structure->parts  as $index => $part)
            {
                switch (strtolower($part->ctype_primary))
                {
                    case 'text':
                        $disposition = '';
                        if(isset($part->disposition))
                            $disposition = isset($part->disposition) ? strtolower($part->disposition) : '';
                        if($disposition != 'attachment')
                        {
                            if(strtolower($part->ctype_secondary) == 'html')
                            {
//                                if($printHeader)
//                                    $content .= $this->builderMsgHeader($part);

                                $content .= $this->decodeMailPart($part->body,$part->ctype_parameters['charset']);
                            }

                            if(strtolower($part->ctype_secondary) == 'plain')
                            {
//                                if($printHeader)
//                                    $content .= $this->builderMsgHeader($part);

                                $content .= '<pre>'. htmlentities($this->decodeMailPart($part->body,$part->ctype_parameters['charset'],false)).'</pre>';
                            }
                            if(strtolower($part->ctype_secondary) == 'calendar')
                                $content .= $this->builderMsgCalendar($part->body);

                        }
                        break;
                    case 'multipart':

//                        if($printHeader)
//                            $content .= $this->builderMsgHeader($part);

                        $this->builderMsgBody($part,$content);

                        break;
//                    case 'message':
//                        if($_SESSION['phpgw_info']['user']['preferences']['expressoMail']['nested_messages_are_shown'] != '1')
//                        {
//                            if(!is_array($part->parts))
//                            {
//                                $content .= "<hr align='left' width='95%' style='border:1px solid #DCDCDC'>";
//                                $content .= '<pre>'.  htmlentities($this->decodeMailPart($part->body, $structure->ctype_parameters['charset'],false)).'</pre>';
//                                $content .= "<hr align='left' width='95%' style='border:1px solid #DCDCDC'>";
//                            }
//                            else
//                                $this->builderMsgBody($part,$content,true);
//                            break;
//                        }
                }
            }
        }
    }


//    function processEmbeddedImages($images, $msgno, $body, $msg_folder)
//    {
//
//        foreach ($images as $image)
//        {
//            $image['cid'] = preg_replace('/</i', '', $image['cid']);
//            $image['cid'] = preg_replace('/>/i', '', $image['cid']);
//
//            $body = str_replace("src=\"cid:".$image['cid']."\"", " src=\"./inc/get_archive.php?msgFolder=$msg_folder&msgNumber=$msgno&indexPart=".$image['pid']."\" ", $body);
//            $body = str_replace("src='cid:".$image['cid']."'", " src=\"./inc/get_archive.php?msgFolder=$msg_folder&msgNumber=$msgno&indexPart=".$image['pid']."\"", $body);
//            $body = str_replace("src=cid:".$image['cid'], " src=\"./inc/get_archive.php?msgFolder=$msg_folder&msgNumber=$msgno&indexPart=".$image['pid']."\"", $body);
//        }
//        return $body;
//    }

    function replaceSpecialCharacters($body)
    {
        if(trim($body) === '') return;

        $body = str_ireplace('POSITION: ABSOLUTE;','', $body);
        $body = str_ireplace('<o:p>&nbsp;</o:p>','<br />', $body);//Qubra de linha do MSO
        $body = preg_replace('/<(meta|base|link|html|\/html)[^>]*>/i', '', $body);


        // Malicious Code Remove
        $dirtyCodePattern = "/(<([\w]+[\w0-9]*)(.*)on(mouse(move|over|down|up)|load|blur|change|error|click|dblclick|focus|key(down|up|press)|select)([\n\ ]*)=([\n\ ]*)[\"'][^>\"']*[\"']([^>]*)>)(.*)(<\/\\2>)?/misU";
        preg_match_all($dirtyCodePattern, $body, $rest, PREG_PATTERN_ORDER);

//        foreach ($rest[0] as $i => $val) {
//            if (!(preg_match("/javascript:window\.open\(\"([^'\"]*)\/index\.php\?menuaction=calendar\.uicalendar\.set_action\&cal_id=([^;'\"]+);?['\"]/i", $rest[1][$i]) && strtoupper($rest[4][$i]) == "CLICK" )) //Calendar events
//                $body = str_replace($rest[1][$i], "<" . $rest[2][$i] . $rest[3][$i] . $rest[7][$i] . ">", $body);
//        }

        require_once(__DIR__ . '/../Resources/Library/CssToInlineStyles/css_to_inline_styles.php');
        $cssToInlineStyles = new \CSSToInlineStyles($body);
        $cssToInlineStyles->setUseInlineStylesBlock(true);
        $cssToInlineStyles->setCleanup(TRUE);
        $body = $cssToInlineStyles->convert(); //Converte as tag style em inline styles

        ///--------------------------------//
        // tags to be removed doe to security reasons
        $tag_list = Array(
            'blink', 'object', 'frame', 'iframe',
            'layer', 'ilayer', 'plaintext', 'script',
            'applet', 'embed', 'frameset', 'xml', 'xmp','style','head'
        );

        foreach ($tag_list as $index => $tag)
            $body = @mb_eregi_replace("<$tag\\b[^>]*>(.*?)</$tag>", '', $body);

        /*
        * Remove deslocamento a esquerda colocado pelo Outlook.
        * Este delocamento faz com que algumas palavras fiquem escondidas atras da barra lateral do expresso.
        */
        $body = mb_ereg_replace("(<p[^>]*)(text-indent:[^>;]*-[^>;]*;)([^>]*>)", "\\1\\3", $body);
        $body = mb_ereg_replace("(<p[^>]*)(margin-right:[^>;]*-[^>;]*;)([^>]*>)", "\\1\\3", $body);
        $body = mb_ereg_replace("(<p[^>]*)(margin-left:[^>;]*-[^>;]*;)([^>]*>)", "\\1\\3", $body);
        //--------------------------------------------------------------------------------------------//
        $body = str_ireplace('position:absolute;', '', $body);

        //Remoção de tags <span></span> para correção de erro no firefox
        //Comentado pois estes replaces geram erros no html da msg, não se pode garantir que o os </span></span> sejam realmente os fechamentos dos <span><span>.
        //Caso realmente haja a nescessidade de remover estes spans deve ser repensado a forma de como faze-lo.
        //		$body = mb_eregi_replace("<span><span>","",$body);
        //		$body = mb_eregi_replace("</span></span>","",$body);
        //Correção para compatibilização com Outlook, ao visualizar a mensagem
        $body = mb_ereg_replace('<!--\[', '<!-- [', $body);
        $body = mb_ereg_replace('&lt;!\[endif\]--&gt;', '<![endif]-->', $body);
        $body  = preg_replace("/<p[^\/>]*>([\s]?)*<\/p[^>]*>/", '', $body); //Remove paragrafos vazios (evita duplo espaçamento em emails do MSO)

        return  $body;
    }

    function replaceLinksCallback($matches)
    {
        if($matches[3])
            $pref = $matches[3];
        else
            $pref = $matches[3] = 'http';

        $a = isset($matches[5]) ? $matches[5] : '';

        return '<a href="'.$pref.'://'.$matches[4]. $a  .'" target="_blank">'.$matches[0].'</a>';
    }


    /**
     * @license   http://www.gnu.org/copyleft/gpl.html GPL
     * @author    Consórcio Expresso Livre - 4Linux (www.4linux.com.br) e Prognus Software Livre (www.prognus.com.br)
     * @param     $body corpo da mensagem
     */
    function replaceLinks(&$body)
    {
        // Trata urls do tipo aaaa.bbb.empresa
        // Usadas na intranet.
        $pattern = '/(?<=[\s|(<br>)|\n|\r|;])(((http|https|ftp|ftps)?:\/\/((?:[\w]\.?)+(?::[\d]+)?[:\/.\-~&=?%;@#,+\w]*))|((?:www?\.)(?:\w\.?)*(?::\d+)?[\:\/\w.\-~&=?%;@+]*))/i';
        $body = preg_replace_callback($pattern,array( &$this, 'replaceLinksCallback'), $body);

    }

    private function isSignedMenssage()
    {
        if(strtolower($this->structure->ctype_primary) == 'application' && strtolower($this->structure->ctype_secondary) == 'x-pkcs7-mime' )
            return true;
        else
            return false;
    }

    private function convertSignedMenssage()
    {
        $decoder = new \Mail_mimeDecode($this->extractSignedContents($this->rawMail));
        $this->structure = $decoder->decode($this->params);
    }

    private function extractSignedContents( $data )
    {
        $pipes_desc = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w')
        );

        $fp = proc_open( 'openssl smime -verify -noverify -nochain', $pipes_desc, $pipes);
        if (!is_resource($fp)) {
            return false;
        }

        $output = '';

        /* $pipes[0] => writeable handle connected to child stdin
           $pipes[1] => readable handle connected to child stdout */
        fwrite($pipes[0], $data);
        fclose($pipes[0]);

        while (!feof($pipes[1])) {
            $output .= fgets($pipes[1], 1024);
        }
        fclose($pipes[1]);
        proc_close($fp);

        return $output;
    }

    public function getAttachment($partNumber)
    {
        $partContent = '';
        $this->_getPartContent($this->structure, $partNumber, $partContent);
        return $partContent;
    }

    public function getEmbeddedImagesInfo()
    {
        $imagesEmbedded = array();
        $this->_getEmbeddedImagesInfo($this->structure,$imagesEmbedded);
        return $imagesEmbedded;
    }
    public function getAttachmentsInfo()
    {
        $attachments = array();
        $this->_getAttachmentsInfo($this->structure,$attachments);
        return $attachments;
    }
    public function getAttachmentInfo($partNumber)
    {
        $attachment = array();
        $this->_getPartInfo($this->structure,$partNumber,$attachment);
        return $attachment[0];
    }
    private function _getPartContent(&$structure, $soughtIndex,&$body,$pIndex = '0')
    {
        if($structure->parts)
        {
            foreach ($structure->parts  as $index => $part)
            {
                if(strtolower($part->ctype_primary) == 'multipart')
                    $this->_getPartContent($part,$soughtIndex,$body,$pIndex.'.'.$index);
                else
                {
                    if(strtolower($part->ctype_primary) == 'message' && is_array($part->parts) && $this->params['rfc_822bodies'] !== true )
                        $this->_getPartContent($part,$soughtIndex,$body,$pIndex.'.'.$index);
                    else
                    {
                        $currentIndex = $pIndex.'.'.$index;
                        if($currentIndex === $soughtIndex)
                        {
                            $body = $part->body;
                            break;
                        }
                    }
                }
            }
        }
        else if($soughtIndex == '0')
            $body = $structure->body;
    }
    private function _getPartInfo(&$structure, $soughtIndex,&$info,$pIndex = '0')
    {

        if($structure->parts)
        {
            foreach ($structure->parts  as $index => $part)
            {
                if(strtolower($part->ctype_primary) == 'multipart')
                    $this->_getPartInfo($part,$soughtIndex,$info,$pIndex.'.'.$index);
                else
                {
                    if(strtolower($part->ctype_primary) == 'message' && is_array($part->parts) && $this->params['rfc_822bodies'] !== true)
                        $this->_getPartInfo($part,$soughtIndex,$info,$pIndex.'.'.$index);
                    else
                    {
                        $currentIndex = $pIndex.'.'.$index;
                        if($currentIndex === $soughtIndex)
                        {
                            $this->_pushAttachmentsInfo($part,$info,$currentIndex);
                            break;
                        }
                    }
                }
            }
        }else if($soughtIndex == '0')
            $this->_pushAttachmentsInfo($structure,$info);
    }
    private function _getAttachmentsInfo($structure, &$attachments, $pIndex = '0')
    {

        if(isset($structure->parts))
        {
            foreach ($structure->parts  as $index => $part)
            {
                if(strtolower($part->ctype_primary) == 'multipart')
                    $this->_getAttachmentsInfo($part,$attachments,$pIndex.'.'.$index);
                else
                {
                    if(strtolower($part->ctype_primary) == 'message' && is_array($part->parts) && $this->params['rfc_822bodies'] !== true)
                        $this->_getAttachmentsInfo($part,$attachments,$pIndex.'.'.$index);
                    else
                    {
                        if(!isset($part->headers['content-transfer-encoding'])) //Caso não esteja especificado a codificação
                            $part->headers['content-transfer-encoding'] = mb_detect_encoding ($part->body,array('BASE64','Quoted-Printable','7bit','8bit','ASCII'));
                        if($part->headers['content-transfer-encoding'] === ('ASCII' || false)) //Caso a codificação retorne ascii ou false especifica como base64
                            $part->headers['content-transfer-encoding'] = 'base64';

                        $this->_pushAttachmentsInfo($part,$attachments,$pIndex.'.'.$index);

                    }
                }
            }
        }
        else
            $this->_pushAttachmentsInfo($structure,$attachments);

    }
    private function _pushAttachmentsInfo(&$structure, &$attachments, $pIndex = '0')
    {
        $name = '';
        if((isset($structure->d_parameters['filename']))&& ($structure->d_parameters['filename'])) $name = $structure->d_parameters['filename'];
        else if((isset($structure->ctype_parameters['name']))&&($structure->ctype_parameters['name'])) $name = $structure->ctype_parameters['name'];
        else if(strtolower($structure->ctype_primary) == 'text' &&  strtolower($structure->ctype_secondary) == 'calendar') $name = 'calendar.ics';

        //Attachments com nomes grandes são quebrados em varias partes VER RFC2231
        if( !$name && isset($structure->disposition) && (strtolower($structure->disposition) === 'attachment' || strtolower($structure->ctype_primary) == 'image' ||  strtolower($structure->ctype_primary.'/'.$structure->ctype_secondary) == 'application/octet-stream'))
            foreach ($structure->d_parameters as $i => $v)
                if(strpos($i , 'filename') !== false)
                    $name .= urldecode(str_ireplace(array('ISO-8859-1','UTF-8','US-ASCII'),'',$v));
        if( !$name && isset($structure->disposition) && (strtolower($structure->disposition) === 'attachment' || strtolower($structure->ctype_primary) == 'image' ||  strtolower($structure->ctype_primary.'/'.$structure->ctype_secondary) == 'application/octet-stream') )
            foreach ($structure->ctype_parameters as $i => $v)
                if(strpos($i , 'name') !== false)
                    $name .= urldecode(str_ireplace(array('ISO-8859-1','UTF-8','US-ASCII'),'',$v));
        ////////////////////////////////////////////////////////////////////////////////////


        if($structure->ctype_primary == 'message') {
            $attach = new \MailParser();
            $attach->setRawMail($structure->body);

            if (!$name)
                $name = (isset($attach->structure->headers['subject'])) ? $attach->structure->headers['subject'] : 'no title';

            if (!preg_match("/\.eml$/", $name))
                $name .= '.eml';
        }

        if(!$name && strtolower($structure->ctype_primary) == 'image')
        {
            if(strlen($structure->ctype_secondary) === 3)
                $ext = strtolower($structure->ctype_secondary);

            $name = 'Embedded-Image.'.$ext;
        }

        if($name)
        {
            $codificao =  mb_detect_encoding($name.'x', 'UTF-8, ISO-8859-1');
            if($codificao == 'UTF-8') $name = utf8_decode($name);

            $definition['pid'] = $pIndex;
            $definition['name'] = addslashes(mb_convert_encoding($name, "ISO-8859-1"));
            $definition['encoding'] = $structure->headers['content-transfer-encoding'];
            $definition['type'] = strtolower($structure->ctype_primary).'/'.strtolower($structure->ctype_secondary);
            $definition['fsize'] = mb_strlen($structure->body, $structure->headers['content-transfer-encoding']);

            array_push($attachments, $definition);
        }
    }
    private function _getEmbeddedImagesInfo($structure, &$images, $pIndex = '0')
    {
        if(isset($structure->parts))
            foreach ($structure->parts  as $index => $part)
            {
                if(strtolower($part->ctype_primary) == 'multipart')
                    $this->_getEmbeddedImagesInfo($part,$images,$pIndex.'.'.$index);
                else
                {
                    if(strtolower($part->ctype_primary) == 'message' && is_array($part->parts) && $this->params['rfc_822bodies'] !== true)
                        $this->_getEmbeddedImagesInfo($part,$images,$pIndex.'.'.$index);
                    else
                    {
                        if(is_array($part->ctype_parameters) && !array_key_exists('name', $part->ctype_parameters))
                            if(isset($part->d_parameters['filename']))
                                $name = $part->d_parameters['filename'];
                            else
                                $name = null;
                        else
                            $name = $part->ctype_parameters['name'];

                        //Attachments com nomes grandes são quebrados em varias partes VER RFC2231
                        if( !$name && isset($part->disposition) && (strtolower($part->disposition) === 'attachment' || strtolower($part->ctype_primary) == 'image' ||  strtolower($part->ctype_primary.'/'.$part->ctype_secondary) == 'application/octet-stream') )
                            foreach ($part->d_parameters as $i => $v)
                                if(strpos($i , 'filename') !== false)
                                    $name .= urldecode(str_ireplace(array('ISO-8859-1','UTF-8','US-ASCII'),'',$v));
                        if( !$name && isset($part->disposition) && (strtolower($part->disposition) === 'attachment' || strtolower($part->ctype_primary) == 'image' ||  strtolower($part->ctype_primary.'/'.$part->ctype_secondary) == 'application/octet-stream') )
                            foreach ($part->ctype_parameters as $i => $v)
                                if(strpos($i , 'name') !== false)
                                    $name .= urldecode(str_ireplace(array('ISO-8859-1','UTF-8','US-ASCII'),'',$v));
                        ////////////////////////////////////////////////////////////////////////////////////

                        if($name && (strlen($name) - (strrpos($name, '.')+1) === 3 ))
                            $ext = strtolower(substr ( $name , (strrpos($name, '.')+1) ));
                        else if(strlen($structure->ctype_secondary) === 3)
                            $ext = strtolower($structure->ctype_secondary);

                        if(!$name && strtolower($structure->ctype_primary) == 'image') $name = 'Embedded-Image.'.$ext;

                        $ctype = strtolower($part->ctype_primary.'/'.$part->ctype_secondary);

                        if(strtolower($part->ctype_primary) == 'image' ||  ($ctype == 'application/octet-stream' && ($ext == 'png' || $ext == 'jpg' || $ext == 'gif' || $ext == 'bmp' || $ext == 'tif')))
                        {
                            $definition['pid'] = $pIndex.'.'.$index;
                            $definition['name'] = addslashes($name);
                            $definition['type'] = $ctype;
                            $definition['encoding'] = isset($part->headers['content-transfer-encoding']) ? $part->headers['content-transfer-encoding'] : 'base64';
                            $definition['cid'] = isset($part->headers['content-id']) ? $part->headers['content-id'] : '';
                            array_push($images, $definition);
                        }
                    }
                }
            }
    }

}
