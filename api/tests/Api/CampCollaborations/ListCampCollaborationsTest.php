<?php

namespace App\Tests\Api\CampCollaborations;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ListCampCollaborationsTest extends ECampApiTestCase {
    public function testListCampCollaborationsIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/camp_collaborations');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testListCampCollaborationsIsAllowedForCollaboratorButFiltered() {
        // precondition: There is a camp collaboration that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['campCollaboration1campUnrelated']);

        $response = static::createClientWithCredentials()->request('GET', '/camp_collaborations');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 7,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('campCollaboration1manager')],
            ['href' => $this->getIriFor('campCollaboration2member')],
            ['href' => $this->getIriFor('campCollaboration3guest')],
            ['href' => $this->getIriFor('campCollaboration4invited')],
            ['href' => $this->getIriFor('campCollaboration5inactive')],
            ['href' => $this->getIriFor('campCollaboration1camp2')],
            ['href' => $this->getIriFor('campCollaboration1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCampCollaborationsFilteredByCampIsAllowedForCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials()->request('GET', '/camp_collaborations?camp=/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 5,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('campCollaboration1manager')],
            ['href' => $this->getIriFor('campCollaboration2member')],
            ['href' => $this->getIriFor('campCollaboration3guest')],
            ['href' => $this->getIriFor('campCollaboration4invited')],
            ['href' => $this->getIriFor('campCollaboration5inactive')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListCampCollaborationsFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/camp_collaborations?camp=/camps/'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertStringNotContainsString($this->getIriFor('campCollaboration1manager'), $response->getContent());
        $this->assertStringNotContainsString($this->getIriFor('campCollaboration2member'), $response->getContent());
        $this->assertStringNotContainsString($this->getIriFor('campCollaboration3guest'), $response->getContent());
        $this->assertStringNotContainsString($this->getIriFor('campCollaboration4invited'), $response->getContent());
        $this->assertStringNotContainsString($this->getIriFor('campCollaboration5inactive'), $response->getContent());
    }

    public function testListCampCollaborationsFilteredByCampIsDeniedForInactiveCollaborator() {
        $camp = static::$fixtures['camp1'];
        $response = static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('GET', '/camp_collaborations?camp=/camps/'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertStringNotContainsString($this->getIriFor('campCollaboration1manager'), $response->getContent());
        $this->assertStringNotContainsString($this->getIriFor('campCollaboration2member'), $response->getContent());
        $this->assertStringNotContainsString($this->getIriFor('campCollaboration3guest'), $response->getContent());
        $this->assertStringNotContainsString($this->getIriFor('campCollaboration4invited'), $response->getContent());
        $this->assertStringNotContainsString($this->getIriFor('campCollaboration5inactive'), $response->getContent());
    }

    public function testListCampCollaborationsFilteredByCampPrototypeIsAllowedForUnrelatedUser() {
        $camp = static::$fixtures['campPrototype'];
        $response = static::createClientWithCredentials()->request('GET', '/camp_collaborations?camp=/camps/'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 1,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('campCollaboration1campPrototype')],
        ], $response->toArray()['_links']['items']);
    }
}
