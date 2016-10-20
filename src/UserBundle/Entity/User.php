<?php
// src/UserBundle/Entity/User.php

//php app/console fos:user:create
// user user@example.com userp@ssword
// testuser test@example.com p@ssword
// adminuser admin@example.com @adminp@ssword
//php bin/console fos:user:promote adminuser --super
//php bin/console fos:user:promote testuser ROLE_ADMIN

namespace UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;




    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}