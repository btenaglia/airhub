<?php

/**
 * Flights controller method
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
use App\Models\Flight;

class FlightController extends BaseController implements GenericControllers
{

    public function all()
    {
        $flightService = $this->getService('Flight');
        $flights = $flightService->allWithRelationsAttrs();

        if ($flights !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $flights);
        } else {
            return $this->jsonResponse('No flights found.', self::HTTP_CODE_OK, []);
        }
    }

    public function futureFlights()
    {
        $flightService = $this->getService('Flight');
        $flights = $flightService->futureFlights();

        if ($flights !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $flights);
        } else {
            return $this->jsonResponse('No future flights found.', self::HTTP_CODE_OK, []);
        }
    }

    public function passedFlights()
    {
        $flightService = $this->getService('Flight');
        $flights = $flightService->passedFlights();

        if ($flights !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $flights);
        } else {
            return $this->jsonResponse('No passed flights found.', self::HTTP_CODE_OK, []);
        }
    }

    public function getAllowedStatus()
    {
        $status = Flight::getAllowedStatus();

        return $this->jsonResponse('', self::HTTP_CODE_OK, $status);
    }

    public function getCreatedStatus()
    {
        $status = Flight::getCreatedStatus();

        return $this->jsonResponse('', self::HTTP_CODE_OK, $status);
    }

    public function create()
    {
        $flightService = $this->getService('Flight');
        $flight = $flightService->create(Input::all());

        if ($flight !== null) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not created model. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function destroy($id)
    {
        $flightService = $this->getService('Flight');
        $success = $flightService->destroy($id);

        if ($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not destroy the flight. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function edit($id)
    {
        $flightService = $this->getService('Flight');
        $success = $flightService->edit($id, Input::all());

        if ($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not update the flight. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function setPlane($id)
    {
        $flightService = $this->getService('Flight');
        $success = $flightService->setPlane($id, Input::all());

        if ($success) {
            return $this->jsonSuccessResponse();
        } else {
            return $this->jsonResponse('Could not update the flight. Try again', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function find($id)
    {
        $flightService = $this->getService('Flight');

        $flight = $flightService->findWithRelationsAttrs($id);

        if ($flight->id == null) {

            return $this->jsonResponse('Not flight found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
        $flightInfo = $flightService->findFlightInfo($flight);
        $array = json_decode(json_encode($flight), true);
        $array = array_merge($array, $flightInfo);

        if ($array !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $array);
        } else {
            return $this->jsonResponse('Not flight found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function approve($id)
    {
        $flightService = $this->getService('Flight');

        $flight = $flightService->approve($id);

        if ($flight !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $flight);
        } else {
            return $this->jsonResponse('Not flight found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function test()
    {
        $flightService = $this->getService('Flight');
        $id = 3;
        $flight = $flightService->test($id);

        if ($flight !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $flight);
        } else {
            return $this->jsonResponse('Not flight found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }

    public function cancel($id)
    {
        $flightService = $this->getService('Flight');

        $flight = $flightService->cancel($id);

        if ($flight !== null) {
            return $this->jsonResponse('', self::HTTP_CODE_OK, $flight);
        } else {
            return $this->jsonResponse('Not flight found.', self::HTTP_CODE_SERVER_ERROR, []);
        }
    }
}
