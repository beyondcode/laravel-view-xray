<?php

namespace BeyondCode\ViewXray\Events;

use Illuminate\Http\Response;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class InjectedXrayBar
{
    use SerializesModels;

    /** @var Response */
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
