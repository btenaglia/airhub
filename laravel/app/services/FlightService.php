<?php
namespace App\Services;

use App\Models\Flight;
use GuzzleHttp\Client;

/**
 * All the flights logic here.
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class FlightService extends BaseService implements GenericServices
{
    private $url = "https://flightxml.flightaware.com/json/FlightXML2";
    private $password = "074cd858e9b54ed5f2a7ae22f4261d6852fde661";
    private $usr = "mackoss";

    public function all()
    {
        return Flight::findAllActives('App\Models\Flight')->toArray();
    }

    public function allWithRelationsAttrs()
    {
        return Flight::allWithRelationsAttrs();
    }

    public function futureFlights()
    {
        return Flight::futureFlights();
    }

    public function passedFlights()
    {
        return Flight::passedFlights();
    }

    public function create($input)
    {

        $flight = new Flight();
        $coords = ["origin" => explode(";", $input["origin_coords"]),
            "destination" => explode(";", $input["destination_coords"])];

        $client = new Client();
        $flight = $this->createFlight($flight, $input);

        //distance
        $response = $client->request('GET',
            $this->url . '/LatLongsToDistance?lat1=' . $coords["origin"][0] . '&lon1=' . $coords["origin"][1] .
            '&lat2=' . $coords["destination"][0] . '&lon2=' . $coords["destination"][1],
            [
                'auth' => [
                    $this->usr,
                    $this->password,
                ],
            ]);
        $distance = json_decode($response->getBody(), true);

        $flight->distance = $distance["LatLongsToDistanceResult"];
        //route
        $response = $client->request('GET',
            $this->url . '/RoutesBetweenAirports?origin=' . $input["origin_id"] . '&destination=' . $input["destination_id"],
            [
                'auth' => [
                    $this->usr,
                    $this->password,
                ],
            ]);
        $route = json_decode($response->getBody(), true);
        $r = $route["RoutesBetweenAirportsResult"]["data"];

        $flight->route = count($r) > 0 ? $r[0]["route"] : "";

        $success = $flight->save();

        if ($success) {
            return $flight;
        } else {
            return null;
        }
    }

    /**
     * Create a flight (called only from create and edit functions)
     * @param type $flight new or fetched from the DB
     * @param type $input $_REQUEST
     * @return $flight
     */
    private function createFlight($flight, $input)
    {

        $flight->setDepartureDate($input['departure_date']);
        $flight->setStatus($input['status']);
        if ($this->checkIfFieldExists($input, 'paramvalueamount')) {
            $flight->setAmount($input['paramvalueamount']);
        }

        if ($this->checkIfFieldExists($input, 'departure_time')) {

            $flight->setDepartureTime($input['departure_time']);
            $flight->setDepartureMinTime(null);
            $flight->setDepartureMaxTime(null);

        } else if ($this->checkIfFieldExists($input, 'departure_min_time') && $this->checkIfFieldExists($input, 'departure_max_time')) {

            $flight->setDepartureTime(null);
            $flight->setDepartureMinTime($input['departure_min_time']);
            $flight->setDepartureMaxTime($input['departure_max_time']);

        } else {
            return null;
        }

        //check if the plane was settled
        if ($this->checkIfFieldExists($input, 'plane_id')) {
            $planeService = $this->getService('Plane');
            $plane = $planeService->find($input['plane_id']);
            $flight->setPlane($plane);
        }

        //assign the origin and destination place
        $placeService = $this->getService('Place');
        $origin = $placeService->find($input['origin']);
        $destination = $placeService->find($input['destination']);
        $flight->setOrigin($origin);
        $flight->setDestination($destination);

        //set the current user (fetched with the token request)
        $user = $this->getCurrentUser();
        $flight->setUser($user);

        return $flight;
    }

    public function destroy($id)
    {
        $flight = $this->find($id);
        $flight->setActive(false);

        return $flight->update();
    }

    public function edit($id, $input)
    {
        $flight = $this->find($id);
        $flight = $this->createFlight($flight, $input);

        $success = $flight->update();

        if ($success) {
            return $flight;
        } else {
            return null;
        }
    }

    public function setPlane($id, $input)
    {
        $planeService = $this->getService('Plane');
        $plane = $planeService->find($input['plane_id']);

        $flight = $this->find($id);
        $flight->setPlane($plane);

        $success = $flight->update();

        if ($success) {
            return $flight;
        } else {
            return null;
        }
    }

    public function find($id)
    {
        return Flight::find($id);
    }

    public function approve($id)
    {
        $flight = $this->find($id);
        $flight->setStatus('scheduled');

        $success = $flight->update();

        if ($success) {

            $mailService = $this->getService('Mail');
            $userService = $this->getService('Account');

            $pushService = $this->getService('Push');

            $origin = $flight->getOrigin->name . ' (' . $flight->getOrigin->name . ')';
            $destination = $flight->getDestination->name . ' (' . $flight->getDestination->name . ')';
            $time = $flight->departure_date . ' ' . ($flight->departure_time == '' || $flight->departure_time == null ? $flight->departure_min_time . ' - ' . $flight->departure_max_time : $flight->departure_time);
            $time_arrive = '';
            $plane = $flight->getPlane->name . ' - ' . $flight->getPlane->type . ' (' . $flight->getPlane->identifier . ')';

            $users = $this->getUsersOfFlight($flight->getId());
            foreach ($users as $userRaw) {

                $user = $userService->find($userRaw->id);
                //$mailService->sendApprovedFlight($flight, $user);

                //Notify
                //Push

                $book = $user->getBookByFlight($userRaw->id, $flight->id);

                $data = array('time' => $time,
                    'time_arrive' => $time_arrive,
                    'route' => 'from: ' . $origin . ' to: ' . $destination,
                    'price' => $book->amount);
                $iddevice = $user->getIdFirebase();
                if (!empty($iddevice)) {
                    $pushService->sendtoDeviceFCM('Notify', 'Flight has been approved.', $iddevice, $data);
                }

                //Email

            }

            return $flight;
        } else {
            return null;
        }
    }

    public function test($id)
    {
        $flight = $this->find($id);

        if (true) {
            $userService = $this->getService('Account');

            $origin = $flight->getOrigin->name . ' (' . $flight->getOrigin->name . ')';
            $destination = $flight->getDestination->name . ' (' . $flight->getDestination->name . ')';
            $time = $flight->departure_date . ' ' . ($flight->departure_time == '' || $flight->departure_time == null ? $flight->departure_min_time . ' - ' . $flight->departure_max_time : $flight->departure_time);
            $time_arrive = '';
            $plane = $flight->getPlane->name . ' - ' . $flight->getPlane->type . ' (' . $flight->getPlane->identifier . ')';
            $users = $this->getUsersOfFlight($flight->getId());

            $ResultsG = array();

            foreach ($users as $userRaw) {
                $user = $userService->find($userRaw->id);
                //$mailService->sendApprovedFlight($flight, $user);

                //Notify
                //Push

                $book = $user->getBookByFlight($userRaw->id, $flight->id);

                $data = array('user' => $userRaw->id,
                    'time' => $time,
                    'time_arrive' => $time_arrive,
                    'route' => 'from: ' . $origin . ' to: ' . $destination,
                    'price' => $book);
                $iddevice = $user->getIdFirebase();
                $ResultsG[] = $data;
                //Email

            }

            return $ResultsG;
        } else {
            return null;
        }
    }

    public function cancel($id)
    {
        $flight = $this->find($id);
        $flight->setStatus('proposed');

        $success = $flight->update();

        if ($success) {

            $mailService = $this->getService('Mail');
            $userService = $this->getService('Account');

            $users = $this->getUsersOfFlight($flight->getId());
            foreach ($users as $userRaw) {

                $user = $userService->find($userRaw->id);
                //$mailService->sendApprovedFlight($flight, $user);
            }

            return $flight;
        } else {
            return null;
        }
    }

    private function getUsersOfFlight($id)
    {
        return Flight::userByFlight($id);
    }

    public function findWithRelationsAttrs($id)
    {
        return Flight::findWithRelationsAttrs($id);
    }
    public function findFlightInfo($flight)
    {
        $plane_id = $flight->plane_ident;
        $client = new Client();
        $response = $client->request('GET',
            $this->url . '/InFlightInfo?ident=' . $plane_id,
            [
                'auth' => [
                    $this->usr,
                    $this->password,
                ],
            ]);
        $flightInfo = $response->getBody();
        $fi = json_decode($flightInfo, true);
        $currentFlight = ["altitude" => $fi["InFlightInfoResult"]["altitude"],
            "speed" => $fi["InFlightInfoResult"]["groundspeed"]];

        return $currentFlight;
    }
}
