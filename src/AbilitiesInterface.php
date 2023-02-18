<?php

namespace Pz\LaravelDoctrine\JsonApi;

interface AbilitiesInterface
{
    const SHOW_RESOURCE = 'show';
    const SHOW_RELATED_RESOURCE = 'showRelated';

    const LIST_RESOURCES = 'list';
    const LIST_RELATED_RESOURCES = 'listRelated';

    const CREATE_RESOURCE = 'create';
    const CREATE_RELATED_RELATIONSHIPS = 'createRelationships';

    const UPDATE_RESOURCE = 'update';
    const UPDATE_RELATED_RELATIONSHIPS = 'updateRelationships';

    const REMOVE_RESOURCE = 'remove';
    const REMOVE_RELATED_RELATIONSHIPS = 'removeRelationships';
}