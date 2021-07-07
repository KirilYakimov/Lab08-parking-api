# Lab08 parking lot api

Vehicle exiting and geting vechicle current exit price is only available for vehicles in the parking.
All datetime in the databese are saved in utc timezone and for the calculations are adjusted to the timezone of the parking lot.

## Configure

Install dependancies from `composer.json`:

```
$ composer install
```

In order to connect the laravel installation with your database and other stuff copy the `.env.example` file:

```
$ cp .env.example .env
```

You can see the configuration in that file. Laravel will read from that file.

For example the DB connection(MySQL) configuration looks like:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_parking_manager  // utf8_bin
DB_USERNAME=root
DB_PASSWORD=
```

Set app debug to false in .env

```
APP_DEBUG=false
```

After that you can run those Php artisan commands

```
$ php artisan key:generate
$ php artisan migrate
$ php artisan db:seed
```

## Deploy

Deploy application with artisan:

```
$ php artisan serve
```

## The four api endpoints for the task

Add accept in header to get json error responses

```
Accept application/json
```

The four endpoints for the task

1. Get Parking free spaces by parking lot id

```
/api/v1/parking_lots/{parking_lot}/parking_lot_spaces
```

If the response is 200 you wiil get parking lot data that has free spaces in data.

```
"success": "1",
"data": {
    "id": 1,
    "space": 150,
    "free_spaces": 126,
    "vehicles": 6
}
```

2. Get vechicle current exit price info by registration plate if the car is in the parking lot.

```
/api/v1/parking_lots/{parking_lot}/vehicles/{vehicle}/exit_parking
```

current price info example

```
request:
-----------------------------------
"registration_plate": "СВ0077MM"
-----------------------------------
response
-----------------------------------
"success": 1,
"massage": "Current vehicle info",
"data": {
    "id": 2,
    "registration_plate": "СВ0077MM",
    "daily_hours": "69:42:59",
    "night_hours": "92:00:00",
    "currency": "BGN",
    "discount": "10.00",
    "without_discount": "393.15",
    "total_price": "353.83"
}
-----------------------------------
```

current exit = total_price

3. Create/add vehicle to the parking.

```
api/v1/parking_lots/{parking_lot}/vehicles
```

Adding vehicle example

With parking lot id in the url.

```
request
-----------------------------------
"brand": "Toyota",
"registration_plate": "СВ9987МР",
"vehicle_type_id": 1,
"card_id": 3
-----------------------------------
response
-----------------------------------
"success": 1,
"massage": "Vehicle created succesfully",
"data": {
    "id": 3,
    "brand": "Toyota",
    "registration_plate": "СВ9987МР",
    "parking_lot_id": 1,
    "vehicle_type": {
        "id": 1,
        "name": "Лек автомобил/мотор",
        "category": "A",
        "parking_lot_id": 1,
        "daily_rate": "3.0000",
        "night_rate": "2.0000",
        "parking_space": 1
    },
    "card": null,
    "entered_at": {
        "date": "2021-07-07 13:47:47.386844",
        "timezone_type": 3,
        "timezone": "UTC"
    },
    "exited_at": null,
    "in_parking": true
}
-----------------------------------
```

4. Vehicle exiting the parking lot.

```
 /api/v1/parking_lots/{parking_lot}/vehicles/{vehicle}/exit_parking
```

exiting example

```
request
-----------------------------------
// By url with parking lot and vehicle ids
.../api/v1/parking_lots/1/vehicles/2/exit_parking
-----------------------------------

response
-----------------------------------
"success": 1,
"massage": "Vehicle exited the parking",
"data": {
    "id": 1,
    "registration_plate": "СО3456ВР",
    "entered_at": "2021-07-07 12:37:17",
    "exited_at": "2021-07-07 18:09:26",
    "daily_hours": "05:22:43",
    "night_hours": "00:09:26",
    "currency": "BGN",
    "discount": "10.00",
    "without_discount": "32.90",
    "total_price": "29.61"
}
-----------------------------------
```

total_price is the finale amount to pay.

## Other endpoints

ParkingLot, Card, VehicleType and Vehicle

for show(GET), update(PUT) and destroy(DELETE) mothods

```
//parking lot
.../api/v1/parking_lots/{parking_lot}
//other
//lowercase modal name in plural vehicles, cards, vehicle_types
.../api/v1/parking_lots/{parking_lot}/{{modal_name}}/{vehicle}
```

for index(GET) and store(POST)

```
//parking lot
.../api/v1/parking_lots/
//other
//lowercase modal name in plural vehicles, cards, vehicle_types
.../api/v1/parking_lots/{parking_lot}/{{modal_name}}/
```
