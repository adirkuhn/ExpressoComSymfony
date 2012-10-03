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
        $newFolder = mb_convert_encoding( str_replace( '.' ,  $this->config['delimiter'] , $folder ) , 'UTF7-IMAP' , 'UTF-8');

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
        return preg_replace_callback( '/\=\?([^\?]*)\?([qb])\?([^\?]*)\?=/i' ,array( 'self' , 'decodeMimeStringCallback'), $string);
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
        return ( strtoupper($matches[1]) == 'ISO-8859-1' ) ? mb_convert_encoding(  $str , 'UTF-8' , 'ISO-8859-1') : $str;
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


    public function headerInfo ( $message , $SE_UID = false  )
    {
        return imap_headerinfo( $this->mbox , $SE_UID ? imap_msgno( $this->mbox , $message ) : $message );
    }

    function formatMailObject( $obj )
    {
        $return = array();
        $return['mail'] = isset($obj->mailbox) ?  $this->decodeMimeString( $obj->mailbox  ) : '' ;
        $return['mail'] .= (isset( $obj->host) && $obj->host != 'unspecified-domain' && $obj->host != '.SYNTAX-ERROR.'  ) ? '@'. $obj->host : '';
        $return['name'] = ( isset( $obj->personal ) && trim($obj->personal) !== '' ) ? $this->decodeMimeString($obj->personal) : '' ;
        return $return;
    }

    function formatMailObjects( Array $objs)
    {
        $return = array();

        foreach($objs as $obj)
            $return[] = $this->formatMailObject($obj);

        return $return;
    }

    function body( $message , $options = FT_PEEK )
    {
       return imap_body( $this->mbox, $message  , $options  );
    }

    function fetchHeader( $message , $options = 0 )
    {
        return imap_fetchheader( $this->mbox, $message  , $options  );
    }

    public function createFolder($folder){
        $folder = mb_convert_encoding( str_replace( '.' ,  $this->config['delimiter'] , $folder ) , 'UTF7-IMAP' , 'UTF-8');
        return imap_createmailbox($this->mbox, '{'.$this->config['host'].":".$this->config['port'].$this->config['options'].'}'.$folder);
    }

    public function editFolder($folder, $newFolder){
        $folder = mb_convert_encoding( str_replace( '.' ,  $this->config['delimiter'] , $folder ) , 'UTF7-IMAP' , 'UTF-8');
        $newFolder = mb_convert_encoding( str_replace( '.' ,  $this->config['delimiter'] , $newFolder ) , 'UTF7-IMAP' , 'UTF-8');
        $mailboxPath =  '{'.$this->config['host'].":".$this->config['port'].$this->config['options'].'}';
        return imap_renamemailbox($this->mbox, $mailboxPath.$folder, $mailboxPath.$newFolder);
    }

    public function deleteFolder($folder){
        $folder = mb_convert_encoding( str_replace( '.' ,  $this->config['delimiter'] , $folder ) , 'UTF7-IMAP' , 'UTF-8');
        return imap_deletemailbox($this->mbox, '{'.$this->config['host'].":".$this->config['port'].$this->config['options'].'}'.$folder);
    }
}
