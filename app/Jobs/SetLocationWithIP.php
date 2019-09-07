<?php

namespace App\Jobs;

use App\Utils;
use Illuminate\Support\Facades\DB;
use GeoIp2\Database\Reader;

use App\History;
use App\Location;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SetLocationWithIP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $history;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(History $history)
    {
        $this->history = $history;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            if (!($this->history->lat && $this->history->lng))
            {
                $reader = new Reader(public_path() . '/GeoIP/GeoLite2-City.mmdb');
                $record = $reader->city($this->history->ip_address);
                if ($record && $record->location) {
                    $this->history->lat = $record->location->latitude;
                    $this->history->lng = $record->location->longitude;

                    $geocode=file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyA0U7X_EOApONDxx7UNdqRIs7o8BDmE_z4&latlng={$this->history->lat},{$this->history->lng}");

                    $geoData= json_decode($geocode);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Utils::trace('Google error!');
                        return null;
                    }

                    $city = '';
                    $state = '';
                    $country = '';
                    $result = null;
                    if(isset($geoData->results[0]))
                    {
                        $result = $geoData->results[0];
                    }

                    if($result)
                    {
                        $nonChina = false;
                        foreach($result->address_components as $addressComponent) {
                            if(in_array('locality', $addressComponent->types) && $nonChina == false) {
                                $city = $addressComponent->long_name;
                            }
                            else if(in_array('administrative_area_level_2', $addressComponent->types))
                            {
                                $nonChina = true;
                                $city = $addressComponent->long_name;
                            }
                            else if(in_array('administrative_area_level_1', $addressComponent->types)) {
                                $state = $addressComponent->long_name;
                            }
                            else if(in_array('country', $addressComponent->types))
                            {
                                $country = $addressComponent->long_name;
                            }
                        }
                    }

                    $current_loc = '';
                    if($city)
                    {
                        $current_loc = $city;
                    }

                    if($state)
                    {
                        if($current_loc)
                        {
                            $current_loc .= ", " . $state;
                        }
                        else
                        {
                            $current_loc .= $state;
                        }
                    }

                    if($country)
                    {
                        if($current_loc)
                        {
                            $current_loc .= ", " . $country;
                        }
                    }

                    if(!$current_loc)
                    {
                        if($record->city->name)
                        {
                            $current_loc .= $record->city->name;
                        }
                        if($record->mostSpecificSubdivision->name)
                        {
                            if($current_loc)
                            {
                                $current_loc .= ", " . $record->mostSpecificSubdivision->name;
                            }
                            else
                            {
                                $current_loc .= $record->mostSpecificSubdivision->name;
                            }
                        }
                        if($record->country->name)
                        {
                            if($current_loc)
                            {
                                $current_loc .= ", " . $record->country->name;
                            }
                        }
                    }

                    if ($current_loc) {
                        $location = DB::table('locations')->where(['location' => $current_loc])->first();
                        if ($location) {
                            $this->history->location_id = $location->id;
                        } else {
                            $location = new Location();
                            $location->location = $current_loc;
                            $location->save();
                            $this->history->location_id = $location->id;
                        }
                    }

                    $this->history->save();
                }
            }

            DB::commit();
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
        }
    }
}
