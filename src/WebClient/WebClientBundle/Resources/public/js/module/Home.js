var oHome = function()
{

}

oHome.prototype.load = function()
{
    $('#main').empty();
    $('#main').append('Modulo Home !!!!!!!!!!!!!!!!!!!!!!')
}

oHome.prototype.destroy = function()
{
    $('#main').empty();
    return true;
}

Module = new oHome();
