<?php
require_once "database.php";

class JamsModel extends Database
{
    public function getJams()
    {
        return $this->executeQuery("SELECT * FROM Jams");
    }

    public function getJamsByDate($datetime)
    {
        return $this->executeQuery("SELECT * FROM Jams WHERE Valid_From < :datetime AND Valid_To > :datetime2", [":datetime"=>$datetime, ":datetime2"=>$datetime]);
    }

    public function importJamsFromApi()
    {
        $certificate = "C:\wamp64\cacert.pem";

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

        $jsondata = json_decode($data, true);

        # Set old data
        $this->executeQuery("UPDATE Jams SET Valid_To = NOW() WHERE Valid_To IS NULL");

        # Add all traffic jams to database
        foreach ($jsondata["roads"] as $road) {
            foreach ($road["segments"] as $segment) {
                if (array_key_exists("jams", $segment))
                {
                    foreach ($segment["jams"] as $jam)
                    {
                        $this->executeQuery("INSERT INTO Jams(Road, Distance, Delay, Reason, Valid_From)
                        VALUES (:road, :distance, :delay, :reason, NOW())
                        ", [":road"=>$jam["road"],":distance"=>$jam["distance"] ?? null,":delay"=>$jam["delay"] ?? null,":reason"=>$jam["reason"] ?? ""]);
                    }
                }
            }
        }
        
        curl_close($curl);
    }
}