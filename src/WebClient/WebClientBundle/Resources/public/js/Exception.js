var oException = function ()
{

}

oException.prototype.log = function ( func , params )
{
    //TODO: Tratar erros
    console.log(' Erro: Function  ' + func);
}

var Exception = new oException();