<?php

namespace Expresso\ImapBundle\Service;

class ImapService
{
    private $config = array();
    private $mbox;
    private $mboxFolder;

    public function __construct(array $config , $securityContext )
    {
        $this->config = $config;
        $this->config['options'] = ($config['TLSEncryption']) ? '/tls/novalidate-cert' : '/notls/novalidate-cert' ;

        $token = $securityContext->getToken();
        $this->config['username'] = $token->getAttribute('username');
        $this->config['userpass'] = $token->getAttribute('plainPassword');
     }

    public function openMailbox( $folder = 'INBOX' )
    {
        $newFolder = mb_convert_encoding( str_replace('/' ,  $this->config['delimiter'] , $folder ) , 'UTF7-IMAP' , 'UTF-8');

        if($newFolder ===  $this->mboxFolder && is_resource( $this->mbox ))
            return $this->mbox;

        $this->mboxFolder =  $newFolder;
        $url = '{'.$this->config['host'].":".$this->config['port'].$this->config['options'].'}'.$this->mboxFolder;

        if (is_resource($this->mbox))
            imap_reopen($this->mbox, $url );
        else
            $this->mbox = imap_open( $url , $this->config['username'] , $this->config['userpass'] );

        return $this->mbox;
    }

    public function getDefaultFolders()
    {
        return $this->config['folders'];
    }

    /**
     * @license    http://www.gnu.org/copyleft/gpl.html GPL
     * @author     Consórcio Expresso Livre - 4Linux (www.4linux.com.br) e Prognus Software Livre (www.prognus.com.br)
     * @sponsor    Caixa Econômica Federal
     * @author     Cristiano Corrêa Schmidt
     * @param      string $string string no formato mime RFC2047
     * @return     string
     * @access     public
     */
    public function decodeMimeString( $string )
    {
        $string =  preg_replace('/\?\=(\s)*\=\?/', '?==?', $string);
        return preg_replace_callback( '/\=\?([^\?]*)\?([qb])\?([^\?]*)\?=/i' ,array( 'this' , 'decodeMimeStringCallback'), $string);
    }

    /**
     * @license    http://www.gnu.org/copyleft/gpl.html GPL
     * @author     Consórcio Expresso Livre - 4Linux (www.4linux.com.br) e Prognus Software Livre (www.prognus.com.br)
     * @sponsor    Caixa Econômica Federal
     * @author     Cristiano Corrêa Schmidt
     * @param $matches
     * @return string
     * @access     public
     */
    private function decodeMimeStringCallback( $matches )
    {
        $str = (strtolower($matches[2]) == 'q') ?  quoted_printable_decode(str_replace('_','=20',$matches[3])) : base64_decode( $matches[3]) ;
        return ( strtoupper($matches[1]) == 'UTF-8' ) ? mb_convert_encoding(  $str , 'ISO-8859-1' , 'UTF-8') : $str;
    }

    public function getMailBoxes ( $pattern  = '*')
    {
        return imap_getmailboxes( $this->mbox , '{'.$this->config['host'].":".$this->config['port'].$this->config['options'].'}'.$this->mboxFolder , $pattern );
    }


    public function status ( $folder  = 'INBOX' , $options = SA_ALL)
    {
        return imap_status( $this->mbox , '{'.$this->config['host'].":".$this->config['port'].$this->config['options'].'}'.$folder , $options );
    }

    public function sort ( $criteria = SORTDATE , $reverse = 0 , $options = SE_UID , $searchCriteria = NULL , $charset = 'UTF-8')
    {
        return imap_sort( $this->mbox , $criteria , $reverse , $options , $searchCriteria , $charset);
    }



}
