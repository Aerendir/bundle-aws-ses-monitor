<?php
namespace Shivas\BouncerBundle\Model;

interface BounceRepositoryInterface
{
    /**
     * @param $email
     * @return Bounce|null
     */
    public function findBounceByEmail($email);

    /**
     * @param Bounce $bounce
     * @return mixed
     */
    public function save(Bounce $bounce);
}
