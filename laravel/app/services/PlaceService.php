<?php
namespace App\Services;

use App\Models\Place;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

/**
 * TODO Comment of component here!
 *
 * @author <a href="mailto:emiliogenesio@gmail.com">Emilio Genesio</a>
 */
class PlaceService extends BaseService implements GenericServices
{
    private $url = "https://flightxml.flightaware.com/json/FlightXML2/AirportInfo";
    private $password = "074cd858e9b54ed5f2a7ae22f4261d6852fde661";
    private $usr = "mackoss";

    public function all()
    {
        return Place::findAllActives('App\Models\Place');
    }

    public function create($input)
    {
        $place = new Place();
        $place->setName($input['name']);
        $code = $input['short_name'];
        $place->setShortName($input['short_name']);
        $client = new Client();
        $response = $client->request('GET',
            $this->url . '?airportCode=' . $code,
            [
                'auth' => [
                    $this->usr,
                    $this->password,
                ],
            ]);
        $AirportInfo = $response->getBody();
        $ai = json_decode($AirportInfo,true);
        $place->latitude = $ai["AirportInfoResult"]["latitude"];
        $place->longitude = $ai["AirportInfoResult"]["longitude"];
        $success = $place->save();
       
        if ($success) {
            return $response;
        } else {
            return null;
        }
    }

    public function destroy($id)
    {

        $inflight = DB::table('flights')->where('origin', $id)->first();
        $inflight2 = DB::table('flights')->where('destination', $id)->first();
        //$inflight = Place::getOriginFlight();
        //$inflight2 = Place::getDestinationFlight();

        if (!($inflight || $inflight2)) {

            $place = $this->find($id);
            $place->setActive(false);

            return $place->update();

        } else {

            return false;

        }
    }

    public function edit($id, $input)
    {
        $place = $this->find($id);
        $place->setName($input['name']);
        $place->setShortName($input['short_name']);
        $client = new Client();
        $response = $client->request('GET',
            $this->url . '?airportCode=' . $input['short_name'],
            [
                'auth' => [
                    $this->usr,
                    $this->password,
                ],
            ]);
        $AirportInfo = $response->getBody();
        $ai = json_decode($AirportInfo,true);
        $place->latitude = $ai["AirportInfoResult"]["latitude"];
        $place->longitude = $ai["AirportInfoResult"]["longitude"];
        $success = $place->update();

        if ($success) {
            return $place;
        } else {
            return null;
        }
    }
    public function findByName($query) {
        return Place::where('name','=',$query)->first();
    }
    public function find($id)
    {
        return Place::find($id);
    }
}
