var oException = function ()
{

}

oException.prototype.error = function ( func , params )
{
    //TODO: Tratar erros
    console.log(' Erro: Function  ' + func);
}
oException.prototype.log = function ( func , log )
{
    //TODO: Tratar erros
    console.log(' LOG: ' + log +  ' | Function  ' + func);
}
oException.prototype.logException = function ( func , e , params )
{
    //TODO: Tratar erros
    console.log(' Erro : '+ e.message +' | Function  ' + func );
}

var Exception = new oException();