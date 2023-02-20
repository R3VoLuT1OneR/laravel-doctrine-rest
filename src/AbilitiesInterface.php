<?php

namespace Pz\LaravelDoctrine\JsonApi;

interface AbilitiesInterface
{
    const SHOW_RESOURCE = 'show';
    const SHOW_RELATIONSHIPS = 'showRelationships';

    const LIST_RESOURCES = 'list';
    const LIST_RELATIONSHIPS = 'listRelationships';

    const CREATE_RESOURCE = 'create';
    const CREATE_RELATIONSHIPS = 'createRelationships';

    const UPDATE_RESOURCE = 'update';
    const UPDATE_RELATIONSHIPS = 'updateRelationships';

    const REMOVE_RESOURCE = 'remove';
    const REMOVE_RELATIONSHIPS = 'removeRelationships';
}
