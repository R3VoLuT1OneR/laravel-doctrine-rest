<?php namespace Pz\LaravelDoctrine\JsonApi;

interface ResourceInterface
{
    /**
     * Get fractal resource key.
     * JSON API `type`
     */
    public static function getResourceKey(): string;

    /**
     * JSON API `id`
     */
    public function getId(): null|string|int;
}
