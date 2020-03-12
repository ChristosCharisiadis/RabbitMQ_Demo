<?php


namespace App\Utils;


use Doctrine\Persistence\ObjectManager;

/**
 * This class contains some static helper methods for communicating with the database.
 * @package App\Utils
 */
class DbUtils
{
    private function __construct() {}

    /**
     * Writes the given object to the database.
     * @param ObjectManager $em
     * @param $object
     */
    public static function writeObject(ObjectManager $em, $object) {
        $em->persist($object);
        $em->flush();
    }

    /**
     * Returns one object from the database based on a specific search field.
     * @param ObjectManager $em
     * @param string $class The class of the object we want to search.
     * @param string $field The name of the field we want to search by.
     * @param string $value The value of the given fields we want to search.
     * @return object|null
     */
    public static function findOneObjectBy(ObjectManager $em, string $class, string $field, string $value) {
        return $em->getRepository($class)->findOneBy([$field => $value]);
    }

    /**
     * Writes an object to the database if it does not exist.
     * Returns the object from the database if it exists, or the newly written object to the database if it doesn't.
     * @param ObjectManager $em
     * @param $object
     * @param $id
     * @return object|null
     */
    public static function writeObjectIfNotExist(ObjectManager $em, $object, $id) {
        $objectFromDB = $em->find(get_class($object), $id);
        if ($objectFromDB === null)
            self::writeObject($em, $object);
        else
            $object = $objectFromDB;

        return $object;
    }
}