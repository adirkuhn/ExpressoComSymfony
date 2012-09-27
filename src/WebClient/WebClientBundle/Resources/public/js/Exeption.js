var oExeption = function ()
{

}

oExeption.prototype.log = function ( func , params )
{
    //TODO: Tratar erros
    console.log(' Erro: Function  ' + func);
}

var Exeption = new oExeption();