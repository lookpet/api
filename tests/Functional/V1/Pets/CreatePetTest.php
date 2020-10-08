<?php

namespace Tests\Functional\V1\Pets;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\DataFixtures\ORM\PetFixture;
use Tests\DataFixtures\ORM\UserFixture;
use Tests\Helpers\AuthenticationHelper;

/**
 * @group functional
 */
class CreatePetTest extends WebTestCase
{
    use FixturesTrait;

    private const CREATE_PET_URL = '/api/v1/pet';
    private const UPDATE_PET_URL = '/api/v1/pet/%s';

    private const TYPE = 'dog';
    private const SLUG = 'super-dog';
    private const NAME = 'Tuz';
    private const BREED = 'Chihuahua';
    private const COLOR = 'Tri-color';
    private const EYE_COLOR = 'brown';
    private const GENDER = 'female';
    private const ABOUT = 'Super dog';
    private const FATHER_NAME = 'Adam';
    private const MOTHER_NAME = 'Eve';
    private const CITY = 'San Francisco';
    private const PLACE_ID = 'super-place-id';
    private const PRICE = '1000$';

    public function testItCreatesPetWithData(): void
    {
        $dateOfbirth = new \DateTimeImmutable('yesterday');
        $client = static::createClient();
        $this->loadFixtures([UserFixture::class]);
        $client = AuthenticationHelper::login($client);
        $client->request(
            Request::METHOD_POST,
            self::CREATE_PET_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode([
                'type' => self::TYPE,
                'name' => self::NAME,
                'slug' => self::SLUG,
                'city' => self::CITY,
                'placeId' => self::PLACE_ID,
                'breed' => self::BREED,
                'fatherName' => self::FATHER_NAME,
                'motherName' => self::MOTHER_NAME,
                'color' => self::COLOR,
                'eyeColor' => self::EYE_COLOR,
                'dateOfBirth' => $dateOfbirth->format('Y-m-d'),
                'about' => self::ABOUT,
                'gender' => self::GENDER,
            ])
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame(self::NAME, $content['name']);
        self::assertSame(self::TYPE, $content['type']);
        self::assertSame(self::CITY, $content['city']);
        self::assertSame(self::PLACE_ID, $content['placeId']);
        self::assertSame(self::BREED, $content['breed']);
        self::assertSame(self::FATHER_NAME, $content['fatherName']);
        self::assertSame(self::MOTHER_NAME, $content['motherName']);
        self::assertSame(self::COLOR, $content['color']);
        self::assertSame(self::EYE_COLOR, $content['eyeColor']);
        self::assertSame(self::ABOUT, $content['about']);
        self::assertSame(self::GENDER, $content['gender']);
        self::assertSame($dateOfbirth->format('Y-m-d'), (new \DateTimeImmutable($content['dateOfBirth']['date']))->format('Y-m-d'));
    }

    public function testItUpdatesPetWithData(): void
    {
        $dateOfbirth = new \DateTimeImmutable('yesterday');
        $client = static::createClient();
        $this->loadFixtures([PetFixture::class, UserFixture::class]);
        $client = AuthenticationHelper::login($client);
        $client->request(
            Request::METHOD_POST,
            sprintf(self::UPDATE_PET_URL, PetFixture::SLUG),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            (string) json_encode([
                'type' => self::TYPE,
                'name' => self::NAME,
                'slug' => self::SLUG,
                'city' => self::CITY,
                'placeId' => self::PLACE_ID,
                'breed' => self::BREED,
                'fatherName' => self::FATHER_NAME,
                'motherName' => self::MOTHER_NAME,
                'color' => self::COLOR,
                'eyeColor' => self::EYE_COLOR,
                'dateOfBirth' => $dateOfbirth->format('Y-m-d'),
                'about' => self::ABOUT,
                'gender' => self::GENDER,
            ])
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame(self::NAME, $content['name']);
        self::assertSame(self::TYPE, $content['type']);
        self::assertSame(self::CITY, $content['city']);
        self::assertSame(self::PLACE_ID, $content['placeId']);
        self::assertSame(self::BREED, $content['breed']);
        self::assertSame(self::FATHER_NAME, $content['fatherName']);
        self::assertSame(self::MOTHER_NAME, $content['motherName']);
        self::assertSame(self::COLOR, $content['color']);
        self::assertSame(self::EYE_COLOR, $content['eyeColor']);
        self::assertSame(self::ABOUT, $content['about']);
        self::assertSame(self::GENDER, $content['gender']);
        self::assertSame($dateOfbirth->format('Y-m-d'), (new \DateTimeImmutable($content['dateOfBirth']['date']))->format('Y-m-d'));
    }
}
