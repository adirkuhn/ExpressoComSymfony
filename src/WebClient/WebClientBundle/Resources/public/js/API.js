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
        Exception.logException( 'API.render'  , e ,  arguments )
        return false;
    }

}

oAPI.prototype.renderRestAppend = function ( template , restURL , append )
{
    var sucessREAD = function ( data , headers , xhr)
    {
        API.render( template  , data, append );
    }

    API.restGET( restURL ,  sucessREAD , function ()  {  Exception.log( 'API.renderRestAppend' , arguments ) } );
}

oAPI.prototype.loadModule = function ( module )
{
    if(Module.destroy())
    {
        $.getScript(this.assectURL+'/js/module/'+module+'.js')
            .done(function(script, textStatus) {
               Module.load();
            })
            .fail(function(jqxhr, settings, exception) {
                Exception.error( 'API.loadModule' , arguments );
            });
    }
    else
    {
        Exception.log( 'API.loadModule' , 'Falha ao destruir modulo');
    }
}

oAPI.prototype.restGET = function ( url , sucess , error )
{
    $.read( this.URL + '/rest/' + url , '' , sucess , error ?  error : function ()  {  Exception.log( 'API.restGET' , arguments ) } );
}

oAPI.prototype.restPUT = function ( url , data , sucess , error )
{
    $.update( this.URL + '/rest/' + url , data , sucess , error ?  error : function ()  {  Exception.log( 'API.restPUT' , arguments ) } );
}

oAPI.prototype.restDELETE = function ( url , sucess , error )
{
    $.destroy( this.URL + '/rest/' + url , '' , sucess , error ?  error : function ()  {  Exception.log( 'API.restDELETE' , arguments ) } );
}

oAPI.prototype.restPOST = function (url , data , sucess , error)
{
    $.create( this.URL + '/rest/' + url , data , sucess , error ?  error : function ()  {  Exception.log( 'API.restPOST' , arguments ) } );

}

