<?php

namespace MeetMatt\OpenApiSpecCoverage\Test\Suite\Integration\Specification;

use Codeception\Test\Unit;
use MeetMatt\OpenApiSpecCoverage\Specification\Factory;

class FactoryTest extends Unit
{
//    public function testFromFile()
//    {
//        $specification = Factory::fromFile(codecept_data_dir('petstore.yaml'));
//
//        $pets = $specification->findPath('/pets');
//        $pet  = $specification->findPath('/pets/{id}');
//
//        $getPets   = $pets->findOperation('get');
//        $createPet = $pets->findOperation('post');
//        $getPet    = $pet->findOperation('get');
//        $deletePet = $pet->findOperation('delete');
//
//        $getPetsTagsQueryParameter  = $getPets->findQueryParameter('tags');
//        $getPetsLimitQueryParameter = $getPets->findQueryParameter('limit');
//        $getPetsPathParameters      = $getPets->getPathParameters();
//        $getPetsMethod              = $getPets->getHttpMethod();
//        $getPetsRequestBodies       = $getPets->getRequestBodies();
//        $getPets->findResponse('200');
//        // TODO: 200responseBodyProperties: <empty>
//        $getPets->findResponse('default');
//        // TODO: defaultResponseBodyProperties: code, message
//
//        $createPetQueryParameters = $createPet->getQueryParameters();
//        $createPetPathParameters  = $createPet->getPathParameters();
//        $createPetMethod          = $createPet->getHttpMethod();
//        $createPet->findRequestBody('application/json');
//        $createPet->findResponse('200');
//        $createPet->findResponse('default');
//        // TODO: defaultResponseBodyProperties: code, message
//
//        $getPetQueryParameters  = $getPet->getQueryParameters();
//        $getPetIdPathParameter = $getPet->findPathParameter('id');
//        $getPetMethod          = $getPet->getHttpMethod();
//        $getPetRequestBodies   = $getPet->getRequestBodies();
//        $getPet->findResponse('200');
//        $getPet->findResponse('404');
//
//        $deletePetQueryParameters = $deletePet->getQueryParameters();
//        $deletePetIdPathParameter = $deletePet->findPathParameter('id');
//        $deletePetMethod          = $deletePet->getHttpMethod();
//        $deletePet->findRequestBody('application/json');
//        $deletePetResponses       = $deletePet->getResponses();
//        $deletePet->findResponse('204');
//        $deletePet->findResponse('default');
//
//        $this->assertNotNull($getPetsTagsQueryParameter);
//        $this->assertNotNull($getPetsLimitQueryParameter);
//        $this->assertEmpty($getPetsPathParameters);
//        $this->assertEquals('get', $getPetsMethod);
//        $this->assertEmpty($getPetsRequestBodies);
//        $this->assertNotEmpty($getPetsResponses);
//        $this->assertEmpty($createPetQueryParameters);
//        $this->assertEmpty($createPetPathParameters);
//        $this->assertEquals('post',$createPetMethod);
//        $this->assertNotEmpty($createPetRequestBodies);
//        $this->assertNotEmpty($createPetResponses);
//        $this->assertEmpty($getPetQueryParameters);
//        $this->assertNotEmpty($getPetIdPathParameter);
//        $this->assertEquals('get', $getPetMethod);
//        $this->assertEmpty($getPetRequestBodies);
//        $this->assertNotEmpty($getPetResponses);
//        $this->assertEmpty($deletePetQueryParameters);
//        $this->assertNotEmpty($deletePetIdPathParameter);
//        $this->assertEquals('delete', $deletePetMethod);
//        $this->assertEmpty($deletePetRequestBodies);
//        $this->assertNotEmpty($deletePetResponses);
//
//
//
//        $this->assertTrue(true);
//    }

    public function testParameters(): void
    {
        $specification = Factory::fromFile(codecept_data_dir('petstore.yaml'));

        codecept_debug($specification);
    }
}