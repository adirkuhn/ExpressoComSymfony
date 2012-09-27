<?php

namespace Expresso\LdapBundle\User;

use Symfony\Component\Security\Core\User\UserInterface;

class LdapUser implements UserInterface, \Serializable
{
    protected $roles,$attributes;

    public function getRoles()
    {
        return $this->roles;
    }

    public function getUserName()
    {
        return $this->attributes['uid'];
    }

    public function getPassword()
    {
        return substr( $this->attributes['userpassword'] , 5); //Remove o prefixo {md5}
    }

    public function getSalt()
    {
        return null;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null ; 
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function eraseCredentials()
    {
        return null; //With ldap No credentials with stored ; Maybe forgotten the roles
    }

    public function serialize()
    {
        return serialize(array($this->roles, $this->attributes));
    }

    public function unserialize($serialized)
    {
        list($this->roles, $this->attributes) = unserialize($serialized);
    }
}
