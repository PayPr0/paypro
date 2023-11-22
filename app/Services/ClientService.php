<?php

namespace App\Services;

use App\Models\Client;

class ClientService
{
    public function createClient($data)
    {
        
        return Client::firstOrCreate(
            ['email'=>$data->email, 'phone'=>$data->phone]
            ,[
            'name' => $data->name,
            'address' => $data->address,
            'phone' => $data->phone,
            'email' => $data->email,
        ]);
    }

    public function updateClient(Client $client,$data)
    {
       return $client->update([
                'name' => $data->name,
                'address' => $data->address,
                'phone' => $data->phone,
                'email' => $data->email,
            ]);
    }

    public function deleteClient(Client $client)
    {
        return $client->delete();
    }
}