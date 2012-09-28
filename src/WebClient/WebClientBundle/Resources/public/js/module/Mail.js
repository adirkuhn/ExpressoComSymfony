var oMail = function()
{

}

oMail.prototype.load = function()
{
    $('#main').empty();
    $('#main').append('Modulo Mail !!!!!!!!!!!!!!!!!!!!!!')
}

oMail.prototype.destroy = function()
{
    $('#main').empty();
    return true;
}

Module = new oMail();
