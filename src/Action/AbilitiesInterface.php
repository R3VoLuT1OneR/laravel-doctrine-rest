<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

interface AbilitiesInterface
{
    const SHOW_RESOURCE = 'show';
    const SHOW_RELATED_RESOURCE = 'showRelated';

    const LIST_RESOURCES = 'list';
    const LIST_RELATED_RESOURCES = 'listRelated';
}