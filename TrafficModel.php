<?php
require_once "database.php";

class TrafficModel extends Database
{
    public function getTraffic()
    {
        return $this->executeQuery("SELECT * FROM Traffic WHERE Valid_To IS NULL");
    }

    public function getTrafficByDate($datetime)
    {
        return $this->executeQuery("SELECT * FROM Traffic WHERE Valid_From < :datetime AND Valid_To > :datetime2", [":datetime"=>$datetime, ":datetime2"=>$datetime]);
    }

    public function importTrafficFromApi()
    {
        $certificate = "C:\Users\Emma\Documents\anwb-api\cacert.pem";

        # Get the json data from the api
        $url = 'https://api.anwb.nl/v2/incidents/desktop?apikey=QYUEE3fEcFD7SGMJ6E7QBCMzdQGqRkAi';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36');
        curl_setopt($curl, CURLOPT_CAINFO, $certificate);
        curl_setopt($curl, CURLOPT_CAPATH, $certificate);

        $data = curl_exec($curl);

        if ($data == false)
        {
            echo 'Curl error: ' . curl_error($curl);
        }

        $jsondata = json_decode($data, true);

        # Set old data
        $this->executeQuery("UPDATE Traffic SET Valid_To = NOW() WHERE Valid_To IS NULL");

        # Add all traffic jams to database
        foreach ($jsondata["roads"] as $road) 
        {
            foreach ($road["segments"] as $segment) 
            {
                if (array_key_exists("jams", $segment))
                {
                    foreach ($segment["jams"] as $jam)
                    {
                        $this->executeQuery("INSERT INTO Traffic(Road, From_Location, To_Location, From_Loc_Lat, From_Loc_Lng, To_Loc_Lat, To_Loc_Lng, Distance, Delay, Reason, Valid_From, TrafficType, Polyline)
                        VALUES (:road, :from, :to, :from_loc_lat, :from_loc_lng, :to_loc_lat, :to_loc_lng, :distance, :delay, :reason, NOW(), 'Jam', :polyline)", 
                        [":road"=>$jam["road"],":from"=>$jam["from"], ":to"=>$jam["to"],":from_loc_lat"=>$jam["fromLoc"]["lat"], 
                        ":from_loc_lng"=>$jam["fromLoc"]["lon"], ":to_loc_lat"=>$jam["toLoc"]["lat"], ":to_loc_lng"=>$jam["toLoc"]["lon"],
                        ":distance"=>$jam["distance"] ?? null,":delay"=>$jam["delay"] ?? null,":reason"=>$jam["reason"] ?? "", ":polyline"=>$jam["polyline"] ?? ""]);
                    }
                }
                if (array_key_exists("roadworks", $segment)) 
                {
                    foreach ($segment["roadworks"] as $roadworks)
                    {
                        $this->executeQuery("INSERT INTO Traffic(Road, From_Location, To_Location, From_Loc_Lat, From_Loc_Lng, To_Loc_Lat, To_Loc_Lng, Distance, Delay, Reason, Valid_From, TrafficType, Polyline)
                        VALUES (:road, :from, :to, :from_loc_lat, :from_loc_lng, :to_loc_lat, :to_loc_lng, :distance, :delay, :reason, NOW(), 'Roadwork', :polyline)", 
                        [":road"=>$roadworks["road"],":from"=>$roadworks["from"], ":to"=>$roadworks["to"], ":from_loc_lat"=>$roadworks["fromLoc"]["lat"], 
                        ":from_loc_lng"=>$roadworks["fromLoc"]["lon"], ":to_loc_lat"=>$roadworks["toLoc"]["lat"], ":to_loc_lng"=>$roadworks["toLoc"]["lon"],
                        ":to"=>$roadworks["to"],":distance"=>$roadworks["distance"] ?? null,":delay"=>$roadworks["delay"] ?? null,":reason"=>$roadworks["reason"] ?? "",":polyline"=>$roadworks["polyline"] ?? ""]);
                    }
                }
            }
        }
        
        curl_close($curl);
    }
}