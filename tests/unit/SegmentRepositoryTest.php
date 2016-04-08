<?php

namespace Test\unit;

use Audiens\AppnexusClient\Auth;
use Audiens\AppnexusClient\entity\Segment;
use Audiens\AppnexusClient\repository\SegmentRepository;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Prophecy\Argument;
use Test\TestCase;

/**
 * Class SegmentRepositoryTest
 */
class SegmentRepositoryTest extends TestCase
{

    /**
     * @test
     */
    public function add_will_create_a_new_segment_and_return_a_repository_response()
    {

        $client = $this->prophesize(Auth::class);

        $repository = new SegmentRepository($client->reveal());

        $segment = new Segment();
        $segment->setName('Test segment');

        $fakeResponse = $this->getFakeResponse($this->getSingleSegment());

        $payload = [
            'segment' => $segment->toArray(),
        ];

        $client->request('POST', Argument::any(), ['body' => json_encode($payload)])
               ->willReturn($fakeResponse)
               ->shouldBeCalled();

        $repositoryResponse = $repository->add($segment);

        $this->assertTrue($repositoryResponse->isSuccessful());
        $this->assertEquals(5012, $segment->getId());

    }

    /**
     * @test
     */
    public function update_will_edit_an_existing_segment()
    {

        $client = $this->prophesize(Auth::class);

        $repository = new SegmentRepository($client->reveal());

        $segment = new Segment();
        $segment->setName('Test segment');
        $segment->setId(123456);

        $responseBody = json_encode(
            [
                'response' => [
                    'status' => 'OK',
                ],
            ]
        );

        $fakeResponse = $this->prophesize(Response::class);
        $stream = $this->prophesize(Stream::class);
        $stream->getContents()->willReturn($responseBody);
        $stream->rewind()->willReturn(null)->shouldBeCalled();
        $fakeResponse->getBody()->willReturn($stream->reveal());

        $payload = [
            'segment' => $segment->toArray(),
        ];

        $client->request('PUT', Argument::any(), ['body' => json_encode($payload)])
               ->willReturn($fakeResponse)
               ->shouldBeCalled();

        $repositoryResponse = $repository->update($segment);

        $this->assertTrue($repositoryResponse->isSuccessful());

    }

    /**
     * @test
     */
    public function remove_will_remove_an_existing_segment()
    {

        $client = $this->prophesize(Auth::class);
        $repository = new SegmentRepository($client->reveal());

        $id = '12346';

        $responseBody = json_encode(
            [
                'response' => [
                    'status' => 'OK',
                ],
            ]
        );

        $fakeResponse = $this->prophesize(Response::class);
        $stream = $this->prophesize(Stream::class);
        $stream->getContents()->willReturn($responseBody);
        $stream->rewind()->willReturn(null)->shouldBeCalled();
        $fakeResponse->getBody()->willReturn($stream->reveal());

        $client->request('DELETE', Argument::containingString($id))
               ->willReturn($fakeResponse)
               ->shouldBeCalled();

        $repositoryResponse = $repository->remove('member_id', $id);

        $this->assertTrue($repositoryResponse->isSuccessful());

    }

    /**
     * @test
     */
    public function find_one_by_id_will_return_a_segment()
    {

        $client = $this->prophesize(Auth::class);
        $repository = new SegmentRepository($client->reveal());

        $id = '5012';

        $fakeResponse = $this->getFakeResponse($this->getSingleSegment());

        $client->request('GET', Argument::containingString($id))
               ->willReturn($fakeResponse)
               ->shouldBeCalled();

        $segment = $repository->findOneById('member_id', $id);

        $this->assertEquals(5012, $segment->getId());

    }

    /**
     * @test
     */
    public function find_all_will_return_an_array_of_segments()
    {

        $client = $this->prophesize(Auth::class);
        $repository = new SegmentRepository($client->reveal());

        $id = '5012';

        $fakeResponse = $this->getFakeResponse($this->getMultipleSegments());

        $client->request('GET', Argument::containingString('start_element=3'))
               ->willReturn($fakeResponse)
               ->shouldBeCalled();

        $segments = $repository->findAll('member_id', 3, 3);

        foreach ($segments as $segment) {
            $this->assertInstanceOf(Segment::class, $segment);
        }

    }
}
