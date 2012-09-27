var oAPI = function ( URL , assectURL )
{
    this.URL = URL;
    this.assectURL = assectURL;

}

oAPI.prototype.render = function ( template , data , append  )
{
    try
    {
        if(append)
            return $(append).append( new EJS({url: this.URL + '/template/EJS/' + template + '.ejs'}).render( { data: data } ) );
        else
            return new EJS({url: this.URL + '/template/EJS/' + template + '.ejs'}).render( { data: data } );
    }
    catch(e)
    {
        Exeption.log( 'API.render'  , arguments )
        return false;
    }

}

oAPI.prototype.renderRestAppend = function ( template , restURL , append )
{
    var sucessREAD = function ( data , headers , xhr)
    {
        $(append).append( API.render( template  , data ) );
    }

    API.restGET( restURL ,  sucessREAD , function ()  {  Exeption.log( 'API.renderRestAppend' , arguments ) } );
}

oAPI.prototype.renderAppend = function (template , data , append)
{

}

oAPI.prototype.restGET = function ( url , sucess , error )
{
    $.read( this.URL + '/rest/' + url , '' , sucess , error ?  error : function ()  {  Exeption.log( 'API.restGET' , arguments ) } );
}

oAPI.prototype.restPUT = function ( url , data , sucess , error )
{
    $.update( this.URL + '/rest/' + url , data , sucess , error ?  error : function ()  {  Exeption.log( 'API.restPUT' , arguments ) } );
}

oAPI.prototype.restDELETE = function ( url , sucess , error )
{
    $.destroy( this.URL + '/rest/' + url , '' , sucess , error ?  error : function ()  {  Exeption.log( 'API.restDELETE' , arguments ) } );
}

oAPI.prototype.restPOST = function (url , data , sucess , error)
{
    $.create( this.URL + '/rest/' + url , data , sucess , error ?  error : function ()  {  Exeption.log( 'API.restPOST' , arguments ) } );

}

